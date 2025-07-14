// JavaScript for Startup-Investor Matching System
class MatchingSystem {
    constructor() {
        this.init();
        this.bindEvents();
        this.setupRealTimeUpdates();
    }

    init() {
        // Initialize tooltips and popovers
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize any existing match cards
        this.enhanceMatchCards();
        
        // Setup notification counter updates
        this.updateNotificationCounts();
    }

    bindEvents() {
        // Bind interest expression buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-express-interest')) {
                this.handleInterestExpression(e);
            }
            
            if (e.target.closest('.btn-find-matches')) {
                this.handleFindMatches(e);
            }
            
            if (e.target.closest('.btn-bulk-action')) {
                this.handleBulkAction(e);
            }
        });

        // Handle match filtering
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('match-filter')) {
                this.filterMatches();
            }
        });

        // Handle search input with debouncing
        const searchInput = document.querySelector('#match-search');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.searchMatches(e.target.value);
                }, 300);
            });
        }
    }

    setupRealTimeUpdates() {
        // Poll for new matches every 30 seconds
        setInterval(() => {
            this.checkForNewMatches();
        }, 30000);

        // Check for new messages every 15 seconds
        setInterval(() => {
            this.updateMessageCounts();
        }, 15000);
    }

    // Core Matching Functions
    async expressInterest(matchId, interested, button = null) {
        if (button) {
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            button.disabled = true;
        }

        try {
            const response = await fetch('/api/match/interest', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    match_id: matchId,
                    interested: interested,
                    _token: this.getCSRFToken()
                })
            });

            const data = await response.json();
            
            if (data.success) {
                if (data.mutual_interest) {
                    this.showMutualInterestModal(matchId, data);
                } else {
                    this.showToast(
                        interested ? 'Interest recorded successfully!' : 'Match declined',
                        interested ? 'success' : 'info'
                    );
                }
                
                // Update the UI
                this.updateMatchCard(matchId, data);
                this.updateNotificationCounts();
                
                // Refresh the page after a short delay to show updated status
                setTimeout(() => {
                    if (data.mutual_interest) {
                        window.location.href = `/matches/mutual`;
                    } else {
                        location.reload();
                    }
                }, 2000);
                
            } else {
                throw new Error(data.message || 'Failed to process interest');
            }
            
        } catch (error) {
            console.error('Error expressing interest:', error);
            this.showToast('An error occurred. Please try again.', 'error');
        } finally {
            if (button) {
                button.disabled = false;
                if (!button.closest('.match-card').classList.contains('processing')) {
                    button.innerHTML = button.originalText || 'Try Again';
                }
            }
        }
    }

    async findNewMatches(button = null) {
        if (button) {
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Finding Matches...';
            button.disabled = true;
        }

        try {
            const response = await fetch('/api/match/find', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    _token: this.getCSRFToken()
                })
            });

            const data = await response.json();
            
            if (data.success) {
                const matchCount = data.matches_created || 0;
                this.showToast(
                    `Found ${matchCount} new matches!`, 
                    'success'
                );
                
                // Update counts and refresh after delay
                this.updateNotificationCounts();
                setTimeout(() => {
                    location.reload();
                }, 1500);
                
            } else {
                throw new Error(data.message || 'Failed to find matches');
            }
            
        } catch (error) {
            console.error('Error finding matches:', error);
            this.showToast('An error occurred while finding matches.', 'error');
        } finally {
            if (button) {
                button.innerHTML = originalText || '<i class="fas fa-sync me-2"></i>Find New Matches';
                button.disabled = false;
            }
        }
    }

    // UI Enhancement Functions
    enhanceMatchCards() {
        const matchCards = document.querySelectorAll('.match-card');
        
        matchCards.forEach(card => {
            // Add hover effects
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-2px)';
                card.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '';
            });

            // Add quick view functionality
            const quickViewBtn = card.querySelector('.btn-quick-view');
            if (quickViewBtn) {
                quickViewBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.showQuickView(card.dataset.matchId);
                });
            }

            // Add bookmark functionality
            const bookmarkBtn = card.querySelector('.btn-bookmark');
            if (bookmarkBtn) {
                bookmarkBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleBookmark(card.dataset.matchId, bookmarkBtn);
                });
            }
        });
    }

    filterMatches() {
        const filters = {
            status: document.querySelector('#filter-status')?.value || '',
            score: document.querySelector('#filter-score')?.value || '',
            industry: document.querySelector('#filter-industry')?.value || '',
            location: document.querySelector('#filter-location')?.value || ''
        };

        const matchCards = document.querySelectorAll('.match-card');
        
        matchCards.forEach(card => {
            let show = true;
            
            // Status filter
            if (filters.status && !card.classList.contains(`status-${filters.status}`)) {
                show = false;
            }
            
            // Score filter
            if (filters.score) {
                const scoreElement = card.querySelector('.match-score');
                if (scoreElement) {
                    const score = parseInt(scoreElement.textContent);
                    const [minScore, maxScore] = filters.score.split('-').map(Number);
                    if (score < minScore || (maxScore && score > maxScore)) {
                        show = false;
                    }
                }
            }
            
            // Industry filter
            if (filters.industry) {
                const industryElement = card.querySelector('.match-industry');
                if (!industryElement || !industryElement.textContent.includes(filters.industry)) {
                    show = false;
                }
            }
            
            // Location filter
            if (filters.location) {
                const locationElement = card.querySelector('.match-location');
                if (!locationElement || !locationElement.textContent.toLowerCase().includes(filters.location.toLowerCase())) {
                    show = false;
                }
            }
            
            card.style.display = show ? 'block' : 'none';
        });

        // Update results count
        const visibleCount = document.querySelectorAll('.match-card[style*="block"], .match-card:not([style*="none"])').length;
        const countElement = document.querySelector('#results-count');
        if (countElement) {
            countElement.textContent = visibleCount;
        }
    }

    searchMatches(query) {
        const matchCards = document.querySelectorAll('.match-card');
        const searchTerms = query.toLowerCase().split(' ');
        
        matchCards.forEach(card => {
            const searchableText = [
                card.querySelector('.match-name')?.textContent || '',
                card.querySelector('.match-company')?.textContent || '',
                card.querySelector('.match-description')?.textContent || '',
                card.querySelector('.match-industry')?.textContent || ''
            ].join(' ').toLowerCase();
            
            const matches = searchTerms.every(term => searchableText.includes(term));
            card.style.display = matches ? 'block' : 'none';
        });
    }

    // Event Handlers
    handleInterestExpression(e) {
        e.preventDefault();
        const button = e.target.closest('.btn-express-interest');
        const matchId = button.dataset.matchId;
        const interested = button.dataset.interested === 'true';
        
        // Store original text for restoration
        button.originalText = button.innerHTML;
        
        this.expressInterest(matchId, interested, button);
    }

    handleFindMatches(e) {
        e.preventDefault();
        const button = e.target.closest('.btn-find-matches');
        this.findNewMatches(button);
    }

    handleBulkAction(e) {
        e.preventDefault();
        const button = e.target.closest('.btn-bulk-action');
        const action = button.dataset.action;
        const selectedMatches = this.getSelectedMatches();
        
        if (selectedMatches.length === 0) {
            this.showToast('Please select matches first', 'warning');
            return;
        }
        
        this.performBulkAction(action, selectedMatches);
    }

    // Advanced Features
    async checkForNewMatches() {
        try {
            const response = await fetch('/api/match/count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.new_matches > 0) {
                this.showNewMatchNotification(data.new_matches);
            }
            
        } catch (error) {
            console.error('Error checking for new matches:', error);
        }
    }

    async updateMessageCounts() {
        try {
            const response = await fetch('/api/messages/unread-count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            const messageCountElement = document.querySelector('#unread-message-count');
            if (messageCountElement && data.unread_count > 0) {
                messageCountElement.textContent = data.unread_count;
                messageCountElement.style.display = 'inline';
            }
            
        } catch (error) {
            console.error('Error updating message counts:', error);
        }
    }

    updateNotificationCounts() {
        // Update various notification badges and counters
        fetch('/api/notifications/counts', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update match counts
            const elements = {
                'total-matches': data.total_matches || 0,
                'mutual-matches': data.mutual_matches || 0,
                'pending-matches': data.pending_matches || 0,
                'new-matches': data.new_matches || 0
            };
            
            Object.entries(elements).forEach(([id, count]) => {
                const element = document.querySelector(`#${id}`);
                if (element) {
                    element.textContent = count;
                }
            });
        })
        .catch(error => console.error('Error updating counts:', error));
    }

    // Modal and UI Functions
    showMutualInterestModal(matchId, data) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-heart me-2"></i>Mutual Interest Achieved!
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-handshake fa-4x text-success mb-3"></i>
                            <h4>Congratulations!</h4>
                            <p class="lead">You now have mutual interest with this ${data.partner_type}.</p>
                            <p>Both parties have expressed interest in working together. Time to start the conversation!</p>
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/messages/conversation/${matchId}" class="btn btn-success btn-lg">
                                <i class="fas fa-comments me-2"></i>Start Conversation
                            </a>
                            <a href="/matches/view/${matchId}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
        
        // Remove modal from DOM when hidden
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    showQuickView(matchId) {
        // Show a quick preview modal with key match information
        fetch(`/api/match/preview/${matchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.renderQuickViewModal(data.match);
                }
            })
            .catch(error => {
                console.error('Error loading quick view:', error);
                this.showToast('Error loading match details', 'error');
            });
    }

    renderQuickViewModal(match) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quick View</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" 
                                         style="width: 80px; height: 80px; font-size: 1.5rem;">
                                        ${match.name.charAt(0)}
                                    </div>
                                </div>
                                <h5>${match.name}</h5>
                                <p class="text-muted">${match.company}</p>
                                <span class="badge bg-primary">${match.score}% Match</span>
                            </div>
                            <div class="col-md-8">
                                <h6>Match Reasons:</h6>
                                <ul class="list-unstyled">
                                    ${match.reasons.map(reason => `<li><i class="fas fa-check text-success me-2"></i>${reason}</li>`).join('')}
                                </ul>
                                <hr>
                                <p>${match.description.substring(0, 200)}...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="/matches/view/${match.id}" class="btn btn-primary">View Full Details</a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
        
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    showNewMatchNotification(count) {
        // Create a non-intrusive notification for new matches
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-heart me-2"></i>
            <strong>New Matches!</strong> You have ${count} new ${count === 1 ? 'match' : 'matches'}.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        
        toast.innerHTML = `
            <i class="fas fa-${icons[type] || icons.info} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Utility Functions
    getCSRFToken() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta) {
            return tokenMeta.getAttribute('content');
        }
        
        // Fallback: try to get from a hidden input
        const tokenInput = document.querySelector('input[name="_token"]');
        return tokenInput ? tokenInput.value : '';
    }

    updateMatchCard(matchId, data) {
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        if (card) {
            // Update status badges and buttons based on the new state
            if (data.status === 'mutual_interest') {
                card.classList.add('mutual-interest');
                const statusBadge = card.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = 'Mutual Interest';
                    statusBadge.className = 'badge bg-success status-badge';
                }
            }
        }
    }

    getSelectedMatches() {
        const checkboxes = document.querySelectorAll('.match-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    async performBulkAction(action, matchIds) {
        try {
            const response = await fetch('/api/match/bulk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    match_ids: matchIds,
                    _token: this.getCSRFToken()
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast(`Bulk action completed for ${data.processed} matches`, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message);
            }
            
        } catch (error) {
            console.error('Error performing bulk action:', error);
            this.showToast('Error performing bulk action', 'error');
        }
    }
}

// Initialize the matching system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.matchingSystem = new MatchingSystem();
});

// Global functions for backward compatibility and template usage
function expressInterest(matchId, interested) {
    if (window.matchingSystem) {
        window.matchingSystem.expressInterest(matchId, interested);
    }
}

function generateMatches() {
    if (window.matchingSystem) {
        window.matchingSystem.findNewMatches();
    }
}

function filterMatches(filter) {
    if (window.matchingSystem) {
        window.matchingSystem.filterMatches();
    }
}
/**
 * Enhanced Matches Page JavaScript
 * Handles interactions for startup and investor matches pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize matches functionality
    initializeMatchesPage();
    
    // Initialize match interactions
    initializeMatchActions();
    
    // Initialize tabs if present
    initializeMatchTabs();
});

function initializeMatchesPage() {
    // Add loading states to action buttons
    const actionButtons = document.querySelectorAll('[data-match-action]');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.matchAction;
            if (action === 'generate') {
                handleGenerateMatches(this);
            } else if (action === 'interest') {
                const matchId = this.dataset.matchId;
                const interested = this.dataset.interested === 'true';
                handleExpressInterest(this, matchId, interested);
            }
        });
    });
}

function initializeMatchActions() {
    // Initialize match cards hover effects and interactions
    const matchCards = document.querySelectorAll('.enhanced-match-card');
    
    matchCards.forEach(card => {
        // Add keyboard navigation
        card.setAttribute('tabindex', '0');
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const viewBtn = card.querySelector('.btn-match-view');
                if (viewBtn) {
                    viewBtn.click();
                }
            }
        });
        
        // Add click-to-expand functionality for descriptions
        const description = card.querySelector('.card-description');
        if (description && description.textContent.includes('...')) {
            description.style.cursor = 'pointer';
            description.title = 'Click to read full description';
            
            description.addEventListener('click', function() {
                showToast('Full description would be shown here', 'info');
            });
        }
    });
}

function initializeMatchTabs() {
    // Enhanced tab switching with animation
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const targetPane = document.querySelector(e.target.dataset.bsTarget);
            if (targetPane) {
                animateMatchCards(targetPane);
            }
        });
    });
    
    // Animate initial visible cards
    const activePane = document.querySelector('.tab-pane.show.active');
    if (activePane) {
        setTimeout(() => animateMatchCards(activePane), 100);
    }
}

function animateMatchCards(container) {
    const cards = container.querySelectorAll('.enhanced-match-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function handleGenerateMatches(button) {
    const originalContent = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Finding Matches...';
    button.disabled = true;
    
    // Prepare request data
    const formData = new FormData();
    formData.append('_token', getCSRFToken());
    
    // Make API request
    fetch(getBaseUrl() + 'api/match/find', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const matchCount = data.matches_created || 0;
            const message = matchCount > 0 
                ? `Great! Found ${matchCount} new matches. Refreshing page...`
                : 'No new matches found at this time. Try updating your profile for better matches.';
            
            showToast(message, matchCount > 0 ? 'success' : 'info');
            
            if (matchCount > 0) {
                setTimeout(() => location.reload(), 2000);
            }
        } else {
            showToast('Error: ' + (data.message || 'Failed to find matches'), 'error');
        }
    })
    .catch(error => {
        console.error('Generate matches error:', error);
        showToast('Network error occurred. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

function handleExpressInterest(button, matchId, interested) {
    const confirmMessage = interested 
        ? 'Express interest in connecting with this match?' 
        : 'Pass on this match? This action cannot be undone.';
    
    if (confirm(confirmMessage)) {
        const originalContent = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
        button.disabled = true;
        
        // Prepare request data
        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('interested', interested);
        formData.append('_token', getCSRFToken());
        
        // Make API request
        fetch(getBaseUrl() + 'api/match/interest', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.mutual_interest) {
                    showToast('🎉 Mutual interest! You can now start a conversation.', 'success');
                } else {
                    const message = interested 
                        ? 'Interest expressed! We\'ll notify you if there\'s mutual interest.'
                        : 'Match passed. This has been removed from your list.';
                    showToast(message, interested ? 'success' : 'info');
                }
                
                // Reload page after short delay to show updated state
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast('Error: ' + (data.message || 'Action failed'), 'error');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Express interest error:', error);
            showToast('Network error occurred. Please try again.', 'error');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}

// Match view functions
function viewMatch(matchId) {
    window.location.href = getBaseUrl() + 'matches/view/' + matchId;
}

function startConversation(matchId) {
    window.location.href = getBaseUrl() + 'messages/conversation/' + matchId;
}

function refreshMatches() {
    const refreshBtn = event.target;
    const originalContent = refreshBtn.innerHTML;
    
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Reload the page after a short delay for visual feedback
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Enhanced match statistics with animations
function animateMatchStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const target = parseInt(stat.textContent);
        let current = 0;
        const increment = target / 20;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(current);
        }, 50);
    });
}

// Match filtering and sorting
function filterMatches(filterType) {
    const matchCards = document.querySelectorAll('.enhanced-match-card');
    
    matchCards.forEach(card => {
        let shouldShow = true;
        
        switch (filterType) {
            case 'high-score':
                const scoreElement = card.querySelector('.match-score-badge');
                const score = scoreElement ? parseInt(scoreElement.textContent) : 0;
                shouldShow = score >= 80;
                break;
            case 'mutual':
                shouldShow = card.classList.contains('match-mutual');
                break;
            case 'pending':
                shouldShow = card.classList.contains('match-pending');
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        if (shouldShow) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Utility functions
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        return metaTag.getAttribute('content');
    }
    
    // Fallback: try to get from session (if available in global scope)
    return window.csrfToken || document.querySelector('input[name="_token"]')?.value || '';
}



// Run debug on page load
document.addEventListener('DOMContentLoaded', debugMatchesPage);

function getBaseUrl() {
    // Get base URL from current location
    const path = window.location.pathname;
    const parts = path.split('/');
    const baseIndex = parts.indexOf('startup');
    
    if (baseIndex !== -1) {
        return window.location.origin + parts.slice(0, baseIndex + 1).join('/') + '/';
    }
    
    return window.location.origin + '/startup/';
}

// Enhanced loading states
function showLoadingState(element, loadingText = 'Loading...', icon = 'fa-spinner fa-spin') {
    const originalContent = element.innerHTML;
    element.innerHTML = `<i class="fas ${icon} me-2"></i>${loadingText}`;
    element.disabled = true;
    
    return function() {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

// Match recommendation improvements
function improveMatches() {
    showToast('Tip: Complete your profile and be specific about your preferences for better matches!', 'info');
    setTimeout(() => {
        window.location.href = getBaseUrl() + 'profile/edit';
    }, 3000);
}

// Advanced match analytics
function trackMatchInteraction(matchId, action) {
    // Track user interactions for analytics
    const data = {
        match_id: matchId,
        action: action,
        timestamp: new Date().toISOString(),
        user_agent: navigator.userAgent
    };
    
    // Send analytics data (non-blocking)
    fetch(getBaseUrl() + 'api/analytics/match-interaction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    }).catch(() => {
        // Silently fail analytics
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats on load
    if (document.querySelectorAll('.stat-number').length > 0) {
        setTimeout(animateMatchStats, 500);
    }
    
    // Initialize tooltips for match cards
    const matchCards = document.querySelectorAll('.enhanced-match-card');
    matchCards.forEach(card => {
        const description = card.querySelector('.card-description');
        if (description && description.textContent.includes('...')) {
            description.title = 'Click to read full description';
        }
    });
});

// Global utilities for matches
window.MatchesUtils = {
    viewMatch: viewMatch,
    startConversation: startConversation,
    refreshMatches: refreshMatches,
    filterMatches: filterMatches,
    improveMatches: improveMatches,
    handleGenerateMatches: handleGenerateMatches,
    handleExpressInterest: handleExpressInterest
};

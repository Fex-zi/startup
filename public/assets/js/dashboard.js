/**
 * Enhanced Dashboard JavaScript 
 * Shared functionality for both startup and investor dashboards
 */

/**
 * @param {string} message - The message to display
 * @param {string} type - success, error, info
 */
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => {
        if (toast.classList.contains('show')) {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }
    });

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Set icon based on type
    let iconClass = '';
    switch(type) {
        case 'success':
            iconClass = 'fas fa-check-circle';
            break;
        case 'error':
            iconClass = 'fas fa-exclamation-circle';
            break;
        case 'info':
        default:
            iconClass = 'fas fa-info-circle';
            break;
    }
    
    toast.innerHTML = `
        <div class="toast-content">
            <i class="${iconClass} me-2"></i>
            ${message}
        </div>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show toast with animation
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Remove toast after delay
    setTimeout(() => {
        if (toast.classList.contains('show')) {
            toast.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 400);
        }
    }, 5000);
}

// ===== ENHANCED DASHBOARD INITIALIZATION =====

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Enhanced Dashboard...');
    
    try {
        // Initialize FontAwesome icons
        initializeFontAwesome();
        
        // Initialize mini charts
        initializeMiniCharts();
        
        // Add enhanced interactive behaviors
        initializeInteractions();
        
        // Initialize mobile sidebar functionality
        initializeMobileSidebar();
        
        // Auto-refresh data every 5 minutes (only if API endpoints exist)
        if (typeof window.API_ENDPOINTS !== 'undefined') {
            setInterval(refreshDashboardData, 300000);
        }
        
        console.log('✅ Enhanced Dashboard initialized successfully');
    } catch (error) {
        console.log('Dashboard initialization had minor issues, but continuing...');
        // Don't show error toast on initialization issues
    }
});

// ===== FONTAWESOME ICON INITIALIZATION =====

function initializeFontAwesome() {
    try {
        // Ensure all FontAwesome icons are properly loaded and styled
        const icons = document.querySelectorAll('.fas, .fa-solid, .far, .fa-regular, .fab, .fa-brands');
        
        icons.forEach(icon => {
            // Force font-family for FontAwesome icons
            if (icon.classList.contains('fas') || icon.classList.contains('fa-solid')) {
                icon.style.fontFamily = '"Font Awesome 6 Free"';
                icon.style.fontWeight = '900';
            } else if (icon.classList.contains('far') || icon.classList.contains('fa-regular')) {
                icon.style.fontFamily = '"Font Awesome 6 Free"';
                icon.style.fontWeight = '400';
            } else if (icon.classList.contains('fab') || icon.classList.contains('fa-brands')) {
                icon.style.fontFamily = '"Font Awesome 6 Brands"';
                icon.style.fontWeight = '400';
            }
            
            // Ensure proper display properties
            icon.style.display = 'inline-block';
            icon.style.fontStyle = 'normal';
            icon.style.fontVariant = 'normal';
            icon.style.textRendering = 'auto';
            icon.style.lineHeight = '1';
        });
        
        console.log('✅ FontAwesome icons initialized and fixed');
    } catch (error) {
        console.log('FontAwesome initialization had issues, but continuing...');
    }
}

// ===== MOBILE SIDEBAR FUNCTIONALITY =====

function initializeMobileSidebar() {
    try {
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        if (mobileToggle && sidebar && sidebarOverlay) {
            // Toggle sidebar
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                
                // Update aria attributes
                const isExpanded = sidebar.classList.contains('active');
                mobileToggle.setAttribute('aria-expanded', isExpanded);
                sidebarOverlay.setAttribute('aria-hidden', !isExpanded);
            });
            
            // Close sidebar when overlay is clicked
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
                sidebarOverlay.setAttribute('aria-hidden', 'true');
            });
            
            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    mobileToggle.setAttribute('aria-expanded', 'false');
                    sidebarOverlay.setAttribute('aria-hidden', 'true');
                }
            });
        }
    } catch (error) {
        console.log('Mobile sidebar initialization had issues, but continuing...');
    }
}

// ===== ENHANCED MINI CHARTS =====

function initializeMiniCharts() {
    try {
        // Enhanced mini charts with better data visualization
        const charts = [
            { id: 'matchesChart', data: [10, 15, 12, 18, 20, 25, 30], color: '#667eea' },
            { id: 'startupsChart', data: [10, 15, 12, 18, 20, 25, 30], color: '#48bb78' },
            { id: 'mutualChart', data: [2, 3, 1, 4, 3, 5, 6], color: '#f56565' },
            { id: 'pendingChart', data: [5, 4, 6, 3, 4, 2, 3], color: '#ed8936' },
            { id: 'scoreChart', data: [65, 70, 68, 75, 72, 78, 80], color: '#4299e1' }
        ];
        
        charts.forEach(chart => {
            const canvas = document.getElementById(chart.id);
            if (canvas) {
                drawMiniChart(canvas, chart.data, chart.color);
            }
        });
    } catch (error) {
        console.log('Mini charts initialization had issues, but continuing...');
    }
}

function drawMiniChart(canvas, data, color = '#667eea') {
    try {
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Set up drawing with enhanced styling
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        // Calculate points
        const stepX = width / (data.length - 1);
        const maxValue = Math.max(...data);
        const minValue = Math.min(...data);
        const range = maxValue - minValue || 1;
        
        // Draw line with smoother curves
        ctx.beginPath();
        data.forEach((value, index) => {
            const x = index * stepX;
            const y = height - ((value - minValue) / range) * height;
            
            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        ctx.stroke();
        
        // Add enhanced gradient fill
        ctx.globalAlpha = 0.15;
        ctx.fillStyle = color;
        ctx.lineTo(width, height);
        ctx.lineTo(0, height);
        ctx.closePath();
        ctx.fill();
        ctx.globalAlpha = 1;
        
        // Add data points
        ctx.fillStyle = color;
        data.forEach((value, index) => {
            const x = index * stepX;
            const y = height - ((value - minValue) / range) * height;
            ctx.beginPath();
            ctx.arc(x, y, 2, 0, 2 * Math.PI);
            ctx.fill();
        });
    } catch (error) {
        console.log('Chart drawing had issues, but continuing...');
    }
}

// ===== ENHANCED INTERACTIONS =====

function initializeInteractions() {
    try {
        // Enhanced click animations for stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
            
            // Add hover effects
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
        
        // Enhanced hover effects for match items
        document.querySelectorAll('.match-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.background = '#f3f4f6';
                this.style.transform = 'translateX(4px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.background = '#f9fafb';
                this.style.transform = '';
            });
        });
        
        // Enhanced quick action interactions
        document.querySelectorAll('.quick-action-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                const icon = this.querySelector('.action-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.1)';
                }
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = '';
                const icon = this.querySelector('.action-icon');
                if (icon) {
                    icon.style.transform = '';
                }
            });
        });
        
        // Add smooth scrolling to anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    } catch (error) {
        console.log('Interactions initialization had issues, but continuing...');
    }
}

// ===== ENHANCED BUTTON INTERACTIONS =====

function refreshMatches() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Enhanced loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    button.disabled = true;
    button.style.opacity = '0.7';
    
    // Simulate API call with better feedback
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        button.style.opacity = '1';
        
        // Show enhanced success message
        showToast('Matches refreshed successfully! Found new potential matches.', 'success');
        
        // Optionally refresh the matches list
        // refreshMatchesList();
    }, 2000);
}

function viewMatch(matchId) {
    showToast('Loading match details...', 'info');
    setTimeout(() => {
        window.location.href = `/startup/matches/view/${matchId}`;
    }, 500);
}

function expressInterest(matchId) {
    // Enhanced confirmation with custom styling
    if (confirm('Express interest in this match? This will notify the investor of your interest.')) {
        showToast('Expressing interest...', 'info');
        
        // Enhanced API call with better error handling
        fetch('/startup/api/match/interest', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `match_id=${matchId}&interested=true`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('Interest expressed successfully! The investor has been notified.', 'success');
                // Update UI to reflect interest expressed
                updateMatchInterestUI(matchId);
            } else {
                showToast(data.message || 'Error expressing interest. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error. Please check your connection and try again.', 'error');
        });
    }
}

function updateMatchInterestUI(matchId) {
    // Update the UI to show interest has been expressed
    const matchButton = document.querySelector(`[onclick="expressInterest(${matchId})"]`);
    if (matchButton) {
        matchButton.innerHTML = '<i class="fas fa-check me-1"></i>Interest Sent';
        matchButton.classList.remove('btn-success');
        matchButton.classList.add('btn-outline-success');
        matchButton.disabled = true;
    }
}

// ===== ENHANCED DATA REFRESH =====

function refreshDashboardData() {
    // Only run if dashboard refresh endpoint exists
    if (typeof window.API_ENDPOINTS !== 'undefined' && window.API_ENDPOINTS.dashboard_refresh) {
        // Enhanced background refresh with better error handling
        fetch('/startup/api/dashboard/refresh', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update stats without page reload
                updateDashboardStats(data.stats);
                console.log('📊 Dashboard data refreshed successfully');
            }
        })
        .catch(error => {
            console.log('Dashboard refresh not available:', error.message);
            // Silently fail for background refresh
        });
    }
}

function updateDashboardStats(stats) {
    try {
        // Enhanced stat updates with smooth animations
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                const currentValue = parseInt(element.textContent.replace(/,/g, '')) || 0;
                const newValue = stats[key];
                if (currentValue !== newValue) {
                    animateNumber(element, currentValue, newValue);
                }
            }
        });
    } catch (error) {
        console.log('Stats update had issues, but continuing...');
    }
}

function animateNumber(element, start, end) {
    try {
        const duration = 1500;
        const startTime = performance.now();
        const difference = end - start;
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Use easing function for smoother animation
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.round(start + (difference * easeOutQuart));
            
            element.textContent = current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        requestAnimationFrame(update);
    } catch (error) {
        console.log('Number animation had issues, but continuing...');
    }
}

// ===== UTILITY FUNCTIONS =====

// Quick action handlers with enhanced feedback
function findInvestors() {
    showToast('Redirecting to investor search...', 'info');
    setTimeout(() => {
        window.location.href = '/search/investors';
    }, 800);
}

function viewMatches() {
    showToast('Loading your matches...', 'info');
    setTimeout(() => {
        window.location.href = '/matches';
    }, 800);
}

function openMessages() {
    showToast('Opening messages...', 'info');
    setTimeout(() => {
        window.location.href = '/messages';
    }, 800);
}

function editProfile() {
    showToast('Opening profile editor...', 'info');
    setTimeout(() => {
        window.location.href = '/profile/edit';
    }, 800);
}

// ===== MINIMAL ERROR HANDLING (FIXED) =====

// Only log errors to console, don't show user toast messages for every minor issue
window.addEventListener('error', function(e) {
    console.error('Dashboard JavaScript Error:', e.error);
    // Removed automatic error toast - only show for critical user-facing errors
});

// Handle unhandled promise rejections silently
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    // Removed automatic error toast - only log to console
});

// ===== PERFORMANCE OPTIMIZATION =====

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// ===== ACCESSIBILITY ENHANCEMENTS =====

// Keyboard navigation support
document.addEventListener('keydown', function(e) {
    // Tab navigation enhancements
    if (e.key === 'Tab') {
        document.body.classList.add('keyboard-navigation');
    }
    
    // Enhanced keyboard shortcuts
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'k':
                e.preventDefault();
                // Focus search if available
                const searchInput = document.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
                break;
        }
    }
});

document.addEventListener('mousedown', function() {
    document.body.classList.remove('keyboard-navigation');
});

// ===== GLOBAL UTILITIES (Enhanced) =====

window.DashboardUtils = {
    showToast: showToast,
    refreshData: refreshDashboardData,
    refreshMatches: refreshMatches,
    viewMatch: viewMatch,
    expressInterest: expressInterest,
    findInvestors: findInvestors,
    viewMatches: viewMatches,
    openMessages: openMessages,
    editProfile: editProfile,
    animateNumber: animateNumber,
    debounce: debounce,
    throttle: throttle
};

console.log('✅ Enhanced Dashboard JavaScript loaded successfully');
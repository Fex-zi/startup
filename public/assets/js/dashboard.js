/**
 * Enhanced Dashboard JavaScript
 * Shared functionality for both startup and investor dashboards
 */

// Enhanced Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize mini charts
    initializeMiniCharts();
    
    // Add interactive behaviors
    initializeInteractions();
    
    // Auto-refresh data every 5 minutes
    setInterval(refreshDashboardData, 300000);
});

function initializeMiniCharts() {
    // Simple canvas-based mini charts
    const charts = [
        { id: 'matchesChart', data: [10, 15, 12, 18, 20, 25, 30] },
        { id: 'startupsChart', data: [10, 15, 12, 18, 20, 25, 30] },
        { id: 'mutualChart', data: [2, 3, 1, 4, 3, 5, 6] },
        { id: 'pendingChart', data: [5, 4, 6, 3, 4, 2, 3] },
        { id: 'scoreChart', data: [65, 70, 68, 75, 72, 78, 80] }
    ];
    
    charts.forEach(chart => {
        const canvas = document.getElementById(chart.id);
        if (canvas) {
            drawMiniChart(canvas, chart.data);
        }
    });
}

function drawMiniChart(canvas, data) {
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Set up drawing
    ctx.strokeStyle = '#667eea';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    // Calculate points
    const stepX = width / (data.length - 1);
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const range = maxValue - minValue || 1;
    
    // Draw line
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
    
    // Add gradient fill
    ctx.globalAlpha = 0.1;
    ctx.fillStyle = '#667eea';
    ctx.lineTo(width, height);
    ctx.lineTo(0, height);
    ctx.closePath();
    ctx.fill();
    ctx.globalAlpha = 1;
}

function initializeInteractions() {
    // Add click animations to stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Add hover effects to match items
    document.querySelectorAll('.match-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.background = '#f3f4f6';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '#f9fafb';
        });
    });
}

function refreshMatches() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show success message
        showToast('Matches refreshed successfully!', 'success');
    }, 2000);
}

function viewMatch(matchId) {
    window.location.href = `/startup/matches/view/${matchId}`;
}

function expressInterest(matchId) {
    if (confirm('Express interest in this match?')) {
        // Simulate API call
        fetch('/startup/api/match/interest', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `match_id=${matchId}&interested=true`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Interest expressed successfully!', 'success');
            } else {
                showToast('Error expressing interest. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error. Please check your connection.', 'error');
        });
    }
}

function refreshDashboardData() {
    // Silently refresh dashboard data in background
    fetch('/startup/api/dashboard/refresh')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats without page reload
                updateDashboardStats(data.stats);
            }
        })
        .catch(error => {
            console.error('Dashboard refresh error:', error);
        });
}

function updateDashboardStats(stats) {
    // Update stat numbers with animation
    Object.keys(stats).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            animateNumber(element, parseInt(element.textContent), stats[key]);
        }
    });
}

function animateNumber(element, start, end) {
    const duration = 1000;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.round(start + (end - start) * progress);
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
        </div>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Global utilities
window.DashboardUtils = {
    showToast: showToast,
    refreshData: refreshDashboardData,
    refreshMatches: refreshMatches,
    viewMatch: viewMatch,
    expressInterest: expressInterest
};

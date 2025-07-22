/**
 * Enhanced Search Page JavaScript
 * Handles interactions for Find Investors and Find Startups pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearchPage();
    
    // Initialize filter management
    initializeFilters();
    
    // Initialize card interactions
    initializeCardInteractions();
});

function initializeSearchPage() {
    // Add loading states to search forms
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('.search-btn');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
                submitBtn.disabled = true;
            }
        });
    });
    
    // Auto-submit form on filter changes (with debouncing)
    const filterInputs = document.querySelectorAll('.auto-filter');
    let filterTimeout;
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                const form = input.closest('form');
                if (form) {
                    form.submit();
                }
            }, 500);
        });
    });
}

function initializeFilters() {
    // Show active filters as tags
    displayActiveFilters();
    
    // Handle filter removal
    document.querySelectorAll('.remove-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            const filterName = this.dataset.filter;
            const filterValue = this.dataset.value;
            removeFilter(filterName, filterValue);
        });
    });
    
    // Clear all filters
    const clearAllBtn = document.querySelector('.clear-all-filters');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            clearAllFilters();
        });
    }
}

function displayActiveFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    const filterContainer = document.querySelector('.filter-tags');
    
    if (!filterContainer) return;
    
    const filters = [];
    
    // Check for active filters
    const filterMappings = {
        'search': 'Search',
        'industry': 'Industry',
        'investor_type': 'Investor Type',
        'stage': 'Stage',
        'location': 'Location',
        'funding_type': 'Funding Type'
    };
    
    Object.entries(filterMappings).forEach(([key, label]) => {
        const value = urlParams.get(key);
        if (value && value.trim() !== '') {
            filters.push({
                key: key,
                label: label,
                value: value,
                displayValue: formatFilterValue(key, value)
            });
        }
    });
    
    // Display filter tags
    if (filters.length > 0) {
        filterContainer.innerHTML = filters.map(filter => `
            <div class="filter-tag">
                ${filter.label}: ${filter.displayValue}
                <span class="remove-filter" data-filter="${filter.key}" data-value="${filter.value}">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        `).join('');
        filterContainer.style.display = 'flex';
    } else {
        filterContainer.style.display = 'none';
    }
}

function formatFilterValue(key, value) {
    // Format filter values for display
    if (key === 'investor_type' || key === 'stage' || key === 'funding_type') {
        return value.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    return value;
}

function removeFilter(filterName, filterValue) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete(filterName);
    urlParams.delete('page'); // Reset to first page
    
    const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
    window.location.href = newUrl;
}

function clearAllFilters() {
    window.location.href = window.location.pathname;
}

function initializeCardInteractions() {
    // Add hover effects and interactions to result cards
    const resultCards = document.querySelectorAll('.search-result-card');
    
    resultCards.forEach(card => {
        // Add click-to-expand functionality for descriptions
        const description = card.querySelector('.card-description');
        if (description && description.textContent.includes('...')) {
            description.style.cursor = 'pointer';
            description.title = 'Click to read more';
            
            description.addEventListener('click', function() {
                // This would expand the description or show a modal
                showToast('Full description would be shown here', 'info');
            });
        }
        
        // Add keyboard navigation
        card.setAttribute('tabindex', '0');
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const viewBtn = card.querySelector('.btn-view');
                if (viewBtn) {
                    viewBtn.click();
                }
            }
        });
    });
}

function expressInterest(targetId, targetType) {
    // Show confirmation with custom styling
    const confirmMessage = targetType === 'investor' 
        ? 'Express interest in connecting with this investor?' 
        : 'Express interest in this startup?';
    
    if (confirm(confirmMessage)) {
        // Show loading state
        const button = event.target;
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Connecting...';
        button.disabled = true;
        
        // Prepare request data
        const requestData = targetType === 'investor' 
            ? `investor_id=${targetId}&interested=true`
            : `startup_id=${targetId}&interested=true`;
        
        // Make API request
        fetch('/startup/api/match/interest', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: requestData
        })
        .then(response => response.json())
        .then(data => {
            button.innerHTML = originalContent;
            button.disabled = false;
            
            if (data.success) {
                const successMessage = targetType === 'investor'
                    ? 'Connection request sent! We\'ll notify you if there\'s mutual interest.'
                    : 'Interest recorded! We\'ll notify you if there\'s mutual interest.';
                
                showToast(successMessage, 'success');
                
                // Update button state
                button.innerHTML = '<i class="fas fa-check me-1"></i>Sent';
                button.classList.remove('btn-connect');
                button.classList.add('btn-outline');
                button.disabled = true;
                
            } else {
                showToast(data.message || 'An error occurred. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.innerHTML = originalContent;
            button.disabled = false;
            showToast('Network error. Please check your connection and try again.', 'error');
        });
    }
}

function refreshResults() {
    // Add visual feedback for refresh action
    const refreshBtn = event.target;
    const originalContent = refreshBtn.innerHTML;
    
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Reload the page after a short delay for visual feedback
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function sortResults(sortBy) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortBy);
    urlParams.delete('page'); // Reset to first page when sorting
    
    const newUrl = window.location.pathname + '?' + urlParams.toString();
    window.location.href = newUrl;
}

function viewProfile(userId) {
    window.location.href = `/startup/profile/view/${userId}`;
}

// Enhanced pagination with smooth scrolling
function goToPage(page) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', page);
    
    const newUrl = window.location.pathname + '?' + urlParams.toString();
    window.location.href = newUrl;
    
    // Smooth scroll to top after page load
    window.addEventListener('load', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// Utility functions for better UX
function showLoadingState(element, loadingText = 'Loading...') {
    const originalContent = element.innerHTML;
    element.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText}`;
    element.disabled = true;
    
    return function() {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

function animateResultsLoad() {
    const cards = document.querySelectorAll('.search-result-card');
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

// Advanced search functionality
function toggleAdvancedSearch() {
    const advancedSection = document.querySelector('.advanced-search');
    const toggleBtn = document.querySelector('.toggle-advanced');
    
    if (advancedSection) {
        advancedSection.style.display = advancedSection.style.display === 'none' ? 'block' : 'none';
        
        if (toggleBtn) {
            const isVisible = advancedSection.style.display === 'block';
            toggleBtn.innerHTML = isVisible 
                ? '<i class="fas fa-chevron-up me-2"></i>Hide Advanced' 
                : '<i class="fas fa-chevron-down me-2"></i>Show Advanced';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate results on load
    if (document.querySelectorAll('.search-result-card').length > 0) {
        setTimeout(animateResultsLoad, 100);
    }
    
    // Initialize tooltips for truncated content
    const descriptions = document.querySelectorAll('.card-description');
    descriptions.forEach(desc => {
        if (desc.textContent.includes('...')) {
            desc.title = 'Click to read full description';
        }
    });
});

// Global utilities
window.SearchUtils = {
    expressInterest: expressInterest,
    refreshResults: refreshResults,
    sortResults: sortResults,
    viewProfile: viewProfile,
    goToPage: goToPage,
    clearAllFilters: clearAllFilters,
    toggleAdvancedSearch: toggleAdvancedSearch
};

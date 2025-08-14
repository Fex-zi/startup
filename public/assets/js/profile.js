/**
 * Profile Management JavaScript
 * Handles profile interactions, file uploads, and form validation
 */

class ProfileManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupFileUploads();
        this.setupFormValidation();
        this.setupImagePreviews();
        this.setupTooltips();
        this.setupInteractiveElements();
    }

    /**
     * Enhanced file upload handling with validation and preview
     */
    setupFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e.target);
            });
        });
    }

    handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;

        // File size validation
        const maxSize = this.getMaxFileSize(input.name);
        if (file.size > maxSize) {
            showToast(
                `File size too large. Maximum size is ${Math.round(maxSize / 1024 / 1024)}MB`, 
                'error'
            );
            input.value = '';
            return;
        }

        // File type validation
        const allowedTypes = this.getAllowedFileTypes(input.name);
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(fileExtension)) {
            showToast(
                `Invalid file type. Allowed: ${allowedTypes.join(', ')}`, 
                'error'
            );
            input.value = '';
            return;
        }

        // Show success message and preview if applicable
        showToast(`File "${file.name}" selected successfully`, 'success');
        
        if (input.name === 'logo' || input.name === 'profile_picture') {
            this.showImagePreview(input, file);
        }

        // Update UI to show file selected
        this.updateFileInputUI(input, file);
    }

    getMaxFileSize(inputName) {
        return inputName === 'logo' || inputName === 'profile_picture' 
            ? 2 * 1024 * 1024  // 2MB for images
            : 10 * 1024 * 1024; // 10MB for documents
    }

    getAllowedFileTypes(inputName) {
        if (inputName === 'logo' || inputName === 'profile_picture') {
            return ['jpg', 'jpeg', 'png', 'webp'];
        }
        return ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
    }

    showImagePreview(input, file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.querySelector('.logo-preview img');
            if (preview) {
                preview.src = e.target.result;
                preview.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    preview.style.transform = 'scale(1)';
                }, 300);
            }
        };
        reader.readAsDataURL(file);
    }

    updateFileInputUI(input, file) {
        // Find any associated file info display and update it
        const infoElement = input.parentElement.querySelector('.file-info');
        if (infoElement) {
            infoElement.innerHTML = `
                <small class="text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    Selected: ${file.name}
                </small>
            `;
        }
    }

    /**
     * Enhanced form validation with real-time feedback
     */
    setupFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                this.handleFormSubmit(e, form);
            });

            // Real-time validation for key fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', () => {
                    this.validateField(field);
                });
            });
        });
    }

    handleFormSubmit(e, form) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            showToast('Please fill in all required fields', 'error');
            
            // Focus on first invalid field
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            this.showFormSubmissionState(form);
        }
        
        form.classList.add('was-validated');
    }

    validateField(field) {
        if (field.checkValidity()) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }
    }

    showFormSubmissionState(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        submitBtn.disabled = true;

        // Reset after 5 seconds in case of network issues
        setTimeout(() => {
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
        }, 5000);
    }

    /**
     * Image preview functionality
     */
    setupImagePreviews() {
        const logoInput = document.getElementById('logo');
        const profilePictureInput = document.getElementById('profile_picture');

        if (logoInput) {
            logoInput.addEventListener('change', (e) => {
                this.previewImage(e.target, '.logo-preview');
            });
        }

        if (profilePictureInput) {
            profilePictureInput.addEventListener('change', (e) => {
                this.previewImage(e.target, '.profile-preview');
            });
        }
    }

    previewImage(input, previewSelector) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const previewContainer = document.querySelector(previewSelector);
            if (!previewContainer) return;

            let img = previewContainer.querySelector('img');
            if (img) {
                img.src = e.target.result;
            } else {
                // Create new image element
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.alt = 'Preview';
                newImg.className = 'rounded-circle border border-primary';
                newImg.style.cssText = 'width: 120px; height: 120px; object-fit: cover;';
                
                previewContainer.innerHTML = '';
                previewContainer.appendChild(newImg);
            }
        };
        reader.readAsDataURL(file);
    }

    /**
     * Setup tooltips for better user guidance
     */
    setupTooltips() {
        // Initialize Bootstrap tooltips if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
    }

    /**
     * Interactive elements for better UX
     */
    setupInteractiveElements() {
        // Animate cards on hover
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Smooth scroll for form sections
        const formSections = document.querySelectorAll('.card[class*="border-"]');
        formSections.forEach((section, index) => {
            const header = section.querySelector('.card-header');
            if (header) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        });
    }
}

/**
 * Profile interaction functions for public profiles
 */
class ProfileInteractions {
    static expressInterest(startupId) {
        if (!confirm('Express interest in this startup?')) return;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('input[name="_token"]')?.value;
        
        fetch(url('api/match/interest'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                startup_id: startupId,
                action: 'express_interest'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Interest expressed successfully!', 'success');
                
                // Update button state
                const btn = event.target.closest('button');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Interest Sent';
                    btn.classList.remove('btn-light', 'btn-primary');
                    btn.classList.add('btn-success');
                    btn.disabled = true;
                }
            } else {
                showToast(data.message || 'Failed to express interest', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
        });
    }

    static sendMessage(userId) {
        // Placeholder for messaging system
        showToast('Messaging feature will be available soon!', 'info');
        
        // TODO: When messaging system is implemented, redirect to conversation
        // window.location.href = url('messages/conversation/' + userId);
    }

    static downloadDocument(documentUrl, documentName) {
        // Track document downloads for analytics
        try {
            fetch(url('api/analytics/download'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    document_url: documentUrl,
                    document_name: documentName
                })
            });
        } catch (error) {
            console.error('Analytics tracking failed:', error);
        }

        // Open document in new tab
        window.open(documentUrl, '_blank');
    }
}

/**
 * Utility functions
 */
function url(path = '') {
    const baseUrl = window.location.origin;
    const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
    return baseUrl + basePath + '/' + path.replace(/^\//, '');
}

function showToast(message, type = 'info') {
    // Use the global toast function if available
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
        return;
    }

    // Fallback to console log if toast system not available
    console.log(`${type.toUpperCase()}: ${message}`);
    
    // Simple alert fallback for important messages
    if (type === 'error') {
        alert('Error: ' + message);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize profile manager
    new ProfileManager();
    
    // Make functions globally available
    window.expressInterest = ProfileInteractions.expressInterest;
    window.sendMessage = ProfileInteractions.sendMessage;
    window.downloadDocument = ProfileInteractions.downloadDocument;
    
    // Show any pending toast messages
    if (typeof window.pendingToastMessage !== 'undefined') {
        showToast(window.pendingToastMessage, window.pendingToastType || 'info');
        delete window.pendingToastMessage;
        delete window.pendingToastType;
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ProfileManager, ProfileInteractions };
}

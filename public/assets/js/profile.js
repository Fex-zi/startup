// Profile page functionality with form validation, file uploads, and interactions

(function() {
    'use strict';

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeProfileFunctionality();
    });

    function initializeProfileFunctionality() {
        // Image preview functionality
        setupImagePreviews();
        
        // Form validation
        setupFormValidation();
        
        // Investment range validation (for investors)
        setupInvestmentValidation();
        
        // File upload validation
        setupFileValidation();
        
        // Industry selection helpers
        setupIndustrySelection();
        
        // Form autosave (optional)
        setupAutosave();
        
        // Profile interaction buttons
        setupProfileInteractions();
    }

    // Image Preview Functionality
    function setupImagePreviews() {
        // Logo/Profile Picture Preview
        const imageInputs = [
            { input: '#logo', preview: '.logo-preview' },
            { input: '#profile_picture', preview: '.profile-preview' }
        ];

        imageInputs.forEach(({ input, preview }) => {
            const inputElement = document.querySelector(input);
            const previewElement = document.querySelector(preview);
            
            if (inputElement && previewElement) {
                inputElement.addEventListener('change', function(e) {
                    handleImagePreview(e, previewElement, input.includes('logo'));
                });
            }
        });
    }

    function handleImagePreview(e, previewContainer, isLogo) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showToast('Please select a valid image file', 'error');
            e.target.value = '';
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showToast('Image size must be less than 2MB', 'error');
            e.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const iconClass = isLogo ? 'fa-building' : 'fa-user';
            const altText = isLogo ? 'Company Logo' : 'Profile Picture';
            
            let existingImg = previewContainer.querySelector('img');
            if (existingImg) {
                existingImg.src = e.target.result;
            } else {
                // Create new image element
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="${altText}" 
                         class="rounded-circle" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                `;
            }
        };
        reader.readAsDataURL(file);
    }

    // Form Validation
    function setupFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!validateForm(form)) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);

            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(input);
                });
            });
        });
    }

    function validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Custom validations
        if (!validateCustomRules(form)) {
            isValid = false;
        }

        if (!isValid) {
            showToast('Please fill in all required fields correctly', 'error');
        }

        return isValid;
    }

    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Email validation
        if (field.type === 'email' && value && !isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }

        // URL validation
        if (field.type === 'url' && value && !isValidURL(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid URL';
        }

        // Number validation
        if (field.type === 'number' && value) {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            const numValue = parseFloat(value);
            
            if (isNaN(numValue)) {
                isValid = false;
                errorMessage = 'Please enter a valid number';
            } else if (min && numValue < parseFloat(min)) {
                isValid = false;
                errorMessage = `Value must be at least ${min}`;
            } else if (max && numValue > parseFloat(max)) {
                isValid = false;
                errorMessage = `Value must be no more than ${max}`;
            }
        }

        // Update field UI
        updateFieldValidationUI(field, isValid, errorMessage);
        
        return isValid;
    }

    function validateCustomRules(form) {
        let isValid = true;

        // Investment range validation (for investor forms)
        const minInvestment = form.querySelector('#min_investment');
        const maxInvestment = form.querySelector('#max_investment');
        
        if (minInvestment && maxInvestment) {
            const minVal = parseFloat(minInvestment.value);
            const maxVal = parseFloat(maxInvestment.value);
            
            if (minVal && maxVal && minVal >= maxVal) {
                updateFieldValidationUI(maxInvestment, false, 'Maximum must be greater than minimum');
                isValid = false;
            }
        }

        // Company name uniqueness (could be enhanced with AJAX)
        const companyNameField = form.querySelector('#company_name');
        if (companyNameField && companyNameField.value.trim().length < 2) {
            updateFieldValidationUI(companyNameField, false, 'Company name must be at least 2 characters');
            isValid = false;
        }

        return isValid;
    }

    function updateFieldValidationUI(field, isValid, errorMessage) {
        // Remove existing validation classes
        field.classList.remove('is-valid', 'is-invalid');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError && !existingError.textContent.trim()) {
            existingError.remove();
        }

        if (!isValid) {
            field.classList.add('is-invalid');
            
            // Add error message if it doesn't exist
            if (!existingError && errorMessage) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errorMessage;
                field.parentNode.appendChild(errorDiv);
            }
        } else if (field.value.trim()) {
            field.classList.add('is-valid');
        }
    }

    // Investment Range Validation
    function setupInvestmentValidation() {
        const minInvestment = document.getElementById('min_investment');
        const maxInvestment = document.getElementById('max_investment');

        if (minInvestment && maxInvestment) {
            minInvestment.addEventListener('change', function() {
                validateInvestmentRange(minInvestment, maxInvestment);
            });

            maxInvestment.addEventListener('change', function() {
                validateInvestmentRange(minInvestment, maxInvestment);
            });
        }
    }

    function validateInvestmentRange(minField, maxField) {
        const minVal = parseFloat(minField.value);
        const maxVal = parseFloat(maxField.value);

        if (minVal && maxVal) {
            if (minVal >= maxVal) {
                showToast('Minimum investment should be less than maximum investment', 'error');
                maxField.focus();
                updateFieldValidationUI(maxField, false, 'Must be greater than minimum investment');
            } else {
                updateFieldValidationUI(minField, true, '');
                updateFieldValidationUI(maxField, true, '');
            }
        }
    }

    // File Upload Validation
    function setupFileValidation() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                validateFileUpload(e.target);
            });
        });
    }

    function validateFileUpload(input) {
        const file = input.files[0];
        if (!file) return;

        const inputName = input.name;
        let maxSize, allowedTypes;

        // Set limits based on input type
        if (inputName === 'logo' || inputName === 'profile_picture') {
            maxSize = 2 * 1024 * 1024; // 2MB
            allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        } else if (inputName === 'pitch_deck' || inputName === 'business_plan') {
            maxSize = 10 * 1024 * 1024; // 10MB
            allowedTypes = ['application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
        }

        // Validate file size
        if (file.size > maxSize) {
            showToast(`File size exceeds ${formatFileSize(maxSize)} limit`, 'error');
            input.value = '';
            return false;
        }

        // Validate file type
        if (!allowedTypes.includes(file.type)) {
            showToast('Invalid file type. Please check allowed formats.', 'error');
            input.value = '';
            return false;
        }

        return true;
    }

    // Industry Selection Helpers
    function setupIndustrySelection() {
        const industryContainer = document.querySelector('[name="preferred_industries[]"]');
        if (!industryContainer) return;

        // Add "Select All" / "Clear All" functionality
        addIndustryControls();
    }

    function addIndustryControls() {
        const industrySection = document.querySelector('label[for="preferred_industries"]');
        if (!industrySection) return;

        const controlsDiv = document.createElement('div');
        controlsDiv.className = 'industry-controls mt-2 mb-2';
        controlsDiv.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAllIndustries()">
                Select All
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllIndustries()">
                Clear All
            </button>
        `;
        
        industrySection.parentNode.insertBefore(controlsDiv, industrySection.nextSibling);
    }

    // Make these functions global for button onclick
    window.selectAllIndustries = function() {
        const checkboxes = document.querySelectorAll('input[name="preferred_industries[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    };

    window.clearAllIndustries = function() {
        const checkboxes = document.querySelectorAll('input[name="preferred_industries[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    };

    // Autosave Functionality (Optional)
    function setupAutosave() {
        const form = document.querySelector('form[action*="profile"]');
        if (!form) return;

        const inputs = form.querySelectorAll('input, textarea, select');
        let autosaveTimeout;

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autosaveTimeout);
                autosaveTimeout = setTimeout(() => {
                    saveFormData(form);
                }, 2000); // Save after 2 seconds of inactivity
            });
        });

        // Load saved data on page load
        loadFormData(form);
    }

    function saveFormData(form) {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        localStorage.setItem('profile_draft', JSON.stringify(data));
        
        // Show subtle indicator
        showAutoSaveIndicator();
    }

    function loadFormData(form) {
        const savedData = localStorage.getItem('profile_draft');
        if (!savedData) return;

        try {
            const data = JSON.parse(savedData);
            
            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field && field.type !== 'file') {
                    field.value = data[key];
                }
            });
        } catch (e) {
            console.log('Could not load saved form data');
        }
    }

    function showAutoSaveIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'autosave-indicator';
        indicator.innerHTML = '<i class="fas fa-check text-success"></i> Draft saved';
        indicator.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(indicator);
        
        // Animate in
        setTimeout(() => indicator.style.opacity = '1', 100);
        
        // Remove after delay
        setTimeout(() => {
            indicator.style.opacity = '0';
            setTimeout(() => indicator.remove(), 300);
        }, 2000);
    }

    // Profile Interaction Functions (for public profiles)
    function setupProfileInteractions() {
        // These functions are defined in the individual view files but can be enhanced here
        
        // Add loading states to interaction buttons
        const interactionButtons = document.querySelectorAll('[onclick*="expressInterest"], [onclick*="connectWithInvestor"]');
        
        interactionButtons.forEach(button => {
            const originalOnClick = button.getAttribute('onclick');
            button.addEventListener('click', function() {
                // Add loading state
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                button.disabled = true;
                
                // Reset after timeout (in case of error)
                setTimeout(() => {
                    if (button.disabled) {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                }, 5000);
            });
        });
    }

    // Utility Functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidURL(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Enhanced profile form submission with progress indication
    function enhanceFormSubmission() {
        const profileForms = document.querySelectorAll('form[action*="profile"]');
        
        profileForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Profile...';
                    submitButton.disabled = true;
                    
                    // Clear autosaved data on successful submission
                    localStorage.removeItem('profile_draft');
                }
            });
        });
    }

    // Initialize enhanced form submission
    enhanceFormSubmission();

})();
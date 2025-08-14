<?php
$layout = 'dashboard';
$title = $title ?? 'Edit Startup Profile';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- FIXED: More prominent header section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">
                                <i class="fas fa-rocket me-2"></i>Edit Your Startup Profile
                            </h3>
                            <p class="mb-0 opacity-75">
                                <strong>Update your startup, investment information and funding needs</strong>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors['general'] as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('profile/update') ?>" enctype="multipart/form-data" id="profileForm">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="_method" value="PUT">
                        
                        <!-- FIXED: Company Logo Section - More Prominent -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-image me-2"></i>Company Logo
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="logo-preview">
                                                <?php if (!empty($startup['logo_url'])): ?>
                                                    <img src="<?= upload_url($startup['logo_url']) ?>" 
                                                         alt="Company Logo" 
                                                         class="rounded-circle border border-primary" 
                                                         style="width: 120px; height: 120px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border border-primary" 
                                                         style="width: 120px; height: 120px;">
                                                        <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control form-control-lg" id="logo" name="logo" accept="image/*">
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Upload company logo (PNG, JPG, max 2MB)
                                                </small>
                                                <?php if (!empty($startup['logo_url'])): ?>
                                                    <div class="mt-2">
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Current logo uploaded successfully
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIXED: Company Information Section - More Prominent -->
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-building me-2"></i>Company Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label fw-bold">Company Name *</label>
                                        <input type="text" 
                                               class="form-control form-control-lg <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" 
                                               id="company_name" 
                                               name="company_name" 
                                               value="<?= htmlspecialchars($startup['company_name'] ?? $old_input['company_name'] ?? '') ?>"
                                               required>
                                        <?php if (isset($errors['company_name'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['company_name'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="industry_id" class="form-label fw-bold">Industry *</label>
                                        <select class="form-select form-select-lg <?= isset($errors['industry_id']) ? 'is-invalid' : '' ?>" 
                                                id="industry_id" 
                                                name="industry_id" 
                                                required>
                                            <option value="">Select your industry...</option>
                                            <?php foreach ($industries as $industry): ?>
                                                <option value="<?= $industry['id'] ?>" 
                                                        <?= ($startup['industry_id'] ?? $old_input['industry_id'] ?? '') == $industry['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($industry['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($errors['industry_id'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['industry_id'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Company Description *</label>
                                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                              id="description" 
                                              name="description" 
                                              rows="5" 
                                              placeholder="Describe your startup, what problem you solve, and your unique value proposition..."
                                              required><?= htmlspecialchars($startup['description'] ?? $old_input['description'] ?? '') ?></textarea>
                                    <?php if (isset($errors['description'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['description'][0]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="stage" class="form-label fw-bold">Current Stage *</label>
                                        <select class="form-select <?= isset($errors['stage']) ? 'is-invalid' : '' ?>" 
                                                id="stage" 
                                                name="stage" 
                                                required>
                                            <option value="">Select stage...</option>
                                            <option value="idea" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'idea' ? 'selected' : '' ?>>Idea Stage</option>
                                            <option value="prototype" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'prototype' ? 'selected' : '' ?>>Prototype</option>
                                            <option value="mvp" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'mvp' ? 'selected' : '' ?>>MVP</option>
                                            <option value="early_revenue" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'early_revenue' ? 'selected' : '' ?>>Early Revenue</option>
                                            <option value="growth" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'growth' ? 'selected' : '' ?>>Growth</option>
                                        </select>
                                        <?php if (isset($errors['stage'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['stage'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="employee_count" class="form-label fw-bold">Team Size</label>
                                        <select class="form-select" id="employee_count" name="employee_count">
                                            <option value="">Select team size...</option>
                                            <option value="1" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '1' ? 'selected' : '' ?>>Just me (1)</option>
                                            <option value="2-5" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '2-5' ? 'selected' : '' ?>>2-5 people</option>
                                            <option value="6-10" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '6-10' ? 'selected' : '' ?>>6-10 people</option>
                                            <option value="11-25" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '11-25' ? 'selected' : '' ?>>11-25 people</option>
                                            <option value="26-50" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '26-50' ? 'selected' : '' ?>>26-50 people</option>
                                            <option value="51+" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '51+' ? 'selected' : '' ?>>51+ people</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="location" class="form-label fw-bold">Location *</label>
                                        <input type="text" 
                                               class="form-control <?= isset($errors['location']) ? 'is-invalid' : '' ?>" 
                                               id="location" 
                                               name="location" 
                                               placeholder="City, State/Country"
                                               value="<?= htmlspecialchars($startup['location'] ?? $old_input['location'] ?? '') ?>"
                                               required>
                                        <?php if (isset($errors['location'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['location'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label fw-bold">Website</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="website" 
                                           name="website" 
                                           placeholder="https://yourcompany.com"
                                           value="<?= htmlspecialchars($startup['website'] ?? $old_input['website'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- FIXED: Investment Information Section - More Prominent -->
                        <div class="card border-success mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-dollar-sign me-2"></i>Investment Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="funding_goal" class="form-label fw-bold">Funding Goal ($) *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   class="form-control form-control-lg <?= isset($errors['funding_goal']) ? 'is-invalid' : '' ?>" 
                                                   id="funding_goal" 
                                                   name="funding_goal" 
                                                   placeholder="500000"
                                                   min="0"
                                                   step="1000"
                                                   value="<?= htmlspecialchars($startup['funding_goal'] ?? $old_input['funding_goal'] ?? '') ?>"
                                                   required>
                                        </div>
                                        <?php if (isset($errors['funding_goal'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['funding_goal'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="funding_type" class="form-label fw-bold">Funding Type *</label>
                                        <select class="form-select form-select-lg <?= isset($errors['funding_type']) ? 'is-invalid' : '' ?>" 
                                                id="funding_type" 
                                                name="funding_type" 
                                                required>
                                            <option value="">Select funding type...</option>
                                            <option value="seed" <?= ($startup['funding_type'] ?? $old_input['funding_type'] ?? '') === 'seed' ? 'selected' : '' ?>>Seed</option>
                                            <option value="series_a" <?= ($startup['funding_type'] ?? $old_input['funding_type'] ?? '') === 'series_a' ? 'selected' : '' ?>>Series A</option>
                                            <option value="series_b" <?= ($startup['funding_type'] ?? $old_input['funding_type'] ?? '') === 'series_b' ? 'selected' : '' ?>>Series B</option>
                                            <option value="debt" <?= ($startup['funding_type'] ?? $old_input['funding_type'] ?? '') === 'debt' ? 'selected' : '' ?>>Debt</option>
                                            <option value="grant" <?= ($startup['funding_type'] ?? $old_input['funding_type'] ?? '') === 'grant' ? 'selected' : '' ?>>Grant</option>
                                        </select>
                                        <?php if (isset($errors['funding_type'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['funding_type'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIXED: Documents Section - More Prominent -->
                        <div class="card border-warning mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Documents & Resources
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="border rounded p-3 h-100">
                                            <label for="pitch_deck" class="form-label fw-bold">
                                                <i class="fas fa-presentation text-primary me-2"></i>Pitch Deck
                                            </label>
                                            <input type="file" class="form-control" id="pitch_deck" name="pitch_deck" accept=".pdf,.ppt,.pptx">
                                            <small class="text-muted d-block mt-2">Upload your pitch deck (PDF, PPT, max 10MB)</small>
                                            <?php if (!empty($startup['pitch_deck_url'])): ?>
                                                <div class="mt-3 p-2 bg-success bg-opacity-10 rounded">
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        <strong>Current:</strong> <?= htmlspecialchars(basename($startup['pitch_deck_url'])) ?>
                                                    </small>
                                                    <a href="<?= upload_url($startup['pitch_deck_url']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary ms-2">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-3 p-2 bg-warning bg-opacity-10 rounded">
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        No pitch deck uploaded yet
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <div class="border rounded p-3 h-100">
                                            <label for="business_plan" class="form-label fw-bold">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>Business Plan
                                            </label>
                                            <input type="file" class="form-control" id="business_plan" name="business_plan" accept=".pdf,.doc,.docx">
                                            <small class="text-muted d-block mt-2">Upload your business plan (PDF, DOC, max 10MB)</small>
                                            <?php if (!empty($startup['business_plan_url'])): ?>
                                                <div class="mt-3 p-2 bg-success bg-opacity-10 rounded">
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        <strong>Current:</strong> <?= htmlspecialchars(basename($startup['business_plan_url'])) ?>
                                                    </small>
                                                    <a href="<?= upload_url($startup['business_plan_url']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-danger ms-2">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-3 p-2 bg-warning bg-opacity-10 rounded">
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        No business plan uploaded yet
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-5 p-4 bg-light rounded">
                            <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.logo-preview img {
    transition: transform 0.3s ease;
}

.logo-preview img:hover {
    transform: scale(1.05);
}

.border-primary {
    border-color: #667eea !important;
}

.border-info {
    border-color: #17a2b8 !important;
}

.border-success {
    border-color: #28a745 !important;
}

.border-warning {
    border-color: #ffc107 !important;
}
</style>

<script>
// FIXED: Enhanced image preview functionality
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector('.logo-preview img');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create new image element if none exists
                const logoDiv = document.querySelector('.logo-preview');
                logoDiv.innerHTML = `<img src="${e.target.result}" alt="Company Logo" class="rounded-circle border border-primary" style="width: 120px; height: 120px; object-fit: cover;">`;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Enhanced form validation
(function() {
    'use strict';
    const form = document.getElementById('profileForm');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            showToast('Please fill in all required fields', 'error');
        } else {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds in case of error
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        }
        form.classList.add('was-validated');
    }, false);
})();

// File size validation
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = this.name === 'logo' ? 2 * 1024 * 1024 : 10 * 1024 * 1024; // 2MB for images, 10MB for docs
            if (file.size > maxSize) {
                showToast(`File size too large. Maximum size is ${maxSize / 1024 / 1024}MB`, 'error');
                this.value = '';
                return;
            }
            showToast(`File "${file.name}" selected successfully`, 'success');
        }
    });
});
</script>

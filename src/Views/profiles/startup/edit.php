<?php
$layout = 'dashboard';
$title = $title ?? 'Edit Startup Profile';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Your Startup Profile
                    </h4>
                    <p class="text-muted mb-0">Update your startup information and funding needs</p>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors['general'] as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('profile/update') ?>" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="_method" value="PUT">
                        
                        <!-- Company Logo Upload -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Company Logo</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="logo-preview">
                                        <?php if (!empty($startup['logo_path'])): ?>
                                            <img src="<?= asset('uploads/logos/' . $startup['logo_path']) ?>" 
                                                 alt="Company Logo" 
                                                 class="rounded-circle" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-building text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="text-muted">Upload company logo (PNG, JPG, max 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" 
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
                                <label for="industry_id" class="form-label">Industry *</label>
                                <select class="form-select <?= isset($errors['industry_id']) ? 'is-invalid' : '' ?>" 
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
                            <label for="description" class="form-label">Company Description *</label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
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
                                <label for="stage" class="form-label">Current Stage *</label>
                                <select class="form-select <?= isset($errors['stage']) ? 'is-invalid' : '' ?>" 
                                        id="stage" 
                                        name="stage" 
                                        required>
                                    <option value="">Select stage...</option>
                                    <option value="idea" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'idea' ? 'selected' : '' ?>>Idea Stage</option>
                                    <option value="prototype" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'prototype' ? 'selected' : '' ?>>Prototype</option>
                                    <option value="mvp" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'mvp' ? 'selected' : '' ?>>MVP</option>
                                    <option value="early_traction" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'early_traction' ? 'selected' : '' ?>>Early Traction</option>
                                    <option value="growth" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'growth' ? 'selected' : '' ?>>Growth</option>
                                    <option value="expansion" <?= ($startup['stage'] ?? $old_input['stage'] ?? '') === 'expansion' ? 'selected' : '' ?>>Expansion</option>
                                </select>
                                <?php if (isset($errors['stage'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['stage'][0]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="employee_count" class="form-label">Team Size</label>
                                <select class="form-select" id="employee_count" name="employee_count">
                                    <option value="">Select team size...</option>
                                    <option value="1" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '1' ? 'selected' : '' ?>>Just me (1)</option>
                                    <option value="2-5" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '2-5' ? 'selected' : '' ?>>2-5 people</option>
                                    <option value="6-10" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '6-10' ? 'selected' : '' ?>>6-10 people</option>
                                    <option value="11-25" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '11-25' ? 'selected' : '' ?>>11-25 people</option>
                                    <option value="26-50" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '26-50' ? 'selected' : '' ?>>26-50 people</option>
                                    <option value="50+" <?= ($startup['employee_count'] ?? $old_input['employee_count'] ?? '') === '50+' ? 'selected' : '' ?>>50+ people</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">Location *</label>
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

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="funding_goal" class="form-label">Funding Goal ($) *</label>
                                <input type="number" 
                                       class="form-control <?= isset($errors['funding_goal']) ? 'is-invalid' : '' ?>" 
                                       id="funding_goal" 
                                       name="funding_goal" 
                                       placeholder="e.g., 500000"
                                       min="0"
                                       step="1000"
                                       value="<?= htmlspecialchars($startup['funding_goal'] ?? $old_input['funding_goal'] ?? '') ?>"
                                       required>
                                <?php if (isset($errors['funding_goal'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['funding_goal'][0]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="funding_type" class="form-label">Funding Type *</label>
                                <select class="form-select <?= isset($errors['funding_type']) ? 'is-invalid' : '' ?>" 
                                        id="funding_type" 
                                        name="funding_type" 
                                        required>
                                    <option value="">Select funding type...</option>
                                    <option value="pre_seed" <?= ($startup['funding_type'] ?? $old_input['funding_type'] ?? '') === 'pre_seed' ? 'selected' : '' ?>>Pre-Seed</option>
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
                            
                            <div class="col-md-4 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="website" 
                                       name="website" 
                                       placeholder="https://yourcompany.com"
                                       value="<?= htmlspecialchars($startup['website'] ?? $old_input['website'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pitch_deck" class="form-label">Pitch Deck (Upload)</label>
                                <input type="file" class="form-control" id="pitch_deck" name="pitch_deck" accept=".pdf,.ppt,.pptx">
                                <small class="text-muted">Upload your pitch deck (PDF, PPT, max 10MB)</small>
                                <?php if (!empty($startup['pitch_deck_path'])): ?>
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-file me-1"></i>Current: <?= htmlspecialchars(basename($startup['pitch_deck_path'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="business_plan" class="form-label">Business Plan (Upload)</label>
                                <input type="file" class="form-control" id="business_plan" name="business_plan" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Upload your business plan (PDF, DOC, max 10MB)</small>
                                <?php if (!empty($startup['business_plan_path'])): ?>
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-file me-1"></i>Current: <?= htmlspecialchars(basename($startup['business_plan_path'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
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
                logoDiv.innerHTML = `<img src="${e.target.result}" alt="Company Logo" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Form validation feedback
(function() {
    'use strict';
    const forms = document.querySelectorAll('form');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                showToast('Please fill in all required fields', 'error');
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
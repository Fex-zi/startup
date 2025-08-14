<?php
$layout = 'dashboard';
$title = $title ?? 'Edit Investor Profile';
?>

<div class="container-fluid">
    <!-- FIXED: Added breadcrumb navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= url('profile') ?>">My Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-12">
            <!-- FIXED: More prominent header section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">
                                <i class="fas fa-handshake me-2"></i>Edit Your Investor Profile
                            </h3>
                            <p class="mb-0 opacity-75">
                                <strong>Update your investment preferences, portfolio and funding capacity</strong>
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
                        
                        <!-- FIXED: Profile Picture Section - More Prominent -->
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-camera me-2"></i>Profile Picture
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="profile-preview">
                                                <?php if (!empty($investor['profile_picture_url'])): ?>
                                                    <img src="<?= upload_url($investor['profile_picture_url']) ?>" 
                                                         alt="Profile Picture" 
                                                         class="rounded-circle border border-primary" 
                                                         style="width: 120px; height: 120px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border border-primary" 
                                                         style="width: 120px; height: 120px;">
                                                        <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control form-control-lg" id="profile_picture" name="profile_picture" accept="image/*">
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Upload profile picture (PNG, JPG, max 2MB)
                                                </small>
                                                <?php if (!empty($investor['profile_picture_url'])): ?>
                                                    <div class="mt-2">
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Current picture uploaded successfully
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIXED: Basic Information Section - More Prominent -->
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-circle me-2"></i>Basic Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="investor_type" class="form-label fw-bold">Investor Type *</label>
                                        <select class="form-select form-select-lg <?= isset($errors['investor_type']) ? 'is-invalid' : '' ?>" 
                                                id="investor_type" 
                                                name="investor_type" 
                                                required>
                                            <option value="">Select investor type...</option>
                                            <option value="angel" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'angel' ? 'selected' : '' ?>>Angel Investor</option>
                                            <option value="vc_firm" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'vc_firm' ? 'selected' : '' ?>>VC Firm</option>
                                            <option value="corporate" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Investor</option>
                                            <option value="family_office" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'family_office' ? 'selected' : '' ?>>Family Office</option>
                                        </select>
                                        <?php if (isset($errors['investor_type'])): ?>
                                            <div class="invalid-feedback">
                                                <?= htmlspecialchars($errors['investor_type'][0]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label fw-bold">Company/Fund Name</label>
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="company_name" 
                                               name="company_name" 
                                               placeholder="e.g., ABC Ventures, Individual Investor"
                                               value="<?= htmlspecialchars($investor['company_name'] ?? $old_input['company_name'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label fw-bold">Bio/Investment Philosophy *</label>
                                    <textarea class="form-control <?= isset($errors['bio']) ? 'is-invalid' : '' ?>" 
                                              id="bio" 
                                              name="bio" 
                                              rows="5" 
                                              placeholder="Describe your investment philosophy, experience, and what you look for in startups..."
                                              required><?= htmlspecialchars($investor['bio'] ?? $old_input['bio'] ?? '') ?></textarea>
                                    <?php if (isset($errors['bio'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['bio'][0]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label fw-bold">Location</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="location" 
                                               name="location" 
                                               placeholder="City, State/Country"
                                               value="<?= htmlspecialchars($investor['location'] ?? $old_input['location'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="availability_status" class="form-label fw-bold">Investment Status</label>
                                        <select class="form-select" id="availability_status" name="availability_status">
                                            <option value="actively_investing" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? '') === 'actively_investing' ? 'selected' : '' ?>>Actively Investing</option>
                                            <option value="selective" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? '') === 'selective' ? 'selected' : '' ?>>Selective</option>
                                            <option value="not_investing" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? '') === 'not_investing' ? 'selected' : '' ?>>Not Currently Investing</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIXED: Investment Preferences Section - More Prominent -->
                        <div class="card border-success mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-dollar-sign me-2"></i>Investment Preferences
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="investment_range_min" class="form-label fw-bold">Minimum Investment ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   class="form-control form-control-lg" 
                                                   id="investment_range_min" 
                                                   name="investment_range_min" 
                                                   placeholder="25000"
                                                   min="0"
                                                   step="1000"
                                                   value="<?= htmlspecialchars($investor['investment_range_min'] ?? $old_input['investment_range_min'] ?? '') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="investment_range_max" class="form-label fw-bold">Maximum Investment ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   class="form-control form-control-lg" 
                                                   id="investment_range_max" 
                                                   name="investment_range_max" 
                                                   placeholder="5000000"
                                                   min="0"
                                                   step="1000"
                                                   value="<?= htmlspecialchars($investor['investment_range_max'] ?? $old_input['investment_range_max'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- FIXED: User-friendly checkbox selection instead of Ctrl/Cmd multi-select -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Preferred Industries</label>
                                        <?php 
                                        $selectedIndustries = !empty($investor['preferred_industries']) 
                                            ? json_decode($investor['preferred_industries'], true) 
                                            : [];
                                        ?>
                                        <div class="industry-checkboxes border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            <div class="row">
                                                <?php foreach ($industries as $industry): ?>
                                                    <div class="col-12 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   id="industry_<?= $industry['id'] ?>" 
                                                                   name="preferred_industries[]" 
                                                                   value="<?= $industry['id'] ?>"
                                                                   <?= in_array($industry['id'], $selectedIndustries) ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="industry_<?= $industry['id'] ?>">
                                                                <?= htmlspecialchars($industry['name']) ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Click to select/deselect multiple industries</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Preferred Investment Stages</label>
                                        <?php 
                                        $selectedStages = !empty($investor['investment_stages']) 
                                            ? json_decode($investor['investment_stages'], true) 
                                            : [];
                                        ?>
                                        <div class="stages-checkboxes border rounded p-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="stage_idea" 
                                                       name="investment_stages[]" 
                                                       value="idea"
                                                       <?= in_array('idea', $selectedStages) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="stage_idea">
                                                    <strong>Idea Stage</strong> - Concepts and early planning
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="stage_prototype" 
                                                       name="investment_stages[]" 
                                                       value="prototype"
                                                       <?= in_array('prototype', $selectedStages) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="stage_prototype">
                                                    <strong>Prototype</strong> - Working models and demos
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="stage_mvp" 
                                                       name="investment_stages[]" 
                                                       value="mvp"
                                                       <?= in_array('mvp', $selectedStages) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="stage_mvp">
                                                    <strong>MVP</strong> - Minimum viable product
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="stage_early_revenue" 
                                                       name="investment_stages[]" 
                                                       value="early_revenue"
                                                       <?= in_array('early_revenue', $selectedStages) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="stage_early_revenue">
                                                    <strong>Early Revenue</strong> - First paying customers
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="stage_growth" 
                                                       name="investment_stages[]" 
                                                       value="growth"
                                                       <?= in_array('growth', $selectedStages) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="stage_growth">
                                                    <strong>Growth Stage</strong> - Scaling operations
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Select all stages you're interested in funding</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIXED: Contact & Links Section - More Prominent -->
                        <div class="card border-warning mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-link me-2"></i>Contact & Links
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="website" class="form-label fw-bold">Website</label>
                                        <input type="url" 
                                               class="form-control" 
                                               id="website" 
                                               name="website" 
                                               placeholder="https://yourfund.com"
                                               value="<?= htmlspecialchars($investor['website'] ?? $old_input['website'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="linkedin_url" class="form-label fw-bold">LinkedIn Profile</label>
                                        <input type="url" 
                                               class="form-control" 
                                               id="linkedin_url" 
                                               name="linkedin_url" 
                                               placeholder="https://linkedin.com/in/yourprofile"
                                               value="<?= htmlspecialchars($investor['linkedin_url'] ?? $old_input['linkedin_url'] ?? '') ?>">
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

.profile-preview img {
    transition: transform 0.3s ease;
}

.profile-preview img:hover {
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

/* FIXED: Enhanced breadcrumb styling */
.breadcrumb {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.breadcrumb-item + .breadcrumb-item::before {
    content: '›';
    font-weight: bold;
    color: #6c757d;
}

.breadcrumb-item a {
    color: #495057;
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-item a:hover {
    color: #007bff;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #6c757d;
    font-weight: 600;
}
</style>

<script>
// Enhanced image preview functionality
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector('.profile-preview img');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create new image element if none exists
                const previewDiv = document.querySelector('.profile-preview');
                previewDiv.innerHTML = `<img src="${e.target.result}" alt="Profile Picture" class="rounded-circle border border-primary" style="width: 120px; height: 120px; object-fit: cover;">`;
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
document.getElementById('profile_picture').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            showToast('File size too large. Maximum size is 2MB', 'error');
            this.value = '';
            return;
        }
        showToast(`File "${file.name}" selected successfully`, 'success');
    }
});
</script>

<?php
$layout = 'dashboard';
$title = $title ?? 'Edit Investor Profile';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Your Investor Profile
                    </h4>
                    <p class="text-muted mb-0">Update your investment criteria and experience</p>
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
                        
                        <!-- Profile Picture Upload -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Profile Picture</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="profile-preview">
                                        <?php if (!empty($investor['profile_picture_url'])): ?>
                                            <img src="<?= asset('uploads/profiles/' . $investor['profile_picture_url']) ?>" 
                                                 alt="Profile Picture" 
                                                 class="rounded-circle" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-user text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                        <small class="text-muted">Upload profile picture (PNG, JPG, max 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="investor_type" class="form-label">Investor Type *</label>
                                <select class="form-select <?= isset($errors['investor_type']) ? 'is-invalid' : '' ?>" 
                                        id="investor_type" 
                                        name="investor_type" 
                                        required>
                                    <option value="">Select investor type...</option>
                                    <option value="angel" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'angel' ? 'selected' : '' ?>>Angel Investor</option>
                                    <option value="vc_fund" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'vc_fund' ? 'selected' : '' ?>>VC Fund</option>
                                    <option value="corporate" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Investor</option>
                                    <option value="family_office" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'family_office' ? 'selected' : '' ?>>Family Office</option>
                                    <option value="accelerator" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'accelerator' ? 'selected' : '' ?>>Accelerator</option>
                                    <option value="government" <?= ($investor['investor_type'] ?? $old_input['investor_type'] ?? '') === 'government' ? 'selected' : '' ?>>Government Fund</option>
                                </select>
                                <?php if (isset($errors['investor_type'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['investor_type'][0]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company/Fund Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="company_name" 
                                       name="company_name" 
                                       placeholder="Your fund or company name"
                                       value="<?= htmlspecialchars($investor['company_name'] ?? $old_input['company_name'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Investment Bio *</label>
                            <textarea class="form-control <?= isset($errors['bio']) ? 'is-invalid' : '' ?>" 
                                      id="bio" 
                                      name="bio" 
                                      rows="4" 
                                      placeholder="Describe your investment experience, philosophy, and what you look for in startups..."
                                      required><?= htmlspecialchars($investor['bio'] ?? $old_input['bio'] ?? '') ?></textarea>
                            <?php if (isset($errors['bio'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['bio'][0]) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="preferred_industries" class="form-label">Preferred Industries</label>
                            <div class="row">
                                <?php 
                                $preferredIndustries = [];
                                if (isset($investor['preferred_industries'])) {
                                    $preferredIndustries = json_decode($investor['preferred_industries'], true) ?: [];
                                } elseif (isset($old_input['preferred_industries'])) {
                                    $preferredIndustries = $old_input['preferred_industries'];
                                }
                                ?>
                                <?php foreach ($industries as $industry): ?>
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="industry_<?= $industry['id'] ?>" 
                                                   name="preferred_industries[]" 
                                                   value="<?= $industry['id'] ?>"
                                                   <?= in_array($industry['id'], $preferredIndustries) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="industry_<?= $industry['id'] ?>">
                                                <?= htmlspecialchars($industry['name']) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Investment Stages</label>
                            <div class="row">
                                <?php 
                                $investmentStages = [];
                                if (isset($investor['investment_stages'])) {
                                    $investmentStages = json_decode($investor['investment_stages'], true) ?: [];
                                } elseif (isset($old_input['investment_stages'])) {
                                    $investmentStages = $old_input['investment_stages'];
                                }
                                ?>
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_idea" 
                                               name="investment_stages[]" 
                                               value="idea"
                                               <?= in_array('idea', $investmentStages) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_idea">Idea</label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_prototype" 
                                               name="investment_stages[]" 
                                               value="prototype"
                                               <?= in_array('prototype', $investmentStages) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_prototype">Prototype</label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_mvp" 
                                               name="investment_stages[]" 
                                               value="mvp"
                                               <?= in_array('mvp', $investmentStages) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_mvp">MVP</label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_traction" 
                                               name="investment_stages[]" 
                                               value="early_traction"
                                               <?= in_array('early_traction', $investmentStages) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_traction">Early Traction</label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_growth" 
                                               name="investment_stages[]" 
                                               value="growth"
                                               <?= in_array('growth', $investmentStages) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_growth">Growth</label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_expansion" 
                                               name="investment_stages[]" 
                                               value="expansion"
                                               <?= in_array('expansion', $investmentStages) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_expansion">Expansion</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="min_investment" class="form-label">Minimum Investment ($)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="min_investment" 
                                       name="min_investment" 
                                       placeholder="e.g., 10000"
                                       min="0"
                                       step="1000"
                                       value="<?= htmlspecialchars($investor['min_investment'] ?? $old_input['min_investment'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="max_investment" class="form-label">Maximum Investment ($)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="max_investment" 
                                       name="max_investment" 
                                       placeholder="e.g., 1000000"
                                       min="0"
                                       step="1000"
                                       value="<?= htmlspecialchars($investor['max_investment'] ?? $old_input['max_investment'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="location" 
                                       name="location" 
                                       placeholder="City, State/Country"
                                       value="<?= htmlspecialchars($investor['location'] ?? $old_input['location'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="website" 
                                       name="website" 
                                       placeholder="https://yourfund.com"
                                       value="<?= htmlspecialchars($investor['website'] ?? $old_input['website'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">LinkedIn Profile</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="linkedin" 
                                       name="linkedin" 
                                       placeholder="https://linkedin.com/in/yourprofile"
                                       value="<?= htmlspecialchars($investor['linkedin_url'] ?? $old_input['linkedin'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="years_experience" class="form-label">Years of Investment Experience</label>
                                <select class="form-select" id="years_experience" name="years_experience">
                                    <option value="">Select experience...</option>
                                    <option value="1-2" <?= ($investor['years_experience'] ?? $old_input['years_experience'] ?? '') === '1-2' ? 'selected' : '' ?>>1-2 years</option>
                                    <option value="3-5" <?= ($investor['years_experience'] ?? $old_input['years_experience'] ?? '') === '3-5' ? 'selected' : '' ?>>3-5 years</option>
                                    <option value="6-10" <?= ($investor['years_experience'] ?? $old_input['years_experience'] ?? '') === '6-10' ? 'selected' : '' ?>>6-10 years</option>
                                    <option value="11-15" <?= ($investor['years_experience'] ?? $old_input['years_experience'] ?? '') === '11-15' ? 'selected' : '' ?>>11-15 years</option>
                                    <option value="15+" <?= ($investor['years_experience'] ?? $old_input['years_experience'] ?? '') === '15+' ? 'selected' : '' ?>>15+ years</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="portfolio_size" class="form-label">Portfolio Size</label>
                                <select class="form-select" id="portfolio_size" name="portfolio_size">
                                    <option value="">Select portfolio size...</option>
                                    <option value="1-5" <?= ($investor['portfolio_size'] ?? $old_input['portfolio_size'] ?? '') === '1-5' ? 'selected' : '' ?>>1-5 companies</option>
                                    <option value="6-15" <?= ($investor['portfolio_size'] ?? $old_input['portfolio_size'] ?? '') === '6-15' ? 'selected' : '' ?>>6-15 companies</option>
                                    <option value="16-30" <?= ($investor['portfolio_size'] ?? $old_input['portfolio_size'] ?? '') === '16-30' ? 'selected' : '' ?>>16-30 companies</option>
                                    <option value="31-50" <?= ($investor['portfolio_size'] ?? $old_input['portfolio_size'] ?? '') === '31-50' ? 'selected' : '' ?>>31-50 companies</option>
                                    <option value="50+" <?= ($investor['portfolio_size'] ?? $old_input['portfolio_size'] ?? '') === '50+' ? 'selected' : '' ?>>50+ companies</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="portfolio_companies" class="form-label">Portfolio Companies</label>
                                <textarea class="form-control" 
                                          id="portfolio_companies" 
                                          name="portfolio_companies" 
                                          rows="3" 
                                          placeholder="List your notable portfolio companies..."><?= htmlspecialchars($investor['portfolio_companies'] ?? $old_input['portfolio_companies'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="availability_status" class="form-label">Investment Status</label>
                                <select class="form-select" id="availability_status" name="availability_status">
                                    <option value="actively_investing" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? 'actively_investing') === 'actively_investing' ? 'selected' : '' ?>>Actively Investing</option>
                                    <option value="selectively_investing" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? '') === 'selectively_investing' ? 'selected' : '' ?>>Selectively Investing</option>
                                    <option value="not_investing" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? '') === 'not_investing' ? 'selected' : '' ?>>Not Currently Investing</option>
                                    <option value="on_hold" <?= ($investor['availability_status'] ?? $old_input['availability_status'] ?? '') === 'on_hold' ? 'selected' : '' ?>>Investment on Hold</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="investment_philosophy" class="form-label">Investment Philosophy</label>
                            <textarea class="form-control" 
                                      id="investment_philosophy" 
                                      name="investment_philosophy" 
                                      rows="3" 
                                      placeholder="Describe your investment approach, what makes you unique as an investor..."><?= htmlspecialchars($investor['investment_philosophy'] ?? $old_input['investment_philosophy'] ?? '') ?></textarea>
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
// Profile picture preview functionality
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
                const profileDiv = document.querySelector('.profile-preview');
                profileDiv.innerHTML = `<img src="${e.target.result}" alt="Profile Picture" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`;
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

// Investment range validation
document.getElementById('min_investment').addEventListener('change', function() {
    const minVal = parseInt(this.value);
    const maxInput = document.getElementById('max_investment');
    const maxVal = parseInt(maxInput.value);
    
    if (minVal && maxVal && minVal >= maxVal) {
        showToast('Minimum investment should be less than maximum investment', 'error');
        maxInput.focus();
    }
});

document.getElementById('max_investment').addEventListener('change', function() {
    const maxVal = parseInt(this.value);
    const minInput = document.getElementById('min_investment');
    const minVal = parseInt(minInput.value);
    
    if (minVal && maxVal && maxVal <= minVal) {
        showToast('Maximum investment should be greater than minimum investment', 'error');
        this.focus();
    }
});
</script>
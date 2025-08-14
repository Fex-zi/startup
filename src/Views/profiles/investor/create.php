<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Create Your Investor Profile
                    </h4>
                    <p class="text-muted mb-0">Tell startups about your investment criteria and experience</p>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors['general'] as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('profile/store') ?>" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="investor_type" class="form-label">Investor Type *</label>
                                <select class="form-select <?= isset($errors['investor_type']) ? 'is-invalid' : '' ?>" 
                                        id="investor_type" 
                                        name="investor_type" 
                                        required>
                                    <option value="">Select investor type...</option>
                                    <option value="angel" <?= ($old_input['investor_type'] ?? '') === 'angel' ? 'selected' : '' ?>>Angel Investor</option>
                                    <option value="vc_firm" <?= ($old_input['investor_type'] ?? '') === 'vc_firm' ? 'selected' : '' ?>>VC Firm</option>
                                    <option value="corporate" <?= ($old_input['investor_type'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Investor</option>
                                    <option value="family_office" <?= ($old_input['investor_type'] ?? '') === 'family_office' ? 'selected' : '' ?>>Family Office</option>
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
                                       placeholder="Your investment firm or fund name"
                                       value="<?= htmlspecialchars($old_input['company_name'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio/Investment Philosophy *</label>
                            <textarea class="form-control <?= isset($errors['bio']) ? 'is-invalid' : '' ?>" 
                                      id="bio" 
                                      name="bio" 
                                      rows="4" 
                                      placeholder="Describe your investment experience, philosophy, and what you look for in startups..."
                                      required><?= htmlspecialchars($old_input['bio'] ?? '') ?></textarea>
                            <?php if (isset($errors['bio'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['bio'][0]) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- FIXED: Added missing profile picture upload -->
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            <small class="text-muted">Upload your profile picture (PNG, JPG, max 2MB)</small>
                        </div>

                        <div class="mb-3">
                            <label for="preferred_industries" class="form-label">Preferred Industries</label>
                            <div class="row">
                                <?php foreach ($industries as $industry): ?>
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="industry_<?= $industry['id'] ?>" 
                                                   name="preferred_industries[]" 
                                                   value="<?= $industry['id'] ?>"
                                                   <?= in_array($industry['id'], $old_input['preferred_industries'] ?? []) ? 'checked' : '' ?>>
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
                                <div class="col-md-2 col-sm-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_idea" 
                                               name="investment_stages[]" 
                                               value="idea"
                                               <?= in_array('idea', $old_input['investment_stages'] ?? []) ? 'checked' : '' ?>>
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
                                               <?= in_array('prototype', $old_input['investment_stages'] ?? []) ? 'checked' : '' ?>>
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
                                               <?= in_array('mvp', $old_input['investment_stages'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_mvp">MVP</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_early_revenue" 
                                               name="investment_stages[]" 
                                               value="early_revenue"
                                               <?= in_array('early_revenue', $old_input['investment_stages'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_early_revenue">Early Revenue</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="stage_growth" 
                                               name="investment_stages[]" 
                                               value="growth"
                                               <?= in_array('growth', $old_input['investment_stages'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="stage_growth">Growth</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="investment_range_min" class="form-label">Min Investment (USD) *</label>
                                <input type="number" 
                                       class="form-control <?= isset($errors['investment_range_min']) ? 'is-invalid' : '' ?>" 
                                       id="investment_range_min" 
                                       name="investment_range_min" 
                                       placeholder="e.g. 25000"
                                       min="0"
                                       step="1000"
                                       value="<?= htmlspecialchars($old_input['investment_range_min'] ?? '') ?>"
                                       required>
                                <?php if (isset($errors['investment_range_min'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['investment_range_min'][0]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="investment_range_max" class="form-label">Max Investment (USD) *</label>
                                <input type="number" 
                                       class="form-control <?= isset($errors['investment_range_max']) ? 'is-invalid' : '' ?>" 
                                       id="investment_range_max" 
                                       name="investment_range_max" 
                                       placeholder="e.g. 500000"
                                       min="0"
                                       step="1000"
                                       value="<?= htmlspecialchars($old_input['investment_range_max'] ?? '') ?>"
                                       required>
                                <?php if (isset($errors['investment_range_max'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['investment_range_max'][0]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label">Location *</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['location']) ? 'is-invalid' : '' ?>" 
                                       id="location" 
                                       name="location" 
                                       placeholder="City, State/Country"
                                       value="<?= htmlspecialchars($old_input['location'] ?? '') ?>"
                                       required>
                                <?php if (isset($errors['location'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['location'][0]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn Profile</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="linkedin_url" 
                                       name="linkedin_url" 
                                       placeholder="https://linkedin.com/in/yourprofile"
                                       value="<?= htmlspecialchars($old_input['linkedin_url'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="website" 
                                       name="website" 
                                       placeholder="https://yourfund.com"
                                       value="<?= htmlspecialchars($old_input['website'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

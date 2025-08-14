<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-rocket me-2"></i>Create Your Startup Profile
                    </h4>
                    <p class="text-muted mb-0">Tell investors about your startup and funding needs</p>
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
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" 
                                       id="company_name" 
                                       name="company_name" 
                                       value="<?= htmlspecialchars($old_input['company_name'] ?? '') ?>"
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
                                                <?= ($old_input['industry_id'] ?? '') == $industry['id'] ? 'selected' : '' ?>>
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
                                      required><?= htmlspecialchars($old_input['description'] ?? '') ?></textarea>
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
                                    <option value="idea" <?= ($old_input['stage'] ?? '') === 'idea' ? 'selected' : '' ?>>Idea</option>
                                    <option value="prototype" <?= ($old_input['stage'] ?? '') === 'prototype' ? 'selected' : '' ?>>Prototype</option>
                                    <option value="mvp" <?= ($old_input['stage'] ?? '') === 'mvp' ? 'selected' : '' ?>>MVP</option>
                                    <option value="early_revenue" <?= ($old_input['stage'] ?? '') === 'early_revenue' ? 'selected' : '' ?>>Early Revenue</option>
                                    <option value="growth" <?= ($old_input['stage'] ?? '') === 'growth' ? 'selected' : '' ?>>Growth</option>
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
                                    <option value="1" <?= ($old_input['employee_count'] ?? '') === '1' ? 'selected' : '' ?>>Just me (1)</option>
                                    <option value="2-5" <?= ($old_input['employee_count'] ?? '') === '2-5' ? 'selected' : '' ?>>2-5 people</option>
                                    <option value="6-10" <?= ($old_input['employee_count'] ?? '') === '6-10' ? 'selected' : '' ?>>6-10 people</option>
                                    <option value="11-25" <?= ($old_input['employee_count'] ?? '') === '11-25' ? 'selected' : '' ?>>11-25 people</option>
                                    <option value="26-50" <?= ($old_input['employee_count'] ?? '') === '26-50' ? 'selected' : '' ?>>26-50 people</option>
                                    <option value="51+" <?= ($old_input['employee_count'] ?? '') === '51+' ? 'selected' : '' ?>>51+ people</option>
                                </select>
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
                            <div class="col-md-4 mb-3">
                                <label for="funding_goal" class="form-label">Funding Goal (USD) *</label>
                                <input type="number" 
                                       class="form-control <?= isset($errors['funding_goal']) ? 'is-invalid' : '' ?>" 
                                       id="funding_goal" 
                                       name="funding_goal" 
                                       placeholder="e.g. 500000"
                                       min="0"
                                       step="1000"
                                       value="<?= htmlspecialchars($old_input['funding_goal'] ?? '') ?>"
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
                                    <option value="seed" <?= ($old_input['funding_type'] ?? '') === 'seed' ? 'selected' : '' ?>>Seed</option>
                                    <option value="series_a" <?= ($old_input['funding_type'] ?? '') === 'series_a' ? 'selected' : '' ?>>Series A</option>
                                    <option value="series_b" <?= ($old_input['funding_type'] ?? '') === 'series_b' ? 'selected' : '' ?>>Series B</option>
                                    <option value="debt" <?= ($old_input['funding_type'] ?? '') === 'debt' ? 'selected' : '' ?>>Debt</option>
                                    <option value="grant" <?= ($old_input['funding_type'] ?? '') === 'grant' ? 'selected' : '' ?>>Grant</option>
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
                                       value="<?= htmlspecialchars($old_input['website'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- FIXED: Added missing file upload sections -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-upload me-2"></i>Company Assets</h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="logo" class="form-label">Company Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <small class="text-muted">Upload your company logo (PNG, JPG, max 2MB)</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="pitch_deck" class="form-label">Pitch Deck</label>
                                <input type="file" class="form-control" id="pitch_deck" name="pitch_deck" accept=".pdf,.ppt,.pptx">
                                <small class="text-muted">Upload your pitch deck (PDF, PPT, max 10MB)</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="business_plan" class="form-label">Business Plan</label>
                                <input type="file" class="form-control" id="business_plan" name="business_plan" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Upload your business plan (PDF, DOC, max 10MB)</small>
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

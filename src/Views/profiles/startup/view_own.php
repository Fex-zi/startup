<?php
$layout = 'dashboard';
$title = $title ?? ($startup['company_name'] ?? 'My Startup Profile');
?>

<div class="container-fluid">
    <!-- Enhanced Profile Header with Edit Controls -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card profile-header-card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="profile-logo-large">
                                <?php if (!empty($startup['logo_url'])): ?>
                                    <img src="<?= upload_url($startup['logo_url']) ?>" 
                                         alt="<?= htmlspecialchars($startup['company_name']) ?> Logo" 
                                         class="rounded-circle img-fluid shadow" 
                                         style="width: 140px; height: 140px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto shadow" 
                                         style="width: 140px; height: 140px;">
                                        <i class="fas fa-building text-white" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <small class="text-white-50">
                                        <?php if (!empty($startup['logo_url'])): ?>
                                            <i class="fas fa-check-circle me-1"></i>Logo uploaded
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-triangle me-1"></i>No logo yet
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h1 class="display-5 mb-2 text-white"><?= htmlspecialchars($startup['company_name']) ?></h1>
                            <p class="lead text-white-50 mb-3">
                                <?= htmlspecialchars($startup['tagline'] ?? 'Your startup - ready to change the world') ?>
                            </p>
                            <div class="profile-badges d-flex flex-wrap gap-2">
                                <span class="badge bg-white text-primary px-3 py-2 fs-6">
                                    <i class="fas fa-industry me-1"></i>
                                    <?= htmlspecialchars($industry['name'] ?? 'Technology') ?>
                                </span>
                                <span class="badge bg-success px-3 py-2 fs-6">
                                    <i class="fas fa-chart-line me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $startup['stage'] ?? 'early_stage')) ?>
                                </span>
                                <span class="badge bg-info px-3 py-2 fs-6">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($startup['location'] ?? 'Remote') ?>
                                </span>
                                <?php if (!empty($startup['employee_count'])): ?>
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                    <i class="fas fa-users me-1"></i>
                                    <?= htmlspecialchars($startup['employee_count']) ?> employees
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="profile-actions">
                                <a href="<?= url('profile/edit') ?>" class="btn btn-light btn-lg mb-2 shadow">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                                <?php if (!empty($startup['website'])): ?>
                                    <a href="<?= htmlspecialchars($startup['website']) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-light">
                                        <i class="fas fa-external-link-alt me-2"></i>Visit Website
                                    </a>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <a href="<?= url('profile/view/' . $user['id']) ?>" 
                                       class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-eye me-1"></i>Public View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔥 CRITICAL FIX: Profile Completeness Widget (using real data) -->
    <div class="row mb-4">
        <div class="col-12">
            <?= render_profile_completion_widget($user['id'], $user['user_type']) ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Company Description -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>About <?= htmlspecialchars($startup['company_name']) ?>
                    </h5>
                    <a href="<?= url('profile/edit') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($startup['description'])): ?>
                        <p class="lead lh-lg"><?= nl2br(htmlspecialchars($startup['description'])) ?></p>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-plus-circle text-muted fa-3x mb-3"></i>
                            <h6 class="text-muted">Add your company description</h6>
                            <p class="text-muted">Tell investors about your vision, mission, and what problem you're solving.</p>
                            <a href="<?= url('profile/edit') ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add Description
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Investment Information -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Investment Information
                    </h5>
                    <a href="<?= url('profile/edit') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted mb-2">Funding Goal</h6>
                                <p class="h3 text-success mb-0">
                                    <?= format_currency($startup['funding_goal'] ?? 0) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted mb-2">Funding Type</h6>
                                <p class="h5 mb-0">
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        <?= ucfirst(str_replace('_', ' ', $startup['funding_type'] ?? 'Not specified')) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Documents & Resources
                    </h5>
                    <a href="<?= url('profile/edit') ?>" class="btn btn-dark btn-sm">
                        <i class="fas fa-upload me-1"></i>Upload
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Pitch Deck -->
                        <div class="col-md-6 mb-3">
                            <?php if (!empty($startup['pitch_deck_url'])): ?>
                                <div class="document-item p-4 border border-primary rounded-3 h-100 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-presentation text-primary fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">Pitch Deck</h6>
                                    <p class="text-muted small mb-3">
                                        <?= htmlspecialchars(basename($startup['pitch_deck_url'])) ?>
                                    </p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="<?= upload_url($startup['pitch_deck_url']) ?>" 
                                           target="_blank" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="<?= upload_url($startup['pitch_deck_url']) ?>" 
                                           download
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="document-placeholder p-4 border border-dashed rounded-3 h-100 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-plus-circle text-muted fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Upload Pitch Deck</h6>
                                    <p class="text-muted small mb-3">Share your compelling presentation with investors</p>
                                    <a href="<?= url('profile/edit') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-upload me-1"></i>Upload
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Business Plan -->
                        <div class="col-md-6 mb-3">
                            <?php if (!empty($startup['business_plan_url'])): ?>
                                <div class="document-item p-4 border border-danger rounded-3 h-100 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-pdf text-danger fa-3x"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">Business Plan</h6>
                                    <p class="text-muted small mb-3">
                                        <?= htmlspecialchars(basename($startup['business_plan_url'])) ?>
                                    </p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="<?= upload_url($startup['business_plan_url']) ?>" 
                                           target="_blank" 
                                           class="btn btn-danger btn-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="<?= upload_url($startup['business_plan_url']) ?>" 
                                           download
                                           class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="document-placeholder p-4 border border-dashed rounded-3 h-100 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-plus-circle text-muted fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">Upload Business Plan</h6>
                                    <p class="text-muted small mb-3">Detailed plan for your business strategy</p>
                                    <a href="<?= url('profile/edit') ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-upload me-1"></i>Upload
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Sidebar -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Company Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-calendar-alt me-2"></i>Founded
                            </span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($startup['founded_year'] ?? date('Y')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-rocket me-2"></i>Stage
                            </span>
                            <span class="fw-bold">
                                <?= ucfirst(str_replace('_', ' ', $startup['stage'] ?? 'Early')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-users me-2"></i>Team Size
                            </span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($startup['employee_count'] ?? 'Not specified') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i>Location
                            </span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($startup['location'] ?? 'Remote') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <a href="<?= url('profile/edit') ?>" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-edit me-1"></i>Update Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Activity -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-activity me-2"></i>Profile Activity
                    </h6>
                </div>
                <div class="card-body">
                    <div class="activity-item mb-3">
                        <i class="fas fa-user-plus text-success me-2"></i>
                        <span>Profile created <?= date('M j, Y', strtotime($user['created_at'])) ?></span>
                    </div>
                    
                    <div class="activity-item mb-3">
                        <i class="fas fa-edit text-info me-2"></i>
                        <span>Last updated <?= date('M j, Y', strtotime($startup['updated_at'])) ?></span>
                    </div>
                    
                    <div class="activity-item">
                        <i class="fas fa-eye text-primary me-2"></i>
                        <span>Profile views: <strong>0</strong></span>
                        <small class="text-muted d-block">Coming soon</small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= url('matches') ?>" class="btn btn-primary">
                            <i class="fas fa-handshake me-2"></i>View Matches
                        </a>
                        <a href="<?= url('search/investors') ?>" class="btn btn-success">
                            <i class="fas fa-search me-2"></i>Find Investors
                        </a>
                        <a href="<?= url('profile/edit') ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
}

.document-placeholder {
    transition: all 0.3s ease;
}

.document-placeholder:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.activity-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}
</style>

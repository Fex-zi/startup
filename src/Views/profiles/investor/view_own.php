<?php
$layout = 'dashboard';
$title = $title ?? 'My Investor Profile';
?>

<div class="container-fluid">
    <!-- FIXED: Added breadcrumb navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
        </ol>
    </nav>
    
    <!-- Enhanced Profile Header with Edit Controls -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card profile-header-card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="profile-logo-large">
                                <?php if (!empty($investor['profile_picture_url'])): ?>
                                    <img src="<?= upload_url($investor['profile_picture_url']) ?>" 
                                         alt="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" 
                                         class="rounded-circle img-fluid shadow" 
                                         style="width: 140px; height: 140px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto shadow" 
                                         style="width: 140px; height: 140px;">
                                        <i class="fas fa-user-tie text-white" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <small class="text-white-50">
                                        <?php if (!empty($investor['profile_picture_url'])): ?>
                                            <i class="fas fa-check-circle me-1"></i>Photo uploaded
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-triangle me-1"></i>No photo yet
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h1 class="display-5 mb-2 text-white">
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </h1>
                            <?php if (!empty($investor['company_name'])): ?>
                                <p class="lead text-white-50 mb-3">
                                    <?= htmlspecialchars($investor['company_name']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="profile-badges d-flex flex-wrap gap-2">
                                <span class="badge bg-white text-primary px-3 py-2 fs-6">
                                    <i class="fas fa-handshake me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'Angel Investor')) ?>
                                </span>
                                <span class="badge bg-success px-3 py-2 fs-6">
                                    <i class="fas fa-chart-line me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $investor['availability_status'] ?? 'actively_investing')) ?>
                                </span>
                                <?php if (!empty($investor['location'])): ?>
                                <span class="badge bg-info px-3 py-2 fs-6">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($investor['location']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="profile-actions">
                                <a href="<?= url('profile/settings') ?>" class="btn btn-light btn-lg mb-2 shadow">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                                <?php if (!empty($investor['website'])): ?>
                                    <a href="<?= htmlspecialchars($investor['website']) ?>" 
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

    <!-- Profile Completeness Alert -->
    <?php 
    $completenessScore = 0;
    $missingItems = [];
    
    if (!empty($investor['bio'])) $completenessScore += 30; else $missingItems[] = 'investment philosophy';
    if (!empty($investor['profile_picture_url'])) $completenessScore += 20; else $missingItems[] = 'profile picture';
    if (!empty($investor['investment_range_min']) && !empty($investor['investment_range_max'])) $completenessScore += 25; else $missingItems[] = 'investment range';
    if (!empty($investor['preferred_industries'])) $completenessScore += 15; else $missingItems[] = 'preferred industries';
    if (!empty($investor['investment_stages'])) $completenessScore += 10; else $missingItems[] = 'preferred stages';
    ?>
    
    <?php if ($completenessScore < 100): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Profile Completeness: <?= $completenessScore ?>%</h5>
                        <p class="mb-2">Complete your profile to get better startup matches!</p>
                        <small class="text-muted">
                            Missing: <?= implode(', ', $missingItems) ?>
                        </small>
                    </div>
                    <div>
                        <a href="<?= url('profile/settings') ?>" class="btn btn-warning">
                        <i class="fas fa-plus me-1"></i>Complete Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Investment Philosophy -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Investment Philosophy & Bio
                    </h5>
                    <a href="<?= url('profile/settings') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($investor['bio'])): ?>
                        <p class="lead lh-lg"><?= nl2br(htmlspecialchars($investor['bio'])) ?></p>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-plus-circle text-muted fa-3x mb-3"></i>
                            <h6 class="text-muted">Add your investment philosophy</h6>
                            <p class="text-muted">Tell startups about your investment approach, experience, and what you look for.</p>
                            <a href="<?= url('profile/settings') ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add Bio
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Investment Preferences -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Investment Preferences
                    </h5>
                    <a href="<?= url('profile/edit') ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted mb-2">Investment Range</h6>
                                <?php if (!empty($investor['investment_range_min']) && !empty($investor['investment_range_max'])): ?>
                                    <p class="h4 text-success mb-0">
                                        <?= format_currency($investor['investment_range_min']) ?> - <?= format_currency($investor['investment_range_max']) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted">Not specified</p>
                                    <a href="<?= url('profile/settings') ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Range
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted mb-2">Preferred Industries</h6>
                                <?php if (!empty($preferred_industry_names)): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($preferred_industry_names as $industry): ?>
                                            <span class="badge bg-primary"><?= htmlspecialchars($industry['name']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Not specified</p>
                                    <a href="<?= url('profile/edit') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Industries
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="funding-metric">
                                <h6 class="text-muted mb-2">Preferred Stages</h6>
                                <?php if (!empty($investor['investment_stages'])): ?>
                                    <?php 
                                    $stages = json_decode($investor['investment_stages'], true) ?? [];
                                    if (!empty($stages)): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($stages as $stage): ?>
                                                <span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $stage)) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-muted">Not specified</p>
                                    <a href="<?= url('profile/settings') ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-plus me-1"></i>Add Stages
                                    </a>
                                <?php endif; ?>
                            </div>
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
                        <i class="fas fa-chart-bar me-2"></i>Investor Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-user-tie me-2"></i>Type
                            </span>
                            <span class="fw-bold">
                                <?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'Angel')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-chart-line me-2"></i>Status
                            </span>
                            <span class="fw-bold">
                                <?= ucfirst(str_replace('_', ' ', $investor['availability_status'] ?? 'Active')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (!empty($investor['location'])): ?>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i>Location
                            </span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($investor['location']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="stat-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <i class="fas fa-calendar-alt me-2"></i>Joined
                            </span>
                            <span class="fw-bold">
                                <?= date('M Y', strtotime($user['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <a href="<?= url('profile/settings') ?>" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-edit me-1"></i>Update Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact & Links
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($investor['website'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-globe text-muted me-2"></i>
                        <a href="<?= htmlspecialchars($investor['website']) ?>" target="_blank" class="text-decoration-none">
                            Website
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($investor['linkedin_url'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fab fa-linkedin text-muted me-2"></i>
                        <a href="<?= htmlspecialchars($investor['linkedin_url']) ?>" target="_blank" class="text-decoration-none">
                            LinkedIn Profile
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (empty($investor['website']) && empty($investor['linkedin_url'])): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-plus-circle text-muted fa-2x mb-2"></i>
                        <p class="text-muted small">Add your contact links</p>
                        <a href="<?= url('profile/settings') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Links
                        </a>
                    </div>
                    <?php endif; ?>
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
                        <a href="<?= url('search/startups') ?>" class="btn btn-success">
                            <i class="fas fa-search me-2"></i>Find Startups
                        </a>
                        <a href="<?= url('profile/settings') ?>" class="btn btn-warning">
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

.funding-metric {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    margin-bottom: 1rem;
}

.stat-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.stat-item:last-child {
    border-bottom: none;
}

.contact-item {
    display: flex;
    align-items-center;
    padding: 0.5rem 0;
}
</style>

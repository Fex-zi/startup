<?php
$layout = 'dashboard';
$title = $title ?? ($startup['company_name'] ?? 'Startup Profile');
?>

<div class="container-fluid">
    <!-- FIXED: Enhanced Profile Header -->
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
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h1 class="display-5 mb-2 text-white"><?= htmlspecialchars($startup['company_name']) ?></h1>
                            <p class="lead text-white-50 mb-3"><?= htmlspecialchars($startup['tagline'] ?? 'Innovative startup seeking investment') ?></p>
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
                                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'investor'): ?>
                                    <button class="btn btn-light btn-lg mb-2 shadow" onclick="expressInterest(<?= $startup['id'] ?>)">
                                        <i class="fas fa-heart me-2 text-danger"></i>Express Interest
                                    </button>
                                    <button class="btn btn-outline-light mb-2" onclick="sendMessage(<?= $user['id'] ?>)">
                                        <i class="fas fa-envelope me-2"></i>Send Message
                                    </button>
                                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                                    <a href="<?= url('profile/edit') ?>" class="btn btn-light btn-lg mb-2 shadow">
                                        <i class="fas fa-edit me-2"></i>Edit Profile
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($startup['website'])): ?>
                                    <a href="<?= htmlspecialchars($startup['website']) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-light">
                                        <i class="fas fa-external-link-alt me-2"></i>Visit Website
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Company Description -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>About <?= htmlspecialchars($startup['company_name']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lead lh-lg"><?= nl2br(htmlspecialchars($startup['description'] ?? '')) ?></p>
                </div>
            </div>

            <!-- Funding Information -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Investment Information
                    </h5>
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

            <!-- FIXED: Documents Section - Now Properly Displays Files -->
            <?php if (!empty($startup['pitch_deck_url']) || !empty($startup['business_plan_url'])): ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Documents & Resources
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($startup['pitch_deck_url'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="document-item p-4 border border-primary rounded-3 h-100 text-center">
                                <div class="mb-3">
                                    <i class="fas fa-presentation text-primary fa-3x"></i>
                                </div>
                                <h6 class="fw-bold mb-2">Pitch Deck</h6>
                                <p class="text-muted small mb-3">
                                    <?= htmlspecialchars(basename($startup['pitch_deck_url'])) ?>
                                </p>
                                <a href="<?= upload_url($startup['pitch_deck_url']) ?>" 
                                   target="_blank" 
                                   class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i>Download PDF
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($startup['business_plan_url'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="document-item p-4 border border-danger rounded-3 h-100 text-center">
                                <div class="mb-3">
                                    <i class="fas fa-file-pdf text-danger fa-3x"></i>
                                </div>
                                <h6 class="fw-bold mb-2">Business Plan</h6>
                                <p class="text-muted small mb-3">
                                    <?= htmlspecialchars(basename($startup['business_plan_url'])) ?>
                                </p>
                                <a href="<?= upload_url($startup['business_plan_url']) ?>" 
                                   target="_blank" 
                                   class="btn btn-danger">
                                    <i class="fas fa-download me-1"></i>Download PDF
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'investor'): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-info-circle me-3 fa-2x"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Investor Access</h6>
                                    <p class="mb-0">As an investor, you can download and review these documents. Express interest to start a conversation!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <!-- Show placeholder when no documents are uploaded -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Documents & Resources
                    </h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open text-muted fa-4x mb-3"></i>
                    <h6 class="text-muted">No documents uploaded yet</h6>
                    <p class="text-muted">This startup hasn't uploaded their pitch deck or business plan yet.</p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                        <a href="<?= url('profile/edit') ?>" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Documents
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
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
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="contact-item mb-3">
                        <i class="fas fa-user text-muted me-2"></i>
                        <span class="fw-bold"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                        <small class="text-muted d-block">Founder</small>
                    </div>
                    
                    <?php if (!empty($startup['website'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-globe text-muted me-2"></i>
                        <a href="<?= htmlspecialchars($startup['website']) ?>" target="_blank" class="text-decoration-none">
                            <?= htmlspecialchars(parse_url($startup['website'], PHP_URL_HOST)) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-item">
                        <i class="fas fa-calendar text-muted me-2"></i>
                        <span>Joined <?= date('M Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Similar Startups -->
            <?php if (!empty($similar_startups)): ?>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Similar Startups
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach (array_slice($similar_startups, 0, 3) as $similar): ?>
                    <div class="similar-item mb-3 pb-3 <?= end($similar_startups) !== $similar ? 'border-bottom' : '' ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <?php if (!empty($similar['logo_url'])): ?>
                                    <img src="<?= upload_url($similar['logo_url']) ?>" 
                                         alt="Logo" 
                                         class="rounded-circle" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-building text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="<?= url('profile/view/' . $similar['user_id']) ?>" 
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($similar['company_name']) ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <?= htmlspecialchars($similar['industry_name']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.profile-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
}

.profile-badges .badge {
    font-weight: 500;
    border-radius: 25px;
}

.funding-metric {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    margin-bottom: 1rem;
}

.document-item {
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.document-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.stat-item:last-child {
    border-bottom: none;
}

.contact-item {
    display: flex;
    align-items-center;
    padding: 0.5rem 0;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    font-weight: 600;
}

.shadow-sm {
    box-shadow: 0 2px 4px rgba(0,0,0,.05) !important;
}

.shadow-lg {
    box-shadow: 0 10px 25px rgba(0,0,0,.1) !important;
}
</style>

<script>
function expressInterest(startupId) {
    if (!confirm('Express interest in this startup?')) return;
    
    fetch('<?= url('api/match/interest') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= htmlspecialchars($csrf_token ?? '') ?>'
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
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Interest Sent';
            btn.classList.remove('btn-light');
            btn.classList.add('btn-success');
            btn.disabled = true;
        } else {
            showToast(data.message || 'Failed to express interest', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

function sendMessage(userId) {
    // For now, redirect to messages page (will be implemented in messaging system)
    showToast('Messaging feature coming soon!', 'info');
}
</script>

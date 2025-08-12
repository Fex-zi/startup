<?php
$layout = 'dashboard';
$title = $title ?? ($startup['company_name'] ?? 'Startup Profile');
?>

<div class="container-fluid">
    <div class="row">
        <!-- Profile Header -->
        <div class="col-12 mb-4">
            <div class="card profile-header-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="profile-logo-large">
                                <?php if (!empty($startup['logo_path'])): ?>
                                    <img src="<?= asset('uploads/logos/' . $startup['logo_path']) ?>" 
                                         alt="<?= htmlspecialchars($startup['company_name']) ?> Logo" 
                                         class="rounded-circle img-fluid" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-building text-white" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h1 class="display-6 mb-2"><?= htmlspecialchars($startup['company_name']) ?></h1>
                            <p class="lead text-muted mb-3"><?= htmlspecialchars($startup['tagline'] ?? '') ?></p>
                            <div class="profile-badges d-flex flex-wrap gap-2">
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="fas fa-industry me-1"></i>
                                    <?= htmlspecialchars($startup['industry_name'] ?? 'Technology') ?>
                                </span>
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-chart-line me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $startup['stage'] ?? 'early_stage')) ?>
                                </span>
                                <span class="badge bg-info px-3 py-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($startup['location'] ?? 'Remote') ?>
                                </span>
                                <?php if (!empty($startup['employee_count'])): ?>
                                <span class="badge bg-warning px-3 py-2">
                                    <i class="fas fa-users me-1"></i>
                                    <?= htmlspecialchars($startup['employee_count']) ?> employees
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="profile-actions">
                                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'investor'): ?>
                                    <button class="btn btn-primary btn-lg mb-2" onclick="expressInterest(<?= $startup['id'] ?>)">
                                        <i class="fas fa-heart me-2"></i>Express Interest
                                    </button>
                                    <button class="btn btn-outline-secondary mb-2" onclick="sendMessage(<?= $user['id'] ?>)">
                                        <i class="fas fa-envelope me-2"></i>Send Message
                                    </button>
                                <?php endif; ?>
                                <?php if (!empty($startup['website'])): ?>
                                    <a href="<?= htmlspecialchars($startup['website']) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-primary">
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
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>About <?= htmlspecialchars($startup['company_name']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lead"><?= nl2br(htmlspecialchars($startup['description'] ?? '')) ?></p>
                </div>
            </div>

            <!-- Funding Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Funding Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted">Funding Goal</h6>
                                <p class="h4 text-primary">
                                    $<?= number_format($startup['funding_goal'] ?? 0) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted">Funding Type</h6>
                                <p class="h5">
                                    <?= ucfirst(str_replace('_', ' ', $startup['funding_type'] ?? 'Not specified')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($startup['current_revenue'])): ?>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted">Annual Revenue</h6>
                                <p class="h5 text-success">
                                    $<?= number_format($startup['current_revenue']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="funding-metric">
                                <h6 class="text-muted">Monthly Growth Rate</h6>
                                <p class="h5 text-info">
                                    <?= htmlspecialchars($startup['growth_rate'] ?? 'N/A') ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Team Information -->
            <?php if (!empty($startup['team_info'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Team
                    </h5>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($startup['team_info'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Documents -->
            <?php if (!empty($startup['pitch_deck_path']) || !empty($startup['business_plan_path'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Documents
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($startup['pitch_deck_path'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="document-item p-3 border rounded">
                                <i class="fas fa-presentation text-primary fa-2x mb-2"></i>
                                <h6>Pitch Deck</h6>
                                <a href="<?= asset('uploads/documents/' . $startup['pitch_deck_path']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($startup['business_plan_path'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="document-item p-3 border rounded">
                                <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                                <h6>Business Plan</h6>
                                <a href="<?= asset('uploads/documents/' . $startup['business_plan_path']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Company Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Founded</span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($startup['founded_year'] ?? date('Y')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Stage</span>
                            <span class="fw-bold">
                                <?= ucfirst(str_replace('_', ' ', $startup['stage'] ?? 'Early')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Team Size</span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($startup['employee_count'] ?? 'Not specified') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Location</span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($startup['location'] ?? 'Remote') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="contact-item mb-3">
                        <i class="fas fa-user text-muted me-2"></i>
                        <span><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                    </div>
                    
                    <?php if (!empty($user['email']) && ($user['email_public'] ?? false)): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                            <?= htmlspecialchars($user['email']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['phone']) && ($user['phone_public'] ?? false)): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <a href="tel:<?= htmlspecialchars($user['phone']) ?>">
                            <?= htmlspecialchars($user['phone']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($startup['website'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-globe text-muted me-2"></i>
                        <a href="<?= htmlspecialchars($startup['website']) ?>" target="_blank">
                            <?= htmlspecialchars(parse_url($startup['website'], PHP_URL_HOST)) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Similar Startups -->
            <?php if (!empty($similar_startups)): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Similar Startups
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach (array_slice($similar_startups, 0, 3) as $similar): ?>
                    <div class="similar-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <?php if (!empty($similar['logo_path'])): ?>
                                    <img src="<?= asset('uploads/logos/' . $similar['logo_path']) ?>" 
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
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.profile-header-card .card-body {
    padding: 2rem;
}

.profile-badges .badge {
    font-size: 0.9rem;
    font-weight: 500;
}

.funding-metric {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.document-item {
    text-align: center;
    transition: all 0.3s ease;
}

.document-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.stat-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.stat-item:last-child {
    border-bottom: none;
}

.contact-item {
    display: flex;
    align-items: center;
}

.similar-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
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
            btn.classList.remove('btn-primary');
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
    // For now, redirect to messages page
    window.location.href = '<?= url('messages/conversation/') ?>' + userId;
}
</script>
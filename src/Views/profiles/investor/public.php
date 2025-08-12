<?php
$layout = 'dashboard';
$title = $title ?? ($investor['company_name'] ?? ($user['first_name'] . ' ' . $user['last_name']));
?>

<div class="container-fluid">
    <div class="row">
        <!-- Profile Header -->
        <div class="col-12 mb-4">
            <div class="card profile-header-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="profile-picture-large">
                                <?php if (!empty($investor['profile_picture_url'])): ?>
                                    <img src="<?= asset('uploads/profiles/' . $investor['profile_picture_url']) ?>" 
                                         alt="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" 
                                         class="rounded-circle img-fluid" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user text-white" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h1 class="display-6 mb-2">
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </h1>
                            <?php if (!empty($investor['company_name'])): ?>
                                <p class="lead text-muted mb-3">
                                    <?= htmlspecialchars($investor['company_name']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="profile-badges d-flex flex-wrap gap-2">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'angel')) ?> Investor
                                </span>
                                <?php if (!empty($investor['years_experience'])): ?>
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= htmlspecialchars($investor['years_experience']) ?> years experience
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($investor['location'])): ?>
                                <span class="badge bg-info px-3 py-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($investor['location']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($investor['portfolio_size'])): ?>
                                <span class="badge bg-warning px-3 py-2">
                                    <i class="fas fa-briefcase me-1"></i>
                                    <?= htmlspecialchars($investor['portfolio_size']) ?> portfolio
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="profile-actions">
                                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'startup'): ?>
                                    <button class="btn btn-primary btn-lg mb-2" onclick="connectWithInvestor(<?= $investor['id'] ?>)">
                                        <i class="fas fa-handshake me-2"></i>Connect
                                    </button>
                                    <button class="btn btn-outline-secondary mb-2" onclick="sendMessage(<?= $user['id'] ?>)">
                                        <i class="fas fa-envelope me-2"></i>Send Message
                                    </button>
                                <?php endif; ?>
                                <?php if (!empty($investor['website'])): ?>
                                    <a href="<?= htmlspecialchars($investor['website']) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-2"></i>Visit Website
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($investor['linkedin'])): ?>
                                    <a href="<?= htmlspecialchars($investor['linkedin']) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-info">
                                        <i class="fab fa-linkedin me-2"></i>LinkedIn
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
            <!-- Investment Bio -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>About <?= htmlspecialchars($user['first_name']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lead"><?= nl2br(htmlspecialchars($investor['bio'] ?? '')) ?></p>
                </div>
            </div>

            <!-- Investment Criteria -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-target me-2"></i>Investment Criteria
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="investment-metric">
                                <h6 class="text-muted">Investment Range</h6>
                                <p class="h4 text-success">
                                    $<?= number_format($investor['min_investment'] ?? 0) ?> - 
                                    $<?= number_format($investor['max_investment'] ?? 0) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="investment-metric">
                                <h6 class="text-muted">Investor Type</h6>
                                <p class="h5">
                                    <?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'Angel Investor')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferred Industries -->
            <?php 
            $preferredIndustries = [];
            if (!empty($investor['preferred_industries'])) {
                $industryIds = json_decode($investor['preferred_industries'], true);
                if ($industryIds && is_array($industryIds)) {
                    // Would need to fetch industry names from database
                    $preferredIndustries = $industryIds; // Simplified for now
                }
            }
            ?>
            <?php if (!empty($preferredIndustries)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-industry me-2"></i>Preferred Industries
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($preferredIndustries as $industry): ?>
                            <span class="badge bg-light text-dark px-3 py-2 border">
                                <?= htmlspecialchars($industry) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Investment Stages -->
            <?php 
            $investmentStages = [];
            if (!empty($investor['investment_stages'])) {
                $investmentStages = json_decode($investor['investment_stages'], true) ?: [];
            }
            ?>
            <?php if (!empty($investmentStages)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Investment Stages
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($investmentStages as $stage): ?>
                            <span class="badge bg-primary px-3 py-2">
                                <?= ucfirst(str_replace('_', ' ', $stage)) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Investment Philosophy -->
            <?php if (!empty($investor['investment_philosophy'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Investment Philosophy
                    </h5>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($investor['investment_philosophy'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Investments -->
            <?php if (!empty($recent_investments)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-handshake me-2"></i>Recent Investments
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach (array_slice($recent_investments, 0, 6) as $investment): ?>
                        <div class="col-md-6 mb-3">
                            <div class="investment-item p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if (!empty($investment['logo_url'])): ?>
                                            <img src="<?= asset('uploads/logos/' . $investment['logo_url']) ?>" 
                                                 alt="Logo" 
                                                 class="rounded-circle" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <?= htmlspecialchars($investment['company_name']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($investment['industry_name']) ?>
                                        </small>
                                        <div>
                                            <span class="badge bg-success">
                                                <?= ucfirst($investment['stage']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
                        <i class="fas fa-chart-bar me-2"></i>Investor Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Investor Type</span>
                            <span class="fw-bold">
                                <?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'Angel')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Experience</span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($investor['years_experience'] ?? 'Not specified') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Portfolio Size</span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($investor['portfolio_size'] ?? 'Not specified') ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Location</span>
                            <span class="fw-bold">
                                <?= htmlspecialchars($investor['location'] ?? 'Not specified') ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($investor['min_investment']) && !empty($investor['max_investment'])): ?>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Investment Range</span>
                            <span class="fw-bold text-success">
                                $<?= number_format($investor['min_investment']) ?>-<?= number_format($investor['max_investment']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
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
                    
                    <?php if (!empty($investor['company_name'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-building text-muted me-2"></i>
                        <span><?= htmlspecialchars($investor['company_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    
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
                    
                    <?php if (!empty($investor['website'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fas fa-globe text-muted me-2"></i>
                        <a href="<?= htmlspecialchars($investor['website']) ?>" target="_blank">
                            <?= htmlspecialchars(parse_url($investor['website'], PHP_URL_HOST)) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($investor['linkedin'])): ?>
                    <div class="contact-item mb-3">
                        <i class="fab fa-linkedin text-muted me-2"></i>
                        <a href="<?= htmlspecialchars($investor['linkedin']) ?>" target="_blank">
                            LinkedIn Profile
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Similar Investors -->
            <?php if (!empty($similar_investors)): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>Similar Investors
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach (array_slice($similar_investors, 0, 3) as $similar): ?>
                    <div class="similar-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <?php if (!empty($similar['profile_picture_url'])): ?>
                                    <img src="<?= asset('uploads/profiles/' . $similar['profile_picture_url']) ?>" 
                                         alt="Profile" 
                                         class="rounded-circle" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="<?= url('profile/view/' . $similar['user_id']) ?>" 
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($similar['first_name'] . ' ' . $similar['last_name']) ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <?= ucfirst(str_replace('_', ' ', $similar['investor_type'])) ?>
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
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

.investment-metric {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.investment-item {
    transition: all 0.3s ease;
}

.investment-item:hover {
    transform: translateY(-3px);
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
function connectWithInvestor(investorId) {
    if (!confirm('Send a connection request to this investor?')) return;
    
    fetch('<?= url('api/match/interest') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= htmlspecialchars($csrf_token ?? '') ?>'
        },
        body: JSON.stringify({
            investor_id: investorId,
            action: 'connect'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Connection request sent successfully!', 'success');
            // Update button state
            const btn = event.target.closest('button');
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Request Sent';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            btn.disabled = true;
        } else {
            showToast(data.message || 'Failed to send connection request', 'error');
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
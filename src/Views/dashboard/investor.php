<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'Investor') ?>!</h2>
                <p class="card-text text-muted">
                    <?php if (!empty($investor['company_name'])): ?>
                        Managing investments for <strong><?= htmlspecialchars($investor['company_name']) ?></strong>
                    <?php else: ?>
                        Your investment dashboard and opportunities
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Error Message Display -->
<?php if (!empty($error_message)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($error_message) ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-rocket fa-2x mb-2"></i>
                <h3><?= $match_stats['total_matches'] ?? 0 ?></h3>
                <p class="mb-0">Startup Matches</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-handshake fa-2x mb-2"></i>
                <h3><?= $match_stats['mutual_matches'] ?? 0 ?></h3>
                <p class="mb-0">Mutual Interest</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h3><?= $match_stats['pending_matches'] ?? 0 ?></h3>
                <p class="mb-0">Pending Reviews</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-star fa-2x mb-2"></i>
                <h3><?= round($match_stats['avg_match_score'] ?? 0) ?>%</h3>
                <p class="mb-0">Avg Match Score</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Startup Matches -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-rocket me-2"></i>Recent Startup Opportunities
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($matches) && is_array($matches)): ?>
                    <?php foreach ($matches as $match): ?>
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <?php if (!empty($match['logo_url'])): ?>
                                    <img src="<?= htmlspecialchars($match['logo_url']) ?>" 
                                         alt="<?= htmlspecialchars($match['company_name'] ?? '') ?>"
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary rounded d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 50px; height: 50px;">
                                        <?= strtoupper(substr($match['company_name'] ?? 'S', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <?= htmlspecialchars($match['company_name'] ?? 'Startup') ?>
                                </h6>
                                <p class="mb-1 text-muted">
                                    <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>
                                </p>
                                <small class="text-success">
                                    <i class="fas fa-star me-1"></i><?= $match['match_score'] ?? 0 ?>% Match
                                    <?php if (!empty($match['funding_goal'])): ?>
                                        • Seeking $<?= number_format($match['funding_goal']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?= url('matches/view/' . ($match['id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="text-center">
                        <a href="<?= url('matches') ?>" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>View All Matches
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-rocket fa-3x text-muted mb-3"></i>
                        <h5>No startup matches yet</h5>
                        <p class="text-muted">Complete your investor profile to start getting matched with startups!</p>
                        <a href="<?= url('profile/edit') ?>" class="btn btn-primary">
                            <i class="fas fa-user-edit me-2"></i>Complete Profile
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('profile/edit') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                    <a href="<?= url('search/startups') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i>Find Startups
                    </a>
                    <a href="<?= url('matches') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-heart me-2"></i>View All Matches
                    </a>
                    <a href="<?= url('messages') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Investment Criteria -->
        <?php if (!empty($investor)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Investment Criteria
                </h5>
            </div>
            <div class="card-body">
                <h6><?= htmlspecialchars($investor['company_name'] ?? 'Your Investment Profile') ?></h6>
                <p class="text-muted small mb-2">
                    <?= htmlspecialchars(substr($investor['bio'] ?? 'No bio available', 0, 100)) ?>
                    <?php if (strlen($investor['bio'] ?? '') > 100): ?>...<?php endif; ?>
                </p>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Investment Range</small>
                        <div class="fw-bold">
                            $<?= number_format($investor['investment_range_min'] ?? 0) ?> - 
                            $<?= number_format($investor['investment_range_max'] ?? 0) ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Type</small>
                        <div class="fw-bold">
                            <?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'Angel')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Startups -->
<?php if (!empty($recent_startups) && is_array($recent_startups)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-rocket me-2"></i>Recently Joined Startups
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($recent_startups as $startup): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <div class="text-white fw-bold">
                                                <?= strtoupper(substr($startup['company_name'] ?? 'S', 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($startup['company_name'] ?? 'Startup') ?></h6>
                                            <small class="text-muted">
                                                <?= ucfirst(str_replace('_', ' ', $startup['stage'] ?? 'Early Stage')) ?>
                                            </small>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-2">
                                        <?= htmlspecialchars(substr($startup['description'] ?? '', 0, 80)) ?>
                                        <?php if (strlen($startup['description'] ?? '') > 80): ?>...<?php endif; ?>
                                    </p>
                                    <a href="<?= url('profile/view/' . ($startup['user_id'] ?? '')) ?>" class="btn btn-sm btn-outline-primary">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
</style>
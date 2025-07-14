<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</h2>
                <p class="card-text text-muted">
                    Here's what's happening with <strong><?= htmlspecialchars($startup['company_name'] ?? 'your startup') ?></strong>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-heart fa-2x mb-2"></i>
                <h3><?= $match_stats['total_matches'] ?? 0 ?></h3>
                <p class="mb-0">Total Matches</p>
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
                <p class="mb-0">Pending</p>
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
    <!-- Recent Matches -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-heart me-2"></i>Recent Matches
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($matches)): ?>
                    <?php foreach ($matches as $match): ?>
                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <?= htmlspecialchars($match['first_name'] . ' ' . $match['last_name']) ?>
                                </h6>
                                <p class="mb-1 text-muted">
                                    <?= htmlspecialchars($match['investor_company'] ?? 'Individual Investor') ?>
                                </p>
                                <small class="text-success">
                                    <i class="fas fa-star me-1"></i><?= $match['match_score'] ?>% Match
                                </small>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="/matches/<?= $match['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="text-center">
                        <a href="/matches" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>View All Matches
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h5>No matches yet</h5>
                        <p class="text-muted">Complete your profile to start getting matched with investors!</p>
                        <a href="/profile/edit" class="btn btn-primary">
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
                    <a href="/profile/edit" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                    <a href="/search/investors" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i>Find Investors
                    </a>
                    <a href="/matches" class="btn btn-outline-primary">
                        <i class="fas fa-heart me-2"></i>View All Matches
                    </a>
                    <a href="/messages" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Startup Info -->
        <?php if ($startup): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-rocket me-2"></i>Your Startup
                </h5>
            </div>
            <div class="card-body">
                <h6><?= htmlspecialchars($startup['company_name']) ?></h6>
                <p class="text-muted small mb-2">
                    <?= htmlspecialchars($startup['description'] ?? 'No description yet') ?>
                </p>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted">Stage</small>
                        <div class="fw-bold"><?= ucfirst($startup['stage']) ?></div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Funding Goal</small>
                        <div class="fw-bold">$<?= number_format($startup['funding_goal'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Investors -->
<?php if (!empty($recent_investors)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Recently Joined Investors
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($recent_investors as $investor): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']) ?></h6>
                                            <small class="text-muted"><?= ucfirst($investor['investor_type']) ?></small>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-2">
                                        <?= htmlspecialchars($investor['company_name'] ?? 'Individual Investor') ?>
                                    </p>
                                    <a href="/profile/view/<?= $investor['id'] ?>" class="btn btn-sm btn-outline-primary">
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

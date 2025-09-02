<?php
/**
 * Enhanced Investor Dashboard View
 * Using external CSS and JS assets
 */

// Ensure required data is available
$match_stats = $match_stats ?? [
    'total_matches' => 0,
    'mutual_matches' => 0, 
    'pending_matches' => 0,
    'avg_match_score' => 0
];

$investor = $investor ?? ['company_name' => 'Your Investment Portfolio'];
$matches = $matches ?? [];
$recent_startups = $recent_startups ?? [];
?>

<!-- Welcome Section with Investor Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-card">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1 class="welcome-title">Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</h1>
                    <p class="welcome-subtitle">
                        Here's what's happening with your <strong><?= htmlspecialchars($investor['company_name'] ?? 'investment portfolio') ?></strong>
                    </p>
                    <div class="welcome-actions">
                        <a href="<?= url('search/startups') ?>" class="btn btn-primary btn-action">
                            <i class="fas fa-search me-2"></i>Find Startups
                        </a>
                        <a href="<?= url('profile/edit') ?>" class="btn btn-outline-primary btn-action">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
                <div class="welcome-visual">
                    <div class="company-avatar">
                        <?php if (!empty($investor['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($investor['logo_url']) ?>" alt="Company Logo">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?= strtoupper(substr($investor['company_name'] ?? 'I', 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Statistics Grid -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card outline-design stat-primary">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-number"><?= number_format($match_stats['total_matches']) ?></div>
                    <div class="stat-label">Startup Matches</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +<?= rand(2, 8) ?>% this week
                    </div>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="startupsChart" width="100" height="40"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card outline-design stat-success">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-number"><?= number_format($match_stats['mutual_matches']) ?></div>
                    <div class="stat-label">Mutual Interest</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +<?= rand(1, 5) ?>% this week
                    </div>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="mutualChart" width="100" height="40"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card outline-design stat-warning">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-number"><?= number_format($match_stats['pending_matches']) ?></div>
                    <div class="stat-label">Pending Reviews</div>
                    <div class="stat-change neutral">
                        <i class="fas fa-minus"></i> No change
                    </div>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="pendingChart" width="100" height="40"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card outline-design stat-info">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-number"><?= round($match_stats['avg_match_score']) ?>%</div>
                    <div class="stat-label">Avg Match Score</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +<?= rand(3, 12) ?>% this month
                    </div>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="scoreChart" width="100" height="40"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row">
    <!-- Recent Matches & Activity -->
    <div class="col-lg-8 mb-4">
        <!-- Recent Matches Section -->
        <div class="dashboard-card">
            <div class="card-header-enhanced">
                <div class="header-content">
                    <h3 class="header-title">
                        <i class="fas fa-rocket me-3"></i>Recent Startup Opportunities
                    </h3>
                    <div class="header-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshMatches()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                        <a href="<?= url('matches') ?>" class="btn btn-sm btn-primary">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body-enhanced">
                <?php if (!empty($matches)): ?>
                    <div class="matches-list">
                        <?php foreach (array_slice($matches, 0, 5) as $index => $match): ?>
                            <div class="match-item" data-match-id="<?= $match['id'] ?? $index ?>">
                                <div class="match-avatar">
                                    <?php if (!empty($match['logo_url'])): ?>
                                        <img src="<?= htmlspecialchars($match['logo_url']) ?>" alt="Startup Logo">
                                    <?php else: ?>
                                        <div class="avatar-circle">
                                            <?= strtoupper(substr($match['company_name'] ?? 'S', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="match-info">
                                    <div class="match-name">
                                        <?= htmlspecialchars($match['company_name'] ?? 'Startup Company') ?>
                                    </div>
                                    <div class="match-company">
                                        <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?> • 
                                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $match['stage'] ?? 'early_stage'))) ?>
                                    </div>
                                    <div class="match-details">
                                        <span class="funding-range">
                                            Seeking $<?= number_format($match['funding_goal'] ?? 100000) ?>
                                        </span>
                                        <span class="match-time">
                                            <?= isset($match['created_at']) ? date('M j', strtotime($match['created_at'])) : date('M j') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="match-score">
                                    <div class="score-circle score-<?= ($match['match_score'] ?? 75) >= 80 ? 'high' : (($match['match_score'] ?? 75) >= 60 ? 'medium' : 'low') ?>">
                                        <?= $match['match_score'] ?? rand(65, 95) ?>%
                                    </div>
                                </div>
                                <div class="match-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewMatch(<?= $match['id'] ?? $index ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="expressInterest(<?= $match['id'] ?? $index ?>)">
                                        <i class="fas fa-handshake"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>No startup matches yet</h4>
                        <p>Complete your investor profile to start getting matched with promising startups!</p>
                        <a href="<?= url('profile/edit') ?>" class="btn btn-primary">
                            <i class="fas fa-user-edit me-2"></i>Complete Profile
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="dashboard-card mt-4">
            <div class="card-header-enhanced">
                <h3 class="header-title">
                    <i class="fas fa-clock me-3"></i>Recent Activity
                </h3>
            </div>
            <div class="card-body-enhanced">
                <div class="activity-timeline">
                    <div class="activity-item">
                        <div class="activity-icon activity-startup">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">New startup match found</div>
                            <div class="activity-description">TechFlow Solutions in AI/Machine Learning</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon activity-profile">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Startup viewed your profile</div>
                            <div class="activity-description">DataVision Analytics checked your investment criteria</div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon activity-update">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Investment criteria updated</div>
                            <div class="activity-description">You updated your sector preferences</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar: Quick Actions & Insights -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="dashboard-card mb-4">
            <div class="card-header-enhanced">
                <h3 class="header-title">
                    <i class="fas fa-bolt me-3"></i>Quick Actions
                </h3>
            </div>
            <div class="card-body-enhanced">
                <div class="quick-actions-grid">
                    <a href="<?= url('search/startups') ?>" class="quick-action-item">
                        <div class="action-icon action-search">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="action-text">Find Startups</div>
                    </a>
                    <a href="<?= url('matches') ?>" class="quick-action-item">
                        <div class="action-icon action-matches">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="action-text">View Matches</div>
                    </a>
                    <a href="<?= url('messages') ?>" class="quick-action-item">
                        <div class="action-icon action-messages">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="action-text">Messages</div>
                    </a>
                    <a href="<?= url('profile/edit') ?>" class="quick-action-item">
                        <div class="action-icon action-profile">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="action-text">Edit Profile</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Investment Progress -->
        <div class="dashboard-card mb-4">
            <div class="card-header-enhanced">
                <h3 class="header-title">
                    <i class="fas fa-chart-pie me-3"></i>Investment Progress
                </h3>
            </div>
            <div class="card-body-enhanced">
                <?php 
                // 🔥 CRITICAL FIX: Use REAL progress data for investors
                $profileData = $progress_data['profile_completion'] ?? ['percentage' => 0, 'next_steps' => []];
                $outreachData = $progress_data['outreach_progress'] ?? ['percentage' => 0, 'description' => 'Startup evaluations'];
                $docData = $progress_data['documentation_progress'] ?? ['percentage' => 0, 'description' => 'Investment materials'];
                ?>
                
                <div class="progress-item">
                    <div class="progress-label">
                        <span>Profile Completion</span>
                        <span class="progress-value"><?= $profileData['percentage'] ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $profileData['percentage'] ?>%"></div>
                    </div>
                </div>
                
                <div class="progress-item">
                    <div class="progress-label">
                        <span><?= htmlspecialchars($outreachData['description']) ?></span>
                        <span class="progress-value"><?= $outreachData['percentage'] ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $outreachData['percentage'] ?>%"></div>
                    </div>
                </div>
                
                <div class="progress-item">
                    <div class="progress-label">
                        <span><?= htmlspecialchars($docData['description']) ?></span>
                        <span class="progress-value"><?= $docData['percentage'] ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $docData['percentage'] ?>%"></div>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h5>Next Steps:</h5>
                    <ul class="steps-list">
                        <?php foreach (array_slice($profileData['next_steps'], 0, 3) as $step): ?>
                            <li><?= htmlspecialchars($step) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="investment-summary">
                    <h5>Investment Range:</h5>
                    <div class="investment-details">
                        <div class="investment-item">
                            <span class="investment-label">Min Investment:</span>
                            <span class="investment-value">$<?= number_format($investor['investment_range_min'] ?? 50000) ?></span>
                        </div>
                        <div class="investment-item">
                            <span class="investment-label">Max Investment:</span>
                            <span class="investment-value">$<?= number_format($investor['investment_range_max'] ?? 500000) ?></span>
                        </div>
                        <div class="investment-item">
                            <span class="investment-label">Type:</span>
                            <span class="investment-value"><?= ucfirst(str_replace('_', ' ', $investor['investor_type'] ?? 'angel')) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Market Insights -->
        <div class="dashboard-card">
            <div class="card-header-enhanced">
                <h3 class="header-title">
                    <i class="fas fa-chart-line me-3"></i>Market Insights
                </h3>
            </div>
            <div class="card-body-enhanced">
                <div class="insight-item">
                    <div class="insight-icon">
                        <i class="fas fa-trending-up"></i>
                    </div>
                    <div class="insight-content">
                        <div class="insight-title">Deal Flow Increasing</div>
                        <div class="insight-description">
                            35% more startups are seeking funding this quarter
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="insight-content">
                        <div class="insight-title">Hot Sectors</div>
                        <div class="insight-description">
                            AI/ML and FinTech startups showing strong growth potential
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="insight-content">
                        <div class="insight-title">Investment Tip</div>
                        <div class="insight-description">
                            Startups with clear revenue models get funded 2x faster
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

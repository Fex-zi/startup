<?php
/**
 * Enhanced Startup Dashboard View
 * Following Critical Path Rule: Dashboard Content & Analytics Component
 */

// Ensure required data is available
$match_stats = $match_stats ?? [
    'total_matches' => 0,
    'mutual_matches' => 0, 
    'pending_matches' => 0,
    'avg_match_score' => 0
];

$startup = $startup ?? ['company_name' => 'Your Startup'];
$matches = $matches ?? [];
$recent_investors = $recent_investors ?? [];
?>

<!-- Welcome Section with Company Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-card">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1 class="welcome-title">Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</h1>
                    <p class="welcome-subtitle">
                        Here's what's happening with <strong><?= htmlspecialchars($startup['company_name'] ?? 'your startup') ?></strong>
                    </p>
                    <div class="welcome-actions">
                        <a href="<?= url('search/investors') ?>" class="btn btn-primary btn-action">
                            <i class="fas fa-search me-2"></i>Find Investors
                        </a>
                        <a href="<?= url('profile/edit') ?>" class="btn btn-outline-primary btn-action">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
                <div class="welcome-visual">
                    <div class="company-avatar">
                        <?php if (!empty($startup['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($startup['logo_url']) ?>" alt="Company Logo">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?= strtoupper(substr($startup['company_name'] ?? 'S', 0, 2)) ?>
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
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-number"><?= number_format($match_stats['total_matches']) ?></div>
                    <div class="stat-label">Total Matches</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> +<?= rand(2, 8) ?>% this week
                    </div>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="matchesChart" width="100" height="40"></canvas>
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
                    <div class="stat-label">Pending Responses</div>
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
                        <i class="fas fa-heart me-3"></i>Recent Investor Matches
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
                                    <?php if (!empty($match['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($match['avatar']) ?>" alt="Investor">
                                    <?php else: ?>
                                        <div class="avatar-circle">
                                            <?= strtoupper(substr($match['first_name'] ?? 'I', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="match-info">
                                    <div class="match-name">
                                        <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>
                                    </div>
                                    <div class="match-company">
                                        <?= htmlspecialchars($match['company_name'] ?? 'Independent Investor') ?>
                                    </div>
                                    <div class="match-details">
                                        <span class="investment-range">
                                            $<?= number_format($match['investment_range_min'] ?? 50000) ?>K - $<?= number_format($match['investment_range_max'] ?? 500000) ?>K
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
                        <h4>No matches yet</h4>
                        <p>Complete your profile to start getting matched with investors!</p>
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
                        <div class="activity-icon activity-match">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">New investor match found</div>
                            <div class="activity-description">Sarah Johnson from TechVentures Capital</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon activity-profile">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Profile view</div>
                            <div class="activity-description">Anonymous investor viewed your profile</div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon activity-update">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Profile updated</div>
                            <div class="activity-description">You updated your funding requirements</div>
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
                    <a href="<?= url('search/investors') ?>" class="quick-action-item">
                        <div class="action-icon action-search">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="action-text">Find Investors</div>
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

        <!-- Startup Progress -->
        <div class="dashboard-card mb-4">
            <div class="card-header-enhanced">
                <h3 class="header-title">
                    <i class="fas fa-rocket me-3"></i>Startup Progress
                </h3>
            </div>
            <div class="card-body-enhanced">
                <div class="progress-item">
                    <div class="progress-label">
                        <span>Profile Completion</span>
                        <span class="progress-value">85%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 85%"></div>
                    </div>
                </div>
                
                <div class="progress-item">
                    <div class="progress-label">
                        <span>Investor Outreach</span>
                        <span class="progress-value">60%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 60%"></div>
                    </div>
                </div>
                
                <div class="progress-item">
                    <div class="progress-label">
                        <span>Documentation</span>
                        <span class="progress-value">40%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 40%"></div>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h5>Next Steps:</h5>
                    <ul class="steps-list">
                        <li>Upload pitch deck</li>
                        <li>Add financial projections</li>
                        <li>Complete team profiles</li>
                    </ul>
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
                        <div class="insight-title">Funding Activity Up</div>
                        <div class="insight-description">
                            <?= htmlspecialchars($startup['industry'] ?? 'Tech') ?> funding increased 23% this quarter
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="insight-content">
                        <div class="insight-title">Active Investors</div>
                        <div class="insight-description">
                            247 investors are actively looking in your space
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="insight-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="insight-content">
                        <div class="insight-title">Profile Tip</div>
                        <div class="insight-description">
                            Startups with video pitches get 3x more investor interest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

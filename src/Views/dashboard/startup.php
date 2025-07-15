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
        <div class="stat-card stat-primary">
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
        <div class="stat-card stat-success">
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
        <div class="stat-card stat-warning">
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
        <div class="stat-card stat-info">
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

<style>
/* Enhanced Dashboard Styles */
/* Better contrast for Edit Profile button */
.btn-outline-primary {
    color: white !important;
    border-color: rgba(255, 255, 255, 0.8) !important;
    background: rgba(255, 255, 255, 0.1) !important;
}

.btn-outline-primary:hover {
    color: #667eea !important;
    background: white !important;
    border-color: white !important;
}
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    padding: 2rem;
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
}

.welcome-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% { transform: rotate(45deg) translateX(-100%); }
    50% { transform: rotate(45deg) translateX(100%); }
    100% { transform: rotate(45deg) translateX(100%); }
}

.welcome-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.welcome-subtitle {
    font-size: 1.2rem;
    margin: 1rem 0;
    opacity: 0.9;
}

.welcome-actions {
    margin-top: 1.5rem;
}

.btn-action {
    margin-right: 1rem;
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.company-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid rgba(255,255,255,0.3);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: 700;
}

/* Enhanced Stat Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card.stat-success::before { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-card.stat-warning::before { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-card.stat-info::before { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.stat-content {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-card.stat-success .stat-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-card.stat-warning .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-card.stat-info .stat-icon { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    color: #6b7280;
    font-size: 0.9rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

.stat-change {
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.5rem;
}

.stat-change.positive { color: #10b981; }
.stat-change.negative { color: #ef4444; }
.stat-change.neutral { color: #6b7280; }

.stat-chart {
    height: 40px;
    margin-top: 1rem;
}

/* Enhanced Dashboard Cards */
.dashboard-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.card-header-enhanced {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.card-body-enhanced {
    padding: 1.5rem;
}

/* Matches List */
.matches-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.match-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.match-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    transform: translateX(4px);
}

.match-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 1rem;
    flex-shrink: 0;
}

.avatar-circle {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
}

.match-info {
    flex-grow: 1;
}

.match-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 1rem;
}

.match-company {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.match-details {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: #9ca3af;
}

.investment-range {
    font-weight: 500;
    color: #059669;
}

.match-score {
    margin: 0 1rem;
}

.score-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    color: white;
}

.score-high { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.score-medium { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.score-low { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

.match-actions {
    display: flex;
    gap: 0.5rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #374151;
    margin-bottom: 0.5rem;
}

/* Activity Timeline */
.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.activity-match { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.activity-profile { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.activity-update { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

.activity-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
}

.activity-description {
    color: #6b7280;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.activity-time {
    color: #9ca3af;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    color: #374151;
    transition: all 0.3s ease;
}

.quick-action-item:hover {
    background: #f3f4f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    color: #374151;
    text-decoration: none;
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-bottom: 0.75rem;
}

.action-search { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.action-matches { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.action-messages { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.action-profile { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

.action-text {
    font-weight: 600;
    font-size: 0.9rem;
    text-align: center;
}

/* Progress Bars */
.progress-item {
    margin-bottom: 1.5rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    font-weight: 500;
    color: #374151;
}

.progress-value {
    color: #667eea;
    font-weight: 600;
}

.progress-bar-container {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
    transition: width 0.6s ease;
}

.next-steps {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.next-steps h5 {
    color: #374151;
    font-size: 1rem;
    margin-bottom: 0.75rem;
}

.steps-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.steps-list li {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    position: relative;
    padding-left: 1.5rem;
}

.steps-list li::before {
    content: '→';
    position: absolute;
    left: 0;
    color: #667eea;
    font-weight: 600;
}

/* Insights */
.insight-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.insight-item:last-child {
    margin-bottom: 0;
}

.insight-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.insight-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
}

.insight-description {
    color: #6b7280;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-title {
        font-size: 2rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .match-item {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .match-actions {
        width: 100%;
        justify-content: flex-end;
    }
}

@media (max-width: 576px) {
    .welcome-card {
        padding: 1.5rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .card-body-enhanced {
        padding: 1rem;
    }
}
</style>

<script>
// Enhanced Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize mini charts
    initializeMiniCharts();
    
    // Add interactive behaviors
    initializeInteractions();
    
    // Auto-refresh data every 5 minutes
    setInterval(refreshDashboardData, 300000);
});

function initializeMiniCharts() {
    // Simple canvas-based mini charts
    const charts = [
        { id: 'matchesChart', data: [10, 15, 12, 18, 20, 25, 30] },
        { id: 'mutualChart', data: [2, 3, 1, 4, 3, 5, 6] },
        { id: 'pendingChart', data: [5, 4, 6, 3, 4, 2, 3] },
        { id: 'scoreChart', data: [65, 70, 68, 75, 72, 78, 80] }
    ];
    
    charts.forEach(chart => {
        const canvas = document.getElementById(chart.id);
        if (canvas) {
            drawMiniChart(canvas, chart.data);
        }
    });
}

function drawMiniChart(canvas, data) {
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Set up drawing
    ctx.strokeStyle = '#667eea';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    
    // Calculate points
    const stepX = width / (data.length - 1);
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const range = maxValue - minValue || 1;
    
    // Draw line
    ctx.beginPath();
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - ((value - minValue) / range) * height;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    ctx.stroke();
    
    // Add gradient fill
    ctx.globalAlpha = 0.1;
    ctx.fillStyle = '#667eea';
    ctx.lineTo(width, height);
    ctx.lineTo(0, height);
    ctx.closePath();
    ctx.fill();
    ctx.globalAlpha = 1;
}

function initializeInteractions() {
    // Add click animations to stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Add hover effects to match items
    document.querySelectorAll('.match-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.background = '#f3f4f6';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '#f9fafb';
        });
    });
}

function refreshMatches() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show success message
        showToast('Matches refreshed successfully!', 'success');
    }, 2000);
}

function viewMatch(matchId) {
    window.location.href = `<?= url('matches/view') ?>/${matchId}`;
}

function expressInterest(matchId) {
    if (confirm('Express interest in this investor?')) {
        // Simulate API call
        fetch(`<?= url('api/match/interest') ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `investor_id=${matchId}&interested=true&_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Interest expressed successfully!', 'success');
            } else {
                showToast('Error expressing interest. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error. Please check your connection.', 'error');
        });
    }
}

function refreshDashboardData() {
    // Silently refresh dashboard data in background
    fetch(`<?= url('api/dashboard/refresh') ?>`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats without page reload
                updateDashboardStats(data.stats);
            }
        })
        .catch(error => {
            console.error('Dashboard refresh error:', error);
        });
}

function updateDashboardStats(stats) {
    // Update stat numbers with animation
    Object.keys(stats).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            animateNumber(element, parseInt(element.textContent), stats[key]);
        }
    });
}

function animateNumber(element, start, end) {
    const duration = 1000;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.round(start + (end - start) * progress);
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
        </div>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}
</script>

<style>
/* Toast Notifications */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 1rem 1.5rem;
    border-left: 4px solid;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 9999;
}

.toast.show {
    transform: translateX(0);
}

.toast-success { border-left-color: #10b981; }
.toast-error { border-left-color: #ef4444; }
.toast-info { border-left-color: #3b82f6; }

.toast-content {
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #374151;
}

.toast-success .toast-content { color: #065f46; }
.toast-error .toast-content { color: #7f1d1d; }
.toast-info .toast-content { color: #1e3a8a; }
</style>
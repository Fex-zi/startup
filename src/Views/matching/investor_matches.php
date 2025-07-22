<!-- Enhanced Matches Header -->
<div class="matches-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="matches-title">
                <i class="fas fa-rocket me-3"></i>Startup Opportunities
            </h1>
            <p class="matches-subtitle">
                Promising startups matched to your investment criteria at <strong><?= htmlspecialchars($investor['company_name'] ?? 'your firm') ?></strong>
            </p>
        </div>
        <button class="btn btn-find-matches" data-match-action="generate">
            <i class="fas fa-sync me-2"></i>Find New Matches
        </button>
    </div>
</div>

<!-- Enhanced Statistics Grid -->
<div class="matches-stats-grid">
    <div class="matches-stat-card stat-primary">
        <div class="stat-content">
            <div class="stat-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="stat-number"><?= number_format($match_stats['total_matches'] ?? 0) ?></div>
            <div class="stat-label">Startup Matches</div>
        </div>
    </div>
    
    <div class="matches-stat-card stat-success">
        <div class="stat-content">
            <div class="stat-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="stat-number"><?= number_format($match_stats['mutual_matches'] ?? 0) ?></div>
            <div class="stat-label">Mutual Interest</div>
        </div>
    </div>
    
    <div class="matches-stat-card stat-warning">
        <div class="stat-content">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number"><?= number_format($match_stats['pending_matches'] ?? 0) ?></div>
            <div class="stat-label">Pending Reviews</div>
        </div>
    </div>
    
    <div class="matches-stat-card stat-info">
        <div class="stat-content">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-number"><?= round($match_stats['avg_match_score'] ?? 0) ?>%</div>
            <div class="stat-label">Avg Match Score</div>
        </div>
    </div>
</div>

<!-- Enhanced Match Tabs -->
<div class="matches-tabs-card">
    <div class="matches-tabs-header">
        <ul class="nav matches-nav-tabs" id="matchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                    All Matches 
                    <span class="tab-badge"><?= count($all_matches ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mutual-tab" data-bs-toggle="tab" data-bs-target="#mutual" type="button" role="tab">
                    Mutual Interest 
                    <span class="tab-badge"><?= count($mutual_matches ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    Due Diligence 
                    <span class="tab-badge"><?= count($pending_matches ?? []) ?></span>
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-4">
        <div class="tab-content" id="matchTabsContent">
            <!-- All Matches Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <?php if (!empty($all_matches)): ?>
                    <div class="row">
                        <?php foreach ($all_matches as $match): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="enhanced-match-card <?= 
                                    ($match['status'] ?? '') === 'mutual_interest' ? 'match-mutual' : 
                                    (($match['status'] ?? '') === 'pending' ? 'match-pending' : '') 
                                ?>">
                                    <?php if (($match['status'] ?? '') === 'mutual_interest'): ?>
                                        <div class="match-status-header status-mutual">
                                            <i class="fas fa-handshake"></i>Mutual Interest
                                        </div>
                                    <?php elseif (($match['status'] ?? '') === 'pending'): ?>
                                        <div class="match-status-header status-pending">
                                            <i class="fas fa-search"></i>Ready for Due Diligence
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="match-card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="match-avatar me-3">
                                                <?php if (!empty($match['logo_url'])): ?>
                                                    <img src="<?= htmlspecialchars($match['logo_url']) ?>" 
                                                         alt="<?= htmlspecialchars($match['company_name']) ?> Logo"
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="match-avatar-placeholder">
                                                        <?= strtoupper(substr($match['company_name'] ?? 'S', 0, 2)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="match-name">
                                                    <?= htmlspecialchars($match['company_name'] ?? 'Startup Company') ?>
                                                    <?php if (!empty($match['is_featured'])): ?>
                                                        <span class="status-badge status-featured ms-2">
                                                            <i class="fas fa-star"></i>Featured
                                                        </span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="match-company">
                                                    <i class="fas fa-user me-1"></i>
                                                    <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?> • 
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    <?= ucfirst(str_replace('_', ' ', $match['stage'] ?? 'early_stage')) ?>
                                                </p>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="match-score-badge <?= 
                                                        ($match['match_score'] ?? 0) >= 80 ? 'match-score-high' : 
                                                        (($match['match_score'] ?? 0) >= 60 ? 'match-score-medium' : 'match-score-low')
                                                    ?>">
                                                        <i class="fas fa-star"></i>
                                                        <?= $match['match_score'] ?? 0 ?>% Match
                                                    </span>
                                                    <span class="status-badge status-info">
                                                        <i class="fas fa-industry me-1"></i>
                                                        <?= htmlspecialchars($match['industry_name'] ?? 'Other') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <p class="card-description">
                                            <?php 
                                            $description = $match['description'] ?? 'Innovative startup with great potential for growth and investment returns.';
                                            echo htmlspecialchars(substr($description, 0, 120));
                                            if (strlen($description) > 120) echo '...';
                                            ?>
                                        </p>
                                        
                                        <!-- Match Details Grid -->
                                        <div class="match-details-grid">
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Funding Goal</span>
                                                <span class="match-detail-value">
                                                    $<?= number_format($match['funding_goal'] ?? 0) ?>
                                                </span>
                                            </div>
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Stage</span>
                                                <span class="match-detail-value">
                                                    <span class="status-badge status-secondary">
                                                        <?= ucfirst(str_replace('_', ' ', $match['stage'] ?? 'early_stage')) ?>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Location</span>
                                                <span class="match-detail-value">
                                                    <?= htmlspecialchars($match['location'] ?? 'N/A') ?>
                                                </span>
                                            </div>
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Founded</span>
                                                <span class="match-detail-value">
                                                    <?= date('M Y', strtotime($match['created_at'] ?? 'now')) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Business Model Tags -->
                                        <div class="match-reasons">
                                            <div class="match-reasons-title">Business Highlights:</div>
                                            <div class="match-reasons-list">
                                                <?php if (!empty($match['business_model'])): ?>
                                                    <span class="match-reason-tag">
                                                        <i class="fas fa-chart-bar"></i>
                                                        <?= ucfirst(str_replace('_', ' ', $match['business_model'])) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($match['revenue_model'])): ?>
                                                    <span class="match-reason-tag">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                        <?= ucfirst(str_replace('_', ' ', $match['revenue_model'])) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($match['team_size'])): ?>
                                                    <span class="match-reason-tag">
                                                        <i class="fas fa-users"></i>
                                                        <?= $match['team_size'] ?> Team Members
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Match Actions -->
                                        <div class="match-actions">
                                            <div class="d-flex gap-2">
                                                <a href="<?= url('matches/view/' . ($match['id'] ?? 0)) ?>" 
                                                   class="btn btn-sm match-action-btn btn-match-view">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                                <?php if (($match['status'] ?? '') === 'mutual_interest'): ?>
                                                    <a href="<?= url('messages/conversation/' . ($match['id'] ?? 0)) ?>" 
                                                       class="btn btn-sm match-action-btn btn-match-message">
                                                        <i class="fas fa-comments me-1"></i>Message
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if (($match['status'] ?? '') === 'pending' && ($match['investor_interested'] ?? null) === null): ?>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm match-action-btn btn-match-interest" 
                                                            data-match-action="interest" 
                                                            data-match-id="<?= $match['id'] ?? 0 ?>" 
                                                            data-interested="true">
                                                        <i class="fas fa-heart me-1"></i>Interested
                                                    </button>
                                                    <button class="btn btn-sm match-action-btn btn-match-decline" 
                                                            data-match-action="interest" 
                                                            data-match-id="<?= $match['id'] ?? 0 ?>" 
                                                            data-interested="false">
                                                        <i class="fas fa-times me-1"></i>Pass
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="matches-empty-state">
                        <div class="matches-empty-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4 class="matches-empty-title">No startup matches yet</h4>
                        <p class="matches-empty-description">
                            Update your investment criteria and preferences to start discovering promising startups in your focus areas!
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-find-matches" data-match-action="generate">
                                <i class="fas fa-sync me-2"></i>Find Matches
                            </button>
                            <a href="<?= url('profile/edit') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-user-edit me-2"></i>Update Criteria
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Mutual Interest Tab -->
            <div class="tab-pane fade" id="mutual" role="tabpanel">
                <?php if (!empty($mutual_matches)): ?>
                    <div class="row">
                        <?php foreach ($mutual_matches as $match): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="enhanced-match-card match-mutual">
                                    <div class="match-status-header status-mutual">
                                        <i class="fas fa-handshake"></i>Mutual Interest - Ready to Connect!
                                    </div>
                                    <div class="match-card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="match-avatar me-3">
                                                <?php if (!empty($match['logo_url'])): ?>
                                                    <img src="<?= htmlspecialchars($match['logo_url']) ?>" 
                                                         alt="<?= htmlspecialchars($match['company_name']) ?> Logo"
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="match-avatar-placeholder">
                                                        <?= strtoupper(substr($match['company_name'] ?? 'S', 0, 2)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="match-name">
                                                    <?= htmlspecialchars($match['company_name'] ?? 'Startup Company') ?>
                                                </h5>
                                                <p class="match-company">
                                                    <i class="fas fa-dollar-sign me-1"></i>
                                                    Seeking $<?= number_format($match['funding_goal'] ?? 0) ?>
                                                </p>
                                                <p class="text-success mb-0">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Ready to discuss investment opportunity!
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <a href="<?= url('messages/conversation/' . ($match['id'] ?? 0)) ?>" 
                                               class="btn btn-match-message">
                                                <i class="fas fa-comments me-2"></i>Start Conversation
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="matches-empty-state">
                        <div class="matches-empty-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4 class="matches-empty-title">No mutual interests yet</h4>
                        <p class="matches-empty-description">
                            When both you and a startup express interest, they'll appear here for investment discussions.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Due Diligence Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <?php if (!empty($pending_matches)): ?>
                    <div class="row">
                        <?php foreach ($pending_matches as $match): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="enhanced-match-card match-pending">
                                    <div class="match-status-header status-pending">
                                        <i class="fas fa-search"></i>Due Diligence Review
                                    </div>
                                    <div class="match-card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="match-avatar me-3">
                                                <?php if (!empty($match['logo_url'])): ?>
                                                    <img src="<?= htmlspecialchars($match['logo_url']) ?>" 
                                                         alt="<?= htmlspecialchars($match['company_name']) ?> Logo"
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="match-avatar-placeholder">
                                                        <?= strtoupper(substr($match['company_name'] ?? 'S', 0, 2)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="match-name">
                                                    <?= htmlspecialchars($match['company_name'] ?? 'Startup Company') ?>
                                                </h5>
                                                <p class="match-company">
                                                    <span class="match-score-badge">
                                                        <i class="fas fa-star"></i>
                                                        <?= $match['match_score'] ?? 0 ?>% Match
                                                    </span>
                                                </p>
                                                <p class="text-warning mb-0">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Awaiting your investment decision
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <div class="d-flex justify-content-center gap-3">
                                                <button class="btn btn-match-interest" 
                                                        data-match-action="interest" 
                                                        data-match-id="<?= $match['id'] ?? 0 ?>" 
                                                        data-interested="true">
                                                    <i class="fas fa-handshake me-1"></i>Invest
                                                </button>
                                                <button class="btn btn-match-decline" 
                                                        data-match-action="interest" 
                                                        data-match-id="<?= $match['id'] ?? 0 ?>" 
                                                        data-interested="false">
                                                    <i class="fas fa-times me-1"></i>Pass
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="matches-empty-state">
                        <div class="matches-empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="matches-empty-title">No pending reviews</h4>
                        <p class="matches-empty-description">
                            All startup opportunities have been reviewed. Excellent work on staying current with deal flow!
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

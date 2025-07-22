<!-- Enhanced Matches Header -->
<div class="matches-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="matches-title">
                <i class="fas fa-heart me-3"></i>Your Matches
            </h1>
            <p class="matches-subtitle">
                Investors interested in <strong><?= htmlspecialchars($startup['company_name'] ?? 'your startup') ?></strong>
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
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-number"><?= number_format($match_stats['total_matches'] ?? 0) ?></div>
            <div class="stat-label">Total Matches</div>
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
                    Pending 
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
                                            <i class="fas fa-clock"></i>Pending Response
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="match-card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="match-avatar me-3">
                                                <?php if (!empty($match['avatar_url'])): ?>
                                                    <img src="<?= htmlspecialchars($match['avatar_url']) ?>" 
                                                         alt="<?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>"
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="match-avatar-placeholder">
                                                        <?php 
                                                        $firstName = $match['first_name'] ?? '';
                                                        $lastName = $match['last_name'] ?? '';
                                                        echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="match-name">
                                                    <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>
                                                </h5>
                                                <?php if (!empty($match['investor_company'])): ?>
                                                    <p class="match-company">
                                                        <i class="fas fa-building me-1"></i>
                                                        <?= htmlspecialchars($match['investor_company']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="match-score-badge <?= 
                                                        ($match['match_score'] ?? 0) >= 80 ? 'match-score-high' : 
                                                        (($match['match_score'] ?? 0) >= 60 ? 'match-score-medium' : 'match-score-low')
                                                    ?>">
                                                        <i class="fas fa-star"></i>
                                                        <?= $match['match_score'] ?? 0 ?>% Match
                                                    </span>
                                                    <span class="status-badge status-info">
                                                        <?= ucfirst(str_replace('_', ' ', $match['investor_type'] ?? 'investor')) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <p class="card-description">
                                            <?php 
                                            $bio = $match['bio'] ?? 'Experienced investor looking for promising opportunities.';
                                            echo htmlspecialchars(substr($bio, 0, 120));
                                            if (strlen($bio) > 120) echo '...';
                                            ?>
                                        </p>
                                        
                                        <!-- Match Details Grid -->
                                        <div class="match-details-grid">
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Investment Range</span>
                                                <span class="match-detail-value">
                                                    $<?= number_format(($match['investment_range_min'] ?? 0) / 1000) ?>K - 
                                                    $<?= number_format(($match['investment_range_max'] ?? 0) / 1000) ?>K
                                                </span>
                                            </div>
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Matched Date</span>
                                                <span class="match-detail-value">
                                                    <?= date('M j, Y', strtotime($match['created_at'] ?? 'now')) ?>
                                                </span>
                                            </div>
                                            <div class="match-detail-item">
                                                <span class="match-detail-label">Status</span>
                                                <span class="match-detail-value">
                                                    <span class="status-badge <?= 
                                                        ($match['status'] ?? '') === 'mutual_interest' ? 'status-active' : 
                                                        (($match['status'] ?? '') === 'pending' ? 'status-warning' : 'status-secondary')
                                                    ?>">
                                                        <?php
                                                        $statusText = [
                                                            'pending' => 'Pending',
                                                            'mutual_interest' => 'Mutual Interest',
                                                            'startup_declined' => 'Declined',
                                                            'investor_declined' => 'Not Interested'
                                                        ];
                                                        echo $statusText[$match['status'] ?? 'pending'] ?? 'Unknown';
                                                        ?>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Match Reasons -->
                                        <?php if (!empty($match['match_reasons']) && is_array($match['match_reasons'])): ?>
                                            <div class="match-reasons">
                                                <div class="match-reasons-title">Why this is a good match:</div>
                                                <div class="match-reasons-list">
                                                    <?php foreach (array_slice($match['match_reasons'], 0, 3) as $reason): ?>
                                                        <span class="match-reason-tag">
                                                            <i class="fas fa-check-circle"></i>
                                                            <?= htmlspecialchars($reason) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
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
                                            
                                            <?php if (($match['status'] ?? '') === 'pending' && ($match['startup_interested'] ?? null) === null): ?>
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
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4 class="matches-empty-title">No matches yet</h4>
                        <p class="matches-empty-description">
                            Complete your startup profile to start getting matched with investors interested in your industry and stage!
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-find-matches" data-match-action="generate">
                                <i class="fas fa-sync me-2"></i>Find Matches
                            </button>
                            <a href="<?= url('profile/edit') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-user-edit me-2"></i>Complete Profile
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
                                                <?php if (!empty($match['avatar_url'])): ?>
                                                    <img src="<?= htmlspecialchars($match['avatar_url']) ?>" 
                                                         alt="<?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>"
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="match-avatar-placeholder">
                                                        <?php 
                                                        $firstName = $match['first_name'] ?? '';
                                                        $lastName = $match['last_name'] ?? '';
                                                        echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="match-name">
                                                    <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>
                                                </h5>
                                                <?php if (!empty($match['investor_company'])): ?>
                                                    <p class="match-company">
                                                        <i class="fas fa-building me-1"></i>
                                                        <?= htmlspecialchars($match['investor_company']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="text-success mb-0">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Ready to start a conversation!
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
                            When both you and an investor express interest, they'll appear here for easy connection.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pending Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <?php if (!empty($pending_matches)): ?>
                    <div class="row">
                        <?php foreach ($pending_matches as $match): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="enhanced-match-card match-pending">
                                    <div class="match-status-header status-pending">
                                        <i class="fas fa-clock"></i>Pending Your Response
                                    </div>
                                    <div class="match-card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="match-avatar me-3">
                                                <?php if (!empty($match['avatar_url'])): ?>
                                                    <img src="<?= htmlspecialchars($match['avatar_url']) ?>" 
                                                         alt="<?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>"
                                                         style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="match-avatar-placeholder">
                                                        <?php 
                                                        $firstName = $match['first_name'] ?? '';
                                                        $lastName = $match['last_name'] ?? '';
                                                        echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="match-name">
                                                    <?= htmlspecialchars(($match['first_name'] ?? '') . ' ' . ($match['last_name'] ?? '')) ?>
                                                </h5>
                                                <p class="match-company">
                                                    <span class="match-score-badge">
                                                        <i class="fas fa-star"></i>
                                                        <?= $match['match_score'] ?? 0 ?>% Match
                                                    </span>
                                                </p>
                                                <p class="text-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Waiting for your response
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <div class="d-flex justify-content-center gap-3">
                                                <button class="btn btn-match-interest" 
                                                        data-match-action="interest" 
                                                        data-match-id="<?= $match['id'] ?? 0 ?>" 
                                                        data-interested="true">
                                                    <i class="fas fa-heart me-1"></i>Interested
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
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="matches-empty-title">No pending matches</h4>
                        <p class="matches-empty-description">
                            All your matches have been reviewed. Great job staying on top of your opportunities!
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

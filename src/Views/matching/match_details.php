<!-- Match Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-handshake me-2"></i>Match Details
                            <span class="badge bg-primary ms-2"><?= $match['match_score'] ?>% Match</span>
                        </h4>
                        <p class="text-muted mb-0">
                            Detailed analysis and compatibility information
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= url('matches') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Matches
                        </a>
                        <?php if ($match['status'] === 'mutual_interest'): ?>
                            <a href="<?= url('messages/conversation/' . $match['id']) ?>" class="btn btn-success">
                                <i class="fas fa-comments me-2"></i>Start Conversation
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Match Status Alert -->
<div class="row mb-4">
    <div class="col-12">
        <?php
        $alertClass = [
            'pending' => 'warning',
            'mutual_interest' => 'success',
            'startup_declined' => 'danger',
            'investor_declined' => 'danger',
            'expired' => 'secondary'
        ];
        $alertIcon = [
            'pending' => 'clock',
            'mutual_interest' => 'check-circle',
            'startup_declined' => 'times-circle',
            'investor_declined' => 'times-circle',
            'expired' => 'hourglass-end'
        ];
        $alertText = [
            'pending' => 'This match is pending response from both parties.',
            'mutual_interest' => 'Congratulations! Both parties have expressed mutual interest.',
            'startup_declined' => 'The startup has declined this match.',
            'investor_declined' => 'The investor has declined this match.',
            'expired' => 'This match has expired due to inactivity.'
        ];
        ?>
        <div class="alert alert-<?= $alertClass[$match['status']] ?? 'info' ?> d-flex align-items-center">
            <i class="fas fa-<?= $alertIcon[$match['status']] ?? 'info-circle' ?> me-3 fa-lg"></i>
            <div class="flex-grow-1">
                <strong>Status: <?= ucfirst(str_replace('_', ' ', $match['status'])) ?></strong>
                <div><?= $alertText[$match['status']] ?? 'Unknown status' ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Left Column - Profile Information -->
    <div class="col-lg-8">
        <!-- Company/Investor Profile -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <?php if ($user_type === 'startup'): ?>
                        <i class="fas fa-user-tie me-2"></i>Investor Profile
                    <?php else: ?>
                        <i class="fas fa-rocket me-2"></i>Startup Profile
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($user_type === 'startup'): ?>
                    <!-- Investor Information -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0 me-4">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 80px; height: 80px; font-size: 1.5rem;">
                                <?= strtoupper(substr($match['investor_first_name'], 0, 1) . substr($match['investor_last_name'], 0, 1)) ?>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">
                                <?= htmlspecialchars($match['investor_first_name'] . ' ' . $match['investor_last_name']) ?>
                            </h4>
                            <?php if (!empty($match['investor_company'])): ?>
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-building me-1"></i>
                                    <?= htmlspecialchars($match['investor_company']) ?>
                                </h6>
                            <?php endif; ?>
                            <p class="mb-2">
                                <span class="badge bg-info me-2">
                                    <?= ucfirst(str_replace('_', ' ', $match['investor_type'])) ?>
                                </span>
                                <span class="badge bg-secondary">
                                    Investment Range: $<?= number_format($match['investment_range_min']) ?> - $<?= number_format($match['investment_range_max']) ?>
                                </span>
                            </p>
                            <p class="mb-0">
                                <?= nl2br(htmlspecialchars($match['investor_bio'] ?? 'No bio available.')) ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Startup Information -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0 me-4">
                            <?php if (!empty($match['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($match['logo_url']) ?>" 
                                     alt="<?= htmlspecialchars($match['company_name']) ?> Logo"
                                     class="rounded" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary rounded d-flex align-items-center justify-content-center text-white fw-bold" 
                                     style="width: 80px; height: 80px; font-size: 1.5rem;">
                                    <?= strtoupper(substr($match['company_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">
                                <?= htmlspecialchars($match['company_name']) ?>
                            </h4>
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user me-1"></i>
                                Founded by <?= htmlspecialchars($match['startup_first_name'] . ' ' . $match['startup_last_name']) ?>
                            </h6>
                            <p class="mb-2">
                                <span class="badge bg-info me-2">
                                    <?= htmlspecialchars($match['industry_name'] ?? 'Other') ?>
                                </span>
                                <span class="badge bg-secondary me-2">
                                    <?= ucfirst(str_replace('_', ' ', $match['stage'])) ?>
                                </span>
                                <span class="badge bg-success">
                                    Seeking $<?= number_format($match['funding_goal']) ?>
                                </span>
                            </p>
                            <p class="mb-0">
                                <?= nl2br(htmlspecialchars($match['startup_description'] ?? 'No description available.')) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Match Analysis -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Match Analysis
                </h5>
            </div>
            <div class="card-body">
                <!-- Match Score Breakdown -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Overall Match Score</span>
                        <span class="badge bg-primary fs-6"><?= $match['match_score'] ?>%</span>
                    </div>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-primary" 
                             role="progressbar" 
                             style="width: <?= $match['match_score'] ?>%"
                             aria-valuenow="<?= $match['match_score'] ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- Match Reasons -->
                <?php if (!empty($match['match_reasons'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Why This is a Good Match:</h6>
                        <div class="row">
                            <?php foreach ($match['match_reasons'] as $reason): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span><?= htmlspecialchars($reason) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Compatibility Metrics -->
                <div class="row">
                    <?php if ($user_type === 'startup'): ?>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-industry fa-2x text-primary mb-2"></i>
                                <h6>Industry Focus</h6>
                                <p class="text-muted mb-0">Investor has experience in your industry</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-coins fa-2x text-success mb-2"></i>
                                <h6>Investment Range</h6>
                                <p class="text-muted mb-0">Your funding goal fits their range</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                <h6>Stage Alignment</h6>
                                <p class="text-muted mb-0">They invest in your stage</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-target fa-2x text-primary mb-2"></i>
                                <h6>Investment Fit</h6>
                                <p class="text-muted mb-0">Funding goal matches your criteria</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-rocket fa-2x text-success mb-2"></i>
                                <h6>Growth Stage</h6>
                                <p class="text-muted mb-0">Company is in your preferred stage</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-handshake fa-2x text-info mb-2"></i>
                                <h6>Portfolio Fit</h6>
                                <p class="text-muted mb-0">Aligns with your investment strategy</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Action Buttons for Pending Matches -->
        <?php if ($match['status'] === 'pending'): ?>
            <?php
            $userInterested = null;
            if ($user_type === 'startup') {
                $userInterested = $match['startup_interested'];
            } else {
                $userInterested = $match['investor_interested'];
            }
            ?>
            
            <?php if ($userInterested === null): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-decision me-2"></i>Make Your Decision
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-4">
                            This looks like a great match! Are you interested in 
                            <?php if ($user_type === 'startup'): ?>
                                connecting with this investor?
                            <?php else: ?>
                                investing in this startup?
                            <?php endif; ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-success btn-lg" onclick="expressInterest(<?= $match['id'] ?>, true)">
                                <i class="fas fa-heart me-2"></i>
                                <?php if ($user_type === 'startup'): ?>
                                    Yes, I'm Interested
                                <?php else: ?>
                                    Yes, I Want to Invest
                                <?php endif; ?>
                            </button>
                            <button class="btn btn-outline-danger btn-lg" onclick="expressInterest(<?= $match['id'] ?>, false)">
                                <i class="fas fa-times me-2"></i>Not a Good Fit
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <?php if ($userInterested): ?>
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>You've expressed interest!</h5>
                            <p class="text-muted">
                                <?php if ($user_type === 'startup'): ?>
                                    We'll notify you if the investor is also interested.
                                <?php else: ?>
                                    We'll notify you if the startup is also interested.
                                <?php endif; ?>
                            </p>
                        <?php else: ?>
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <h5>You've declined this match</h5>
                            <p class="text-muted">This match won't appear in your active matches anymore.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Right Column - Quick Stats & Actions -->
    <div class="col-lg-4">
        <!-- Match Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Match Timeline
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Match Created</h6>
                            <small class="text-muted"><?= date('M j, Y \a\t g:i A', strtotime($match['created_at'])) ?></small>
                        </div>
                    </div>
                    
                    <?php if ($match['startup_interested'] !== null): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker <?= $match['startup_interested'] ? 'bg-success' : 'bg-danger' ?>"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    Startup <?= $match['startup_interested'] ? 'Interested' : 'Declined' ?>
                                </h6>
                                <small class="text-muted"><?= date('M j, Y', strtotime($match['updated_at'])) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($match['investor_interested'] !== null): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker <?= $match['investor_interested'] ? 'bg-success' : 'bg-danger' ?>"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">
                                    Investor <?= $match['investor_interested'] ? 'Interested' : 'Declined' ?>
                                </h6>
                                <small class="text-muted"><?= date('M j, Y', strtotime($match['updated_at'])) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($match['status'] === 'mutual_interest'): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Mutual Interest Achieved!</h6>
                                <small class="text-muted">Ready for next steps</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Key Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Key Information
                </h6>
            </div>
            <div class="card-body">
                <?php if ($user_type === 'startup'): ?>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Investment Range</span>
                            <strong>$<?= number_format($match['investment_range_min']) ?> - $<?= number_format($match['investment_range_max']) ?></strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Investor Type</span>
                            <strong><?= ucfirst(str_replace('_', ' ', $match['investor_type'])) ?></strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Funding Goal</span>
                            <strong>$<?= number_format($match['funding_goal']) ?></strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Stage</span>
                            <strong><?= ucfirst(str_replace('_', ' ', $match['stage'])) ?></strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Industry</span>
                            <strong><?= htmlspecialchars($match['industry_name'] ?? 'Other') ?></strong>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tools me-2"></i>Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($user_type === 'startup'): ?>
                        <a href="<?= url('profile/view/' . $match['investor_user_id']) ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user me-2"></i>View Full Profile
                        </a>
                    <?php else: ?>
                        <a href="<?= url('profile/view/' . $match['startup_user_id']) ?>" class="btn btn-outline-primary">
                            <i class="fas fa-building me-2"></i>View Company Profile
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($match['status'] === 'mutual_interest'): ?>
                        <a href="<?= url('messages/conversation/' . $match['id']) ?>" class="btn btn-success">
                            <i class="fas fa-comments me-2"></i>Start Conversation
                        </a>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Details
                    </button>
                    
                    <button class="btn btn-outline-info" onclick="shareMatch()">
                        <i class="fas fa-share me-2"></i>Share Match
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.stat-item {
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 10px;
}

.stat-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
</style>

<script>
function expressInterest(matchId, interested) {
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);
    
    fetch('<?= url('api/match/interest') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `match_id=${matchId}&interested=${interested}&_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.mutual_interest) {
                alert('Congratulations! Mutual interest achieved. You can now start conversations and move forward.');
            } else if (interested) {
                alert('Interest recorded successfully! We\'ll notify you if there\'s mutual interest.');
            } else {
                alert('Match declined. This will be removed from your active matches.');
            }
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        buttons.forEach(btn => btn.disabled = false);
    });
}

function shareMatch() {
    if (navigator.share) {
        navigator.share({
            title: 'Match Details',
            text: 'Check out this match on our platform',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}
</script>
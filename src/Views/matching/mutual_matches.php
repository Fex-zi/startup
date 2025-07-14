<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 text-white">
                            <i class="fas fa-handshake me-2"></i>Mutual Interest Matches
                        </h2>
                        <p class="mb-0 text-white-50">
                            <?php if ($user_type === 'startup'): ?>
                                Investors ready to discuss funding opportunities
                            <?php else: ?>
                                Startups ready for investment discussions
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="text-center">
                        <h3 class="text-white mb-0"><?= count($mutual_matches) ?></h3>
                        <small class="text-white-50">Active Connections</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Message -->
<?php if (!empty($mutual_matches)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-trophy fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">Congratulations!</h5>
                    <p class="mb-0">
                        You have <strong><?= count($mutual_matches) ?></strong> mutual interest 
                        <?= count($mutual_matches) === 1 ? 'match' : 'matches' ?>. 
                        These represent serious opportunities for collaboration.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-handshake fa-2x text-success mb-2"></i>
                <h3 class="text-success"><?= $stats['mutual_matches'] ?? 0 ?></h3>
                <p class="mb-0 text-muted">Mutual Interests</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-comments fa-2x text-info mb-2"></i>
                <h3 class="text-info"><?= count(array_filter($mutual_matches, function($m) { return !empty($m['last_message']); })) ?></h3>
                <p class="mb-0 text-muted">Active Conversations</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h3 class="text-warning"><?= count(array_filter($mutual_matches, function($m) { return empty($m['last_message']); })) ?></h3>
                <p class="mb-0 text-muted">Awaiting Contact</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-star fa-2x text-primary mb-2"></i>
                <h3 class="text-primary"><?= $stats['total_matches'] ?? 0 ?></h3>
                <p class="mb-0 text-muted">Total Matches</p>
            </div>
        </div>
    </div>
</div>

<!-- Mutual Matches List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Your Mutual Interest Matches
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportMatches()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterMatches('recent')">Recent Activity</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterMatches('no_messages')">No Messages Yet</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterMatches('high_potential')">High Potential</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="filterMatches('all')">Show All</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($mutual_matches)): ?>
                    <div class="row" id="matches-container">
                        <?php foreach ($mutual_matches as $match): ?>
                            <div class="col-lg-6 mb-4 match-item" data-match-id="<?= $match['id'] ?>">
                                <div class="card h-100 border-success shadow-sm mutual-match-card">
                                    <div class="card-header bg-success text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-check-circle me-2"></i>Mutual Interest Achieved
                                            </h6>
                                            <span class="badge bg-light text-success">
                                                <?= $match['match_score'] ?>% Match
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <?php if ($user_type === 'startup'): ?>
                                                    <!-- Investor Avatar -->
                                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                                         style="width: 60px; height: 60px; font-size: 1.2rem;">
                                                        <?= strtoupper(substr($match['investor_first_name'], 0, 1) . substr($match['investor_last_name'], 0, 1)) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Startup Logo -->
                                                    <?php if (!empty($match['logo_url'])): ?>
                                                        <img src="<?= htmlspecialchars($match['logo_url']) ?>" 
                                                             alt="<?= htmlspecialchars($match['company_name']) ?>"
                                                             class="rounded" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-primary rounded d-flex align-items-center justify-content-center text-white fw-bold" 
                                                             style="width: 60px; height: 60px; font-size: 1.2rem;">
                                                            <?= strtoupper(substr($match['company_name'], 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <?php if ($user_type === 'startup'): ?>
                                                    <h5 class="mb-1">
                                                        <?= htmlspecialchars($match['investor_first_name'] . ' ' . $match['investor_last_name']) ?>
                                                    </h5>
                                                    <?php if (!empty($match['investor_company'])): ?>
                                                        <p class="text-muted mb-1">
                                                            <i class="fas fa-building me-1"></i>
                                                            <?= htmlspecialchars($match['investor_company']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <p class="small text-success mb-0">
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        Investment Range: $<?= number_format($match['investment_range_min']) ?> - $<?= number_format($match['investment_range_max']) ?>
                                                    </p>
                                                <?php else: ?>
                                                    <h5 class="mb-1">
                                                        <?= htmlspecialchars($match['company_name']) ?>
                                                    </h5>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-user me-1"></i>
                                                        <?= htmlspecialchars($match['startup_first_name'] . ' ' . $match['startup_last_name']) ?>
                                                    </p>
                                                    <p class="small text-success mb-0">
                                                        <i class="fas fa-chart-line me-1"></i>
                                                        Seeking: $<?= number_format($match['funding_goal']) ?> • Stage: <?= ucfirst(str_replace('_', ' ', $match['stage'])) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Match Timeline -->
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-history me-1"></i>
                                                Mutual interest achieved <?= date('M j, Y', strtotime($match['updated_at'])) ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-2">
                                                <a href="<?= url('messages/conversation/' . $match['id']) ?>" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-comments me-1"></i>
                                                    <?php if (!empty($match['last_message'])): ?>
                                                        Continue Chat
                                                    <?php else: ?>
                                                        Start Conversation
                                                    <?php endif; ?>
                                                </a>
                                                <a href="<?= url('matches/view/' . $match['id']) ?>" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>Details
                                                </a>
                                            </div>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="scheduleCall(<?= $match['id'] ?>)">
                                                            <i class="fas fa-phone me-2"></i>Schedule Call
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="shareContact(<?= $match['id'] ?>)">
                                                            <i class="fas fa-share me-2"></i>Share Contact
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-muted" href="#" onclick="markAsCompleted(<?= $match['id'] ?>)">
                                                            <i class="fas fa-check me-2"></i>Mark Completed
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Footer -->
                                    <div class="card-footer bg-light">
                                        <small class="text-muted d-flex justify-content-between">
                                            <span>
                                                <?php if (!empty($match['last_message'])): ?>
                                                    <i class="fas fa-message me-1"></i>Last activity: <?= date('M j', strtotime($match['last_message_at'])) ?>
                                                <?php else: ?>
                                                    <i class="fas fa-clock me-1"></i>Ready for first contact
                                                <?php endif; ?>
                                            </span>
                                            <span class="badge bg-success">
                                                Priority Match
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Next Steps Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="mb-3">
                                        <i class="fas fa-lightbulb me-2 text-warning"></i>Next Steps
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3">
                                                <i class="fas fa-comments fa-2x text-primary mb-2"></i>
                                                <h6>Start Conversations</h6>
                                                <p class="small text-muted">
                                                    Reach out to matches that haven't been contacted yet
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3">
                                                <i class="fas fa-calendar fa-2x text-success mb-2"></i>
                                                <h6>Schedule Meetings</h6>
                                                <p class="small text-muted">
                                                    Move from messages to video calls or in-person meetings
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3">
                                                <i class="fas fa-handshake fa-2x text-warning mb-2"></i>
                                                <h6>Close Deals</h6>
                                                <p class="small text-muted">
                                                    Convert mutual interest into successful partnerships
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- No Mutual Matches State -->
                    <div class="text-center py-5">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h5>No Mutual Interest Matches Yet</h5>
                        <p class="text-muted mb-4">
                            Mutual interest matches appear when both you and a 
                            <?= $user_type === 'startup' ? 'potential investor' : 'startup' ?> 
                            express interest in working together.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="<?= url('matches') ?>" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>View All Matches
                            </a>
                            <?php if ($user_type === 'startup'): ?>
                                <a href="<?= url('search/investors') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-user-tie me-2"></i>Find Investors
                                </a>
                            <?php else: ?>
                                <a href="<?= url('search/startups') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-rocket me-2"></i>Find Startups
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.mutual-match-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.mutual-match-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
</style>

<script>
function filterMatches(filter) {
    const matches = document.querySelectorAll('.match-item');
    
    matches.forEach(match => {
        let show = true;
        
        switch(filter) {
            case 'recent':
                // Show matches with recent activity (last 7 days)
                show = match.querySelector('.card-footer').textContent.includes('Last activity');
                break;
            case 'no_messages':
                // Show matches without messages
                show = match.querySelector('.card-footer').textContent.includes('Ready for first contact');
                break;
            case 'high_potential':
                // Show matches with 80%+ match score
                const scoreText = match.querySelector('.badge').textContent;
                const score = parseInt(scoreText.match(/\d+/)[0]);
                show = score >= 80;
                break;
            case 'all':
            default:
                show = true;
                break;
        }
        
        match.style.display = show ? 'block' : 'none';
    });
}

function exportMatches() {
    // Create CSV export of mutual matches
    const matches = document.querySelectorAll('.match-item');
    let csv = 'Name,Company,Match Score,Status,Date\n';
    
    matches.forEach(match => {
        if (match.style.display !== 'none') {
            const name = match.querySelector('h5').textContent.trim();
            const company = match.querySelector('.text-muted')?.textContent.trim() || '';
            const score = match.querySelector('.badge').textContent.match(/\d+/)[0];
            const status = 'Mutual Interest';
            const date = match.querySelector('.card-footer small').textContent.split(':')[1]?.trim() || '';
            
            csv += `"${name}","${company}","${score}%","${status}","${date}"\n`;
        }
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'mutual_interest_matches.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function scheduleCall(matchId) {
    // Placeholder for scheduling functionality
    alert('Schedule call functionality would integrate with calendar systems');
}

function shareContact(matchId) {
    // Copy share link to clipboard
    const shareUrl = `${window.location.origin}/matches/view/${matchId}`;
    navigator.clipboard.writeText(shareUrl).then(() => {
        showToast('Match link copied to clipboard!', 'success');
    });
}

function markAsCompleted(matchId) {
    if (confirm('Mark this match as completed? This will archive it from your active matches.')) {
        // AJAX call to update match status
        fetch(`<?= url('api/match/complete') ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `match_id=${matchId}&_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Match marked as completed', 'success');
                location.reload();
            } else {
                showToast('Error updating match', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
}

function showToast(message, type) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}
</script>
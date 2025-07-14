<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">
                            <i class="fas fa-heart me-2"></i>Your Matches
                        </h2>
                        <p class="text-muted mb-0">
                            Investors interested in <strong><?= htmlspecialchars($startup['company_name']) ?></strong>
                        </p>
                    </div>
                    <button class="btn btn-primary" onclick="generateMatches()">
                        <i class="fas fa-sync me-2"></i>Find New Matches
                    </button>
                </div>
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

<!-- Match Tabs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="matchTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                            All Matches <span class="badge bg-primary ms-2"><?= count($all_matches) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mutual-tab" data-bs-toggle="tab" data-bs-target="#mutual" type="button" role="tab">
                            Mutual Interest <span class="badge bg-success ms-2"><?= count($mutual_matches) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                            Pending <span class="badge bg-warning ms-2"><?= count($pending_matches) ?></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="matchTabsContent">
                    <!-- All Matches Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <?php if (!empty($all_matches)): ?>
                            <div class="row">
                                <?php foreach ($all_matches as $match): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card match-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start mb-3">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                                             style="width: 50px; height: 50px;">
                                                            <?= strtoupper(substr($match['first_name'], 0, 1) . substr($match['last_name'], 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1">
                                                            <?= htmlspecialchars($match['first_name'] . ' ' . $match['last_name']) ?>
                                                            <span class="badge bg-primary ms-2"><?= $match['match_score'] ?>% Match</span>
                                                        </h5>
                                                        <?php if (!empty($match['investor_company'])): ?>
                                                            <p class="text-muted mb-1">
                                                                <i class="fas fa-building me-1"></i>
                                                                <?= htmlspecialchars($match['investor_company']) ?>
                                                            </p>
                                                        <?php endif; ?>
                                                        <p class="text-muted mb-0">
                                                            <small>
                                                                <span class="badge bg-info me-2">
                                                                    <?= ucfirst(str_replace('_', ' ', $match['investor_type'])) ?>
                                                                </span>
                                                                Status: 
                                                                <?php
                                                                $statusClass = [
                                                                    'pending' => 'warning',
                                                                    'mutual_interest' => 'success',
                                                                    'startup_declined' => 'danger',
                                                                    'investor_declined' => 'danger'
                                                                ];
                                                                $statusText = [
                                                                    'pending' => 'Pending',
                                                                    'mutual_interest' => 'Mutual Interest',
                                                                    'startup_declined' => 'Declined',
                                                                    'investor_declined' => 'Investor Declined'
                                                                ];
                                                                ?>
                                                                <span class="badge bg-<?= $statusClass[$match['status']] ?? 'secondary' ?>">
                                                                    <?= $statusText[$match['status']] ?? ucfirst($match['status']) ?>
                                                                </span>
                                                            </small>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <p class="mb-3">
                                                    <?= htmlspecialchars(substr($match['bio'] ?? '', 0, 100)) ?>
                                                    <?php if (strlen($match['bio'] ?? '') > 100): ?>...<?php endif; ?>
                                                </p>
                                                
                                                <!-- Match Reasons -->
                                                <?php if (!empty($match['match_reasons'])): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block mb-1">Match Reasons:</small>
                                                        <?php foreach ($match['match_reasons'] as $reason): ?>
                                                            <span class="badge bg-light text-dark me-1 mb-1">
                                                                <i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($reason) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="row text-center mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Investment Range</small>
                                                        <strong>
                                                            $<?= number_format($match['investment_range_min']) ?> - 
                                                            $<?= number_format($match['investment_range_max']) ?>
                                                        </strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Matched</small>
                                                        <strong><?= date('M j, Y', strtotime($match['created_at'])) ?></strong>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex gap-2">
                                                        <a href="<?= url('matches/view/' . $match['id']) ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>View Details
                                                        </a>
                                                        <?php if ($match['status'] === 'mutual_interest'): ?>
                                                            <a href="<?= url('messages/conversation/' . $match['id']) ?>" 
                                                               class="btn btn-sm btn-success">
                                                                <i class="fas fa-comments me-1"></i>Message
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <?php if ($match['status'] === 'pending' && $match['startup_interested'] === null): ?>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-success" 
                                                                    onclick="expressInterest(<?= $match['id'] ?>, true)">
                                                                <i class="fas fa-heart me-1"></i>Interested
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" 
                                                                    onclick="expressInterest(<?= $match['id'] ?>, false)">
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
                            <div class="text-center py-5">
                                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                                <h5>No matches yet</h5>
                                <p class="text-muted">Click "Find New Matches" to discover investors interested in your startup.</p>
                                <button class="btn btn-primary" onclick="generateMatches()">
                                    <i class="fas fa-sync me-2"></i>Find Matches
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mutual Interest Tab -->
                    <div class="tab-pane fade" id="mutual" role="tabpanel">
                        <?php if (!empty($mutual_matches)): ?>
                            <div class="row">
                                <?php foreach ($mutual_matches as $match): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card match-card h-100 border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-handshake me-2"></i>Mutual Interest
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <!-- Similar content structure as above but with emphasis on mutual interest -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                                             style="width: 50px; height: 50px;">
                                                            <?= strtoupper(substr($match['first_name'], 0, 1) . substr($match['last_name'], 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1">
                                                            <?= htmlspecialchars($match['first_name'] . ' ' . $match['last_name']) ?>
                                                        </h5>
                                                        <p class="text-muted mb-0">
                                                            Ready to connect! Start a conversation.
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-center">
                                                    <a href="<?= url('messages/conversation/' . $match['id']) ?>" 
                                                       class="btn btn-success">
                                                        <i class="fas fa-comments me-2"></i>Start Conversation
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                                <h5>No mutual interests yet</h5>
                                <p class="text-muted">When both you and an investor express interest, they'll appear here.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pending Tab -->
                    <div class="tab-pane fade" id="pending" role="tabpanel">
                        <?php if (!empty($pending_matches)): ?>
                            <div class="row">
                                <?php foreach ($pending_matches as $match): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card match-card h-100 border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-clock me-2"></i>Pending Response
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <!-- Content for pending matches with call to action -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold" 
                                                             style="width: 50px; height: 50px;">
                                                            <?= strtoupper(substr($match['first_name'], 0, 1) . substr($match['last_name'], 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1">
                                                            <?= htmlspecialchars($match['first_name'] . ' ' . $match['last_name']) ?>
                                                        </h5>
                                                        <p class="text-muted mb-0">
                                                            <?= $match['match_score'] ?>% match - Waiting for your response
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-success" 
                                                            onclick="expressInterest(<?= $match['id'] ?>, true)">
                                                        <i class="fas fa-heart me-1"></i>Interested
                                                    </button>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="expressInterest(<?= $match['id'] ?>, false)">
                                                        <i class="fas fa-times me-1"></i>Pass
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <h5>No pending matches</h5>
                                <p class="text-muted">All matches have been reviewed.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.match-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.match-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
</style>

<script>
function generateMatches() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Finding Matches...';
    btn.disabled = true;
    
    fetch('<?= url('api/match/find') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Great! Found ${data.matches_created} new matches. Refreshing page...`);
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
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function expressInterest(matchId, interested) {
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
                alert('Mutual interest! You can now start a conversation.');
            } else {
                alert(data.message);
            }
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>
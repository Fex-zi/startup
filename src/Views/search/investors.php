<!-- Search Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-search me-2"></i>Find Investors
                </h4>
                <p class="text-muted mb-0">Connect with investors looking for opportunities</p>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="<?= url('search/investors') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Investor name, company..."
                                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="investor_type" class="form-label">Investor Type</label>
                            <select class="form-select" id="investor_type" name="investor_type">
                                <option value="">All Types</option>
                                <option value="angel" <?= ($filters['investor_type'] ?? '') === 'angel' ? 'selected' : '' ?>>Angel Investor</option>
                                <option value="vc_firm" <?= ($filters['investor_type'] ?? '') === 'vc_firm' ? 'selected' : '' ?>>VC Firm</option>
                                <option value="corporate" <?= ($filters['investor_type'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Investor</option>
                                <option value="family_office" <?= ($filters['investor_type'] ?? '') === 'family_office' ? 'selected' : '' ?>>Family Office</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="industry" class="form-label">Industry Focus</label>
                            <select class="form-select" id="industry" name="industry">
                                <option value="">All Industries</option>
                                <?php foreach ($industries as $industry): ?>
                                    <option value="<?= $industry['id'] ?>" 
                                            <?= ($filters['industry'] ?? '') == $industry['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($industry['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="location" 
                                   name="location" 
                                   placeholder="City, State..."
                                   value="<?= htmlspecialchars($filters['location'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="investment_min" class="form-label">Min Investment</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="investment_min" 
                                   name="investment_min" 
                                   placeholder="e.g. 50000"
                                   step="10000"
                                   value="<?= htmlspecialchars($filters['investment_min'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="investment_max" class="form-label">Max Investment</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="investment_max" 
                                   name="investment_max" 
                                   placeholder="e.g. 500000"
                                   step="10000"
                                   value="<?= htmlspecialchars($filters['investment_max'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Availability</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actively_investing" name="actively_investing" value="1" 
                                       <?= (!empty($filters['actively_investing'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="actively_investing">
                                    Actively investing only
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-dollar-sign me-2"></i>
                    Investors 
                    <span class="badge bg-primary"><?= $pagination['total'] ?? 0 ?></span>
                </h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Sort by: Newest</option>
                        <option>Sort by: Investment Range</option>
                        <option>Sort by: Type</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($investors)): ?>
                    <div class="row">
                        <?php foreach ($investors as $investor): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 investor-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <?php if (!empty($investor['avatar_url'])): ?>
                                                    <img src="<?= htmlspecialchars($investor['avatar_url']) ?>" 
                                                         alt="<?= htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']) ?>"
                                                         class="rounded-circle" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                                         style="width: 60px; height: 60px; font-size: 1.2rem;">
                                                        <?= strtoupper(substr($investor['first_name'], 0, 1) . substr($investor['last_name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">
                                                    <?= htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']) ?>
                                                    <?php if ($investor['availability_status'] === 'actively_investing'): ?>
                                                        <span class="badge bg-success ms-2">
                                                            <i class="fas fa-check-circle me-1"></i>Active
                                                        </span>
                                                    <?php endif; ?>
                                                </h5>
                                                <?php if (!empty($investor['company_name'])): ?>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-building me-1"></i>
                                                        <?= htmlspecialchars($investor['company_name']) ?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="text-muted mb-0">
                                                    <small>
                                                        <span class="badge bg-info me-2">
                                                            <?= ucfirst(str_replace('_', ' ', $investor['investor_type'])) ?>
                                                        </span>
                                                        <?php if (!empty($investor['location'])): ?>
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            <?= htmlspecialchars($investor['location']) ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <p class="mb-3">
                                            <?= htmlspecialchars(substr($investor['bio'] ?? '', 0, 120)) ?>
                                            <?php if (strlen($investor['bio'] ?? '') > 120): ?>...<?php endif; ?>
                                        </p>
                                        
                                        <div class="row text-center mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Investment Range</small>
                                                <strong>
                                                    <?php 
                                                    $min = $investor['investment_range_min'];
                                                    $max = $investor['investment_range_max'];
                                                    echo '$' . number_format($min) . ' - $' . number_format($max);
                                                    ?>
                                                </strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Focus Areas</small>
                                                <?php 
                                                $preferredIndustries = json_decode($investor['preferred_industries'], true) ?? [];
                                                if (!empty($preferredIndustries)): 
                                                ?>
                                                    <span class="badge bg-secondary">
                                                        <?= count($preferredIndustries) ?> Industries
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Investment Stages -->
                                        <?php 
                                        $investmentStages = json_decode($investor['investment_stages'], true) ?? [];
                                        if (!empty($investmentStages)): 
                                        ?>
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">Investment Stages:</small>
                                                <?php foreach (array_slice($investmentStages, 0, 3) as $stage): ?>
                                                    <span class="badge bg-light text-dark me-1">
                                                        <?= ucfirst(str_replace('_', ' ', $stage)) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                                <?php if (count($investmentStages) > 3): ?>
                                                    <span class="badge bg-light text-dark">+<?= count($investmentStages) - 3 ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Joined <?= date('M Y', strtotime($investor['created_at'])) ?>
                                            </small>
                                            <div class="d-flex gap-2">
                                                <?php if (!empty($investor['linkedin_url'])): ?>
                                                    <a href="<?= htmlspecialchars($investor['linkedin_url']) ?>" 
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fab fa-linkedin me-1"></i>LinkedIn
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= url('profile/view/' . $investor['user_id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <?php if ($currentUser['user_type'] === 'startup'): ?>
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="expressInterest(<?= $investor['id'] ?>, 'investor')">
                                                        <i class="fas fa-handshake me-1"></i>Connect
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($pagination['last_page'] > 1): ?>
                        <nav aria-label="Search results pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1])) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++): ?>
                                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1])) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5>No investors found</h5>
                        <p class="text-muted">Try adjusting your search criteria or browse all investors.</p>
                        <a href="<?= url('search/investors') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-refresh me-2"></i>Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.investor-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.investor-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
</style>

<script>
function expressInterest(investorId, type) {
    // This would be implemented with AJAX to the matching API
    fetch('<?= url('api/match/interest') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `investor_id=${investorId}&interested=true&_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Connection request sent! We\'ll notify you if there\'s mutual interest.');
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
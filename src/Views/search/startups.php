<!-- Search Header -->
 <style></style>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-search me-2"></i>Find Startups
                </h4>
                <p class="text-muted mb-0">Discover promising startups looking for investment</p>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="<?= url('search/startups') ?>" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Company name, description..."
                                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="industry" class="form-label">Industry</label>
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
                            <label for="stage" class="form-label">Stage</label>
                            <select class="form-select" id="stage" name="stage">
                                <option value="">All Stages</option>
                                <option value="idea" <?= ($filters['stage'] ?? '') === 'idea' ? 'selected' : '' ?>>Idea</option>
                                <option value="prototype" <?= ($filters['stage'] ?? '') === 'prototype' ? 'selected' : '' ?>>Prototype</option>
                                <option value="mvp" <?= ($filters['stage'] ?? '') === 'mvp' ? 'selected' : '' ?>>MVP</option>
                                <option value="early_revenue" <?= ($filters['stage'] ?? '') === 'early_revenue' ? 'selected' : '' ?>>Early Revenue</option>
                                <option value="growth" <?= ($filters['stage'] ?? '') === 'growth' ? 'selected' : '' ?>>Growth</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
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
                            <label for="funding_min" class="form-label">Min Funding</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="funding_min" 
                                   name="funding_min" 
                                   placeholder="e.g. 100000"
                                   step="10000"
                                   value="<?= htmlspecialchars($filters['funding_min'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="funding_max" class="form-label">Max Funding</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="funding_max" 
                                   name="funding_max" 
                                   placeholder="e.g. 1000000"
                                   step="10000"
                                   value="<?= htmlspecialchars($filters['funding_max'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="funding_type" class="form-label">Funding Type</label>
                            <select class="form-select" id="funding_type" name="funding_type">
                                <option value="">All Types</option>
                                <option value="seed" <?= ($filters['funding_type'] ?? '') === 'seed' ? 'selected' : '' ?>>Seed</option>
                                <option value="series_a" <?= ($filters['funding_type'] ?? '') === 'series_a' ? 'selected' : '' ?>>Series A</option>
                                <option value="series_b" <?= ($filters['funding_type'] ?? '') === 'series_b' ? 'selected' : '' ?>>Series B</option>
                                <option value="debt" <?= ($filters['funding_type'] ?? '') === 'debt' ? 'selected' : '' ?>>Debt</option>
                                <option value="grant" <?= ($filters['funding_type'] ?? '') === 'grant' ? 'selected' : '' ?>>Grant</option>
                            </select>
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
                    <i class="fas fa-rocket me-2"></i>
                    Startups 
                    <span class="badge bg-primary"><?= $pagination['total'] ?? 0 ?></span>
                </h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Sort by: Newest</option>
                        <option>Sort by: Funding Goal</option>
                        <option>Sort by: Stage</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($startups)): ?>
                    <div class="row">
                        <?php foreach ($startups as $startup): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 startup-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <?php if (!empty($startup['logo_url'])): ?>
                                                    <img src="<?= htmlspecialchars($startup['logo_url']) ?>" 
                                                         alt="<?= htmlspecialchars($startup['company_name']) ?> Logo"
                                                         class="rounded" 
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-primary rounded d-flex align-items-center justify-content-center text-white fw-bold" 
                                                         style="width: 60px; height: 60px; font-size: 1.5rem;">
                                                        <?= strtoupper(substr($startup['company_name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">
                                                    <?= htmlspecialchars($startup['company_name']) ?>
                                                    <?php if ($startup['is_featured']): ?>
                                                        <span class="badge bg-warning text-dark ms-2">Featured</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="text-muted mb-1">
                                                    <small>
                                                        <i class="fas fa-user me-1"></i>
                                                        <?= htmlspecialchars($startup['first_name'] . ' ' . $startup['last_name']) ?>
                                                    </small>
                                                </p>
                                                <p class="text-muted mb-0">
                                                    <small>
                                                        <i class="fas fa-industry me-1"></i>
                                                        <?= htmlspecialchars($startup['industry_name'] ?? 'Other') ?>
                                                        <span class="mx-2">•</span>
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        <?= htmlspecialchars($startup['location'] ?? 'Not specified') ?>
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <p class="mb-3">
                                            <?= htmlspecialchars(substr($startup['description'] ?? '', 0, 120)) ?>
                                            <?php if (strlen($startup['description'] ?? '') > 120): ?>...<?php endif; ?>
                                        </p>
                                        
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Stage</small>
                                                <span class="badge bg-info">
                                                    <?= ucfirst(str_replace('_', ' ', $startup['stage'])) ?>
                                                </span>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Funding Goal</small>
                                                <strong>$<?= number_format($startup['funding_goal'] ?? 0) ?></strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Type</small>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst(str_replace('_', ' ', $startup['funding_type'] ?? '')) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('M j, Y', strtotime($startup['created_at'])) ?>
                                            </small>
                                            <div class="d-flex gap-2">
                                                <a href="<?= url('profile/view/' . $startup['user_id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <?php if ($currentUser['user_type'] === 'investor'): ?>
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="expressInterest(<?= $startup['id'] ?>, 'startup')">
                                                        <i class="fas fa-heart me-1"></i>Interested
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
                        <h5>No startups found</h5>
                        <p class="text-muted">Try adjusting your search criteria or browse all startups.</p>
                        <a href="<?= url('search/startups') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-refresh me-2"></i>Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.startup-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.startup-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
</style>

<script>
function expressInterest(startupId, type) {
    // This would be implemented with AJAX to the matching API
    fetch('<?= url('api/match/interest') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `startup_id=${startupId}&interested=true&_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Interest recorded! We\'ll notify you if there\'s mutual interest.');
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
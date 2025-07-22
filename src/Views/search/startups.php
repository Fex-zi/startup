<!-- Enhanced Search Header -->
<div class="search-header">
    <h1 class="search-title">
        <i class="fas fa-rocket me-3"></i>Find Startups
    </h1>
    <p class="search-subtitle">Discover promising startups looking for investment opportunities</p>
</div>

<!-- Enhanced Search Form -->
<div class="search-form-card">
    <div class="search-form-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Search & Filter Startups
        </h5>
    </div>
    <div class="search-form-body">
        <form method="GET" action="<?= url('search/startups') ?>" class="search-form">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           placeholder="Company name, description..."
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="industry" class="form-label">
                        <i class="fas fa-industry me-1"></i>Industry
                    </label>
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
                    <label for="stage" class="form-label">
                        <i class="fas fa-chart-line me-1"></i>Stage
                    </label>
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
                    <label for="location" class="form-label">
                        <i class="fas fa-map-marker-alt me-1"></i>Location
                    </label>
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
                    <label for="funding_min" class="form-label">
                        <i class="fas fa-dollar-sign me-1"></i>Min Funding Goal
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="funding_min" 
                           name="funding_min" 
                           placeholder="e.g. 100000"
                           step="10000"
                           value="<?= htmlspecialchars($filters['funding_min'] ?? '') ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="funding_max" class="form-label">
                        <i class="fas fa-dollar-sign me-1"></i>Max Funding Goal
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="funding_max" 
                           name="funding_max" 
                           placeholder="e.g. 1000000"
                           step="10000"
                           value="<?= htmlspecialchars($filters['funding_max'] ?? '') ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="funding_type" class="form-label">
                        <i class="fas fa-coins me-1"></i>Funding Type
                    </label>
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
                    <button type="submit" class="btn search-btn w-100">
                        <i class="fas fa-search me-2"></i>Find Startups
                    </button>
                </div>
            </div>
            
            <!-- Active Filters Display -->
            <div class="filter-tags" style="display: none;"></div>
        </form>
    </div>
</div>

<!-- Enhanced Results Section -->
<div class="results-card">
    <div class="results-header d-flex justify-content-between align-items-center">
        <div class="results-title">
            <i class="fas fa-rocket me-2"></i>
            Startups
            <span class="results-count"><?= number_format(count($startups ?? [])) ?></span>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-sm btn-outline-primary" onclick="refreshResults()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <select class="sort-select" onchange="sortResults(this.value)">
                <option value="newest">Sort by: Newest</option>
                <option value="funding_goal">Sort by: Funding Goal</option>
                <option value="stage">Sort by: Stage</option>
                <option value="location">Sort by: Location</option>
            </select>
        </div>
    </div>
    <div class="card-body p-4">
        <?php if (!empty($startups)): ?>
            <div class="row">
                <?php foreach ($startups as $startup): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="search-result-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="card-avatar me-3">
                                        <?php if (!empty($startup['logo_url'])): ?>
                                            <img src="<?= htmlspecialchars($startup['logo_url']) ?>" 
                                                 alt="<?= htmlspecialchars($startup['company_name']) ?> Logo"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <?= strtoupper(substr($startup['company_name'], 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">
                                            <?= htmlspecialchars($startup['company_name']) ?>
                                            <?php if ($startup['is_featured']): ?>
                                                <span class="status-badge status-featured ms-2">
                                                    <i class="fas fa-star"></i>Featured
                                                </span>
                                            <?php endif; ?>
                                        </h5>
                                        <p class="card-subtitle">
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($startup['first_name'] . ' ' . $startup['last_name']) ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <span class="status-badge status-info">
                                                <i class="fas fa-industry me-1"></i>
                                                <?= htmlspecialchars($startup['industry_name'] ?? 'Other') ?>
                                            </span>
                                            <?php if (!empty($startup['location'])): ?>
                                                <span class="text-muted small">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?= htmlspecialchars($startup['location']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="card-description">
                                    <?php 
                                    $description = $startup['description'] ?? 'Innovative startup with great potential for growth and success.';
                                    echo htmlspecialchars(substr($description, 0, 150));
                                    if (strlen($description) > 150) echo '...';
                                    ?>
                                </p>
                                
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-label">Stage</span>
                                        <span class="stat-value">
                                            <span class="status-badge status-secondary">
                                                <?= ucfirst(str_replace('_', ' ', $startup['stage'])) ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Funding Goal</span>
                                        <span class="stat-value">
                                            $<?= number_format($startup['funding_goal'] ?? 0) ?>
                                        </span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Type</span>
                                        <span class="stat-value">
                                            <span class="status-badge status-light">
                                                <?= ucfirst(str_replace('_', ' ', $startup['funding_type'] ?? 'seed')) ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Business Model & Traction -->
                                <div class="industry-tags">
                                    <?php if (!empty($startup['business_model'])): ?>
                                        <span class="industry-tag">
                                            <i class="fas fa-chart-bar me-1"></i>
                                            <?= ucfirst(str_replace('_', ' ', $startup['business_model'])) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($startup['revenue_model'])): ?>
                                        <span class="industry-tag">
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                            <?= ucfirst(str_replace('_', ' ', $startup['revenue_model'])) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($startup['team_size'])): ?>
                                        <span class="industry-tag">
                                            <i class="fas fa-users me-1"></i>
                                            <?= $startup['team_size'] ?> Team Members
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-meta">
                                    <div class="meta-info">
                                        <i class="fas fa-calendar-alt"></i>
                                        Founded <?= date('M Y', strtotime($startup['created_at'])) ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm action-btn btn-view" 
                                                onclick="viewProfile(<?= $startup['user_id'] ?>)">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                        <?php if ($currentUser['user_type'] === 'investor'): ?>
                                            <button class="btn btn-sm action-btn btn-connect" 
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
            
            <!-- Enhanced Pagination -->
            <?php if ($pagination['last_page'] > 1): ?>
                <div class="pagination-wrapper">
                    <nav aria-label="Search results pagination">
                        <ul class="pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1])) ?>">
                                        <i class="fas fa-chevron-left me-1"></i>Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1])) ?>">
                                        Next<i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h4 class="empty-title">No startups found</h4>
                <p class="empty-description">
                    We couldn't find any startups matching your criteria. Try adjusting your filters or search terms to discover more innovative companies.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="<?= url('search/startups') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-refresh me-2"></i>Clear All Filters
                    </a>
                    <a href="<?= url('dashboard') ?>" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

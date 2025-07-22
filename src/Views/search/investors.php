<!-- Enhanced Search Header -->
<div class="search-header">
    <h1 class="search-title">
        <i class="fas fa-search me-3"></i>Find Investors
    </h1>
    <p class="search-subtitle">Connect with investors looking for promising opportunities like yours</p>
</div>

<!-- Enhanced Search Form -->
<div class="search-form-card">
    <div class="search-form-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Search & Filter Investors
        </h5>
    </div>
    <div class="search-form-body">
        <form method="GET" action="<?= url('search/investors') ?>" class="search-form">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           placeholder="Investor name, company..."
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="investor_type" class="form-label">
                        <i class="fas fa-building me-1"></i>Investor Type
                    </label>
                    <select class="form-select" id="investor_type" name="investor_type">
                        <option value="">All Types</option>
                        <option value="angel" <?= ($filters['investor_type'] ?? '') === 'angel' ? 'selected' : '' ?>>Angel Investor</option>
                        <option value="vc_firm" <?= ($filters['investor_type'] ?? '') === 'vc_firm' ? 'selected' : '' ?>>VC Firm</option>
                        <option value="corporate" <?= ($filters['investor_type'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Investor</option>
                        <option value="family_office" <?= ($filters['investor_type'] ?? '') === 'family_office' ? 'selected' : '' ?>>Family Office</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="industry" class="form-label">
                        <i class="fas fa-industry me-1"></i>Industry Focus
                    </label>
                    <select class="form-select" id="industry" name="industry">
                        <option value="">All Industries</option>
                        <?php if (!empty($industries) && is_array($industries)): ?>
                            <?php foreach ($industries as $industry): ?>
                                <option value="<?= htmlspecialchars($industry['id'] ?? '') ?>" 
                                        <?= ($filters['industry'] ?? '') == ($industry['id'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($industry['name'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
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
                    <label for="investment_min" class="form-label">
                        <i class="fas fa-dollar-sign me-1"></i>Min Investment
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="investment_min" 
                           name="investment_min" 
                           placeholder="e.g. 50000"
                           step="10000"
                           value="<?= htmlspecialchars($filters['investment_min'] ?? '') ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="investment_max" class="form-label">
                        <i class="fas fa-dollar-sign me-1"></i>Max Investment
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="investment_max" 
                           name="investment_max" 
                           placeholder="e.g. 500000"
                           step="10000"
                           value="<?= htmlspecialchars($filters['investment_max'] ?? '') ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        <i class="fas fa-check-circle me-1"></i>Availability
                    </label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="actively_investing" name="actively_investing" value="1" 
                               <?= (!empty($filters['actively_investing'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="actively_investing">
                            Actively investing only
                        </label>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn search-btn w-100">
                        <i class="fas fa-search me-2"></i>Find Investors
                    </button>
                </div>
            </div>
            
            <!-- Active Filters Display -->
            <div class="filter-tags" style="display: none;"></div>
        </form>
    </div>
</div>

<!-- Error Display -->
<?php if (!empty($error)): ?>
    <div class="alert alert-warning rounded-3 shadow-sm">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Enhanced Results Section -->
<div class="results-card">
    <div class="results-header d-flex justify-content-between align-items-center">
        <div class="results-title">
            <i class="fas fa-dollar-sign me-2"></i>
            Investors
            <span class="results-count"><?= number_format(count($investors ?? [])) ?></span>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-sm btn-outline-primary" onclick="refreshResults()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <select class="sort-select" onchange="sortResults(this.value)">
                <option value="newest">Sort by: Newest</option>
                <option value="investment_range">Sort by: Investment Range</option>
                <option value="type">Sort by: Type</option>
                <option value="location">Sort by: Location</option>
            </select>
        </div>
    </div>
    <div class="card-body p-4">
        <?php if (!empty($investors) && is_array($investors)): ?>
            <div class="row">
                <?php foreach ($investors as $investor): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="search-result-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="card-avatar me-3">
                                        <?php if (!empty($investor['avatar_url'])): ?>
                                            <img src="<?= htmlspecialchars($investor['avatar_url']) ?>" 
                                                 alt="<?= htmlspecialchars(($investor['first_name'] ?? '') . ' ' . ($investor['last_name'] ?? '')) ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <?php 
                                                $firstName = $investor['first_name'] ?? '';
                                                $lastName = $investor['last_name'] ?? '';
                                                echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">
                                            <?= htmlspecialchars(($investor['first_name'] ?? '') . ' ' . ($investor['last_name'] ?? '')) ?>
                                            <?php if (($investor['availability_status'] ?? '') === 'actively_investing'): ?>
                                                <span class="status-badge status-active ms-2">
                                                    <i class="fas fa-check-circle"></i>Active
                                                </span>
                                            <?php endif; ?>
                                        </h5>
                                        <?php if (!empty($investor['company_name'])): ?>
                                            <p class="card-subtitle">
                                                <i class="fas fa-building me-1"></i>
                                                <?= htmlspecialchars($investor['company_name']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <?php if (!empty($investor['investor_type'])): ?>
                                                <span class="status-badge status-info">
                                                    <?= ucfirst(str_replace('_', ' ', $investor['investor_type'])) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($investor['location'])): ?>
                                                <span class="text-muted small">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?= htmlspecialchars($investor['location']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="card-description">
                                    <?php 
                                    $bio = $investor['bio'] ?? 'Experienced investor looking for promising opportunities.';
                                    echo htmlspecialchars(substr($bio, 0, 150));
                                    if (strlen($bio) > 150) echo '...';
                                    ?>
                                </p>
                                
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-label">Investment Range</span>
                                        <span class="stat-value">
                                            <?php 
                                            $min = $investor['investment_range_min'] ?? 0;
                                            $max = $investor['investment_range_max'] ?? 0;
                                            echo '$' . number_format($min/1000) . 'K - $' . number_format($max/1000) . 'K';
                                            ?>
                                        </span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Focus Areas</span>
                                        <span class="stat-value">
                                            <?php 
                                            $preferredIndustries = $investor['preferred_industries'] ?? [];
                                            if (!empty($preferredIndustries) && is_array($preferredIndustries)): 
                                                echo count($preferredIndustries) . ' Industries';
                                            else:
                                                echo 'All';
                                            endif;
                                            ?>
                                        </span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Member Since</span>
                                        <span class="stat-value">
                                            <?= date('M Y', strtotime($investor['created_at'] ?? 'now')) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Investment Stages -->
                                <?php 
                                $investmentStages = $investor['investment_stages'] ?? [];
                                if (!empty($investmentStages) && is_array($investmentStages)): 
                                ?>
                                    <div class="industry-tags">
                                        <?php 
                                        $displayStages = array_slice($investmentStages, 0, 4);
                                        foreach ($displayStages as $stage): 
                                        ?>
                                            <span class="industry-tag">
                                                <?= ucfirst(str_replace('_', ' ', $stage)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($investmentStages) > 4): ?>
                                            <span class="industry-tag">+<?= count($investmentStages) - 4 ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-meta">
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($investor['linkedin_url'])): ?>
                                            <a href="<?= htmlspecialchars($investor['linkedin_url']) ?>" 
                                               target="_blank"
                                               class="btn btn-sm btn-outline action-btn">
                                                <i class="fab fa-linkedin me-1"></i>LinkedIn
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm action-btn btn-view" 
                                                onclick="viewProfile(<?= $investor['user_id'] ?? 0 ?>)">
                                            <i class="fas fa-eye me-1"></i>View Profile
                                        </button>
                                        <?php if (($currentUser['user_type'] ?? '') === 'startup'): ?>
                                            <button class="btn btn-sm action-btn btn-connect" 
                                                    onclick="expressInterest(<?= $investor['id'] ?? 0 ?>, 'investor')">
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
            
            <!-- Enhanced Pagination -->
            <?php if (($pagination['last_page'] ?? 1) > 1): ?>
                <div class="pagination-wrapper">
                    <nav aria-label="Search results pagination">
                        <ul class="pagination">
                            <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => ($pagination['current_page'] ?? 1) - 1])) ?>">
                                        <i class="fas fa-chevron-left me-1"></i>Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php 
                            $currentPage = $pagination['current_page'] ?? 1;
                            $lastPage = $pagination['last_page'] ?? 1;
                            for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++): 
                            ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $lastPage): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">
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
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h4 class="empty-title">No investors found</h4>
                <p class="empty-description">
                    We couldn't find any investors matching your criteria. Try adjusting your filters or search terms to discover more opportunities.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="<?= url('search/investors') ?>" class="btn btn-outline-primary">
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

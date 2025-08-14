<?php
// Get current route for active navigation
$currentUri = $_SERVER['REQUEST_URI'];
$currentPath = parse_url($currentUri, PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && strpos($currentPath, $basePath) === 0) {
    $currentPath = substr($currentPath, strlen($basePath));
}
$currentPath = trim($currentPath, '/');

function isActiveRoute($route, $currentPath) {
    if ($route === 'dashboard' && ($currentPath === '' || $currentPath === 'dashboard')) {
        return true;
    }
    return strpos($currentPath, $route) === 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - Startup Connect' ?></title>
    
    <!-- External CSS Files - Following New Rule -->
    <link href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('vendor/fontawesome/all.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/layout.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/search.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/matches.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="sidebar">
        <i class="fas fa-bars" aria-hidden="true"></i>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
                <div class="sidebar-header text-center">
                    <h4><i class="fas fa-rocket me-2" aria-hidden="true"></i>Startup Connect</h4>
                </div>
                
                <ul class="nav flex-column py-3" role="menubar">
                    <li class="nav-item" role="none">
                        <a class="nav-link <?= isActiveRoute('dashboard', $currentPath) ? 'active' : '' ?>" 
                           href="<?= url('dashboard') ?>" role="menuitem" aria-current="<?= isActiveRoute('dashboard', $currentPath) ? 'page' : 'false' ?>">
                            <i class="fas fa-tachometer-alt" aria-hidden="true"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item" role="none">
                        <a class="nav-link <?= isActiveRoute('profile', $currentPath) ? 'active' : '' ?>" 
                           href="<?= url('profile') ?>" role="menuitem" aria-current="<?= isActiveRoute('profile', $currentPath) ? 'page' : 'false' ?>">
                            <i class="fas fa-user" aria-hidden="true"></i>My Profile
                        </a>
                    </li>
                    <li class="nav-item" role="none">
                        <a class="nav-link <?= isActiveRoute('matches', $currentPath) ? 'active' : '' ?>" 
                           href="<?= url('matches') ?>" role="menuitem" aria-current="<?= isActiveRoute('matches', $currentPath) ? 'page' : 'false' ?>">
                            <i class="fas fa-heart" aria-hidden="true"></i>Matches
                        </a>
                    </li>
                    <?php if (($_SESSION['user_type'] ?? '') === 'startup'): ?>
                        <li class="nav-item" role="none">
                            <a class="nav-link <?= isActiveRoute('search/investors', $currentPath) ? 'active' : '' ?>" 
                               href="<?= url('search/investors') ?>" role="menuitem" aria-current="<?= isActiveRoute('search/investors', $currentPath) ? 'page' : 'false' ?>">
                                <i class="fas fa-search" aria-hidden="true"></i>Find Investors
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item" role="none">
                            <a class="nav-link <?= isActiveRoute('search/startups', $currentPath) ? 'active' : '' ?>" 
                               href="<?= url('search/startups') ?>" role="menuitem" aria-current="<?= isActiveRoute('search/startups', $currentPath) ? 'page' : 'false' ?>">
                                <i class="fas fa-search" aria-hidden="true"></i>Find Startups
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item" role="none">
                        <a class="nav-link <?= isActiveRoute('messages', $currentPath) ? 'active' : '' ?>" 
                           href="<?= url('messages') ?>" role="menuitem" aria-current="<?= isActiveRoute('messages', $currentPath) ? 'page' : 'false' ?>">
                            <i class="fas fa-envelope" aria-hidden="true"></i>Messages
                        </a>
                    </li>
                    
                    <hr class="my-3 mx-3" style="border-color: rgba(255, 255, 255, 0.2);" role="separator">
                    
                    <li class="nav-item" role="none">
                        <a class="nav-link <?= isActiveRoute('settings', $currentPath) ? 'active' : '' ?>" 
                           href="<?= url('settings') ?>" role="menuitem" aria-current="<?= isActiveRoute('settings', $currentPath) ? 'page' : 'false' ?>">
                            <i class="fas fa-cog" aria-hidden="true"></i>Settings
                        </a>
                    </li>
                    <li class="nav-item" role="none">
                        <a class="nav-link" href="<?= url('logout') ?>" role="menuitem">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>Logout
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Main Content -->
            <div class="main-content">
                <!-- Top Navigation -->
                <nav class="top-navbar d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0"><?= $title ?? 'Dashboard' ?></span>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="nav-link dropdown-toggle d-flex align-items-center btn btn-link" 
                               id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" 
                               aria-label="User menu">
                                <i class="fas fa-user-circle me-2" style="font-size: 1.5rem;" aria-hidden="true"></i>
                                <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?= url('profile') ?>">
                                    <i class="fas fa-user me-2" aria-hidden="true"></i>View Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?= url('profile/settings') ?>">
                                    <i class="fas fa-edit me-2" aria-hidden="true"></i>Edit Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?= url('settings') ?>">
                                    <i class="fas fa-cog me-2" aria-hidden="true"></i>Settings
                                </a></li>
                                <li><hr class="dropdown-divider" role="separator"></li>
                                <li><a class="dropdown-item" href="<?= url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>Logout
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="page-content">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset('js/layout.js') ?>"></script>
    <script src="<?= asset('js/dashboard.js') ?>"></script>
    <script src="<?= asset('js/search.js') ?>"></script>
    <script src="<?= asset('js/matches.js') ?>"></script>
</body>
</html>

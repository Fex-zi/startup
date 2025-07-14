<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - Startup Connect' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            padding: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center mb-4">
                    <h4><i class="fas fa-rocket me-2"></i>Startup Connect</h4>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="<?= url('dashboard') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="<?= url('profile/edit') ?>">
                        <i class="fas fa-user me-2"></i>Profile
                    </a>
                    <a class="nav-link" href="<?= url('matches') ?>">
                        <i class="fas fa-heart me-2"></i>Matches
                    </a>
                    <?php if (($_SESSION['user_type'] ?? '') === 'startup'): ?>
                        <a class="nav-link" href="<?= url('search/investors') ?>">
                            <i class="fas fa-search me-2"></i>Find Investors
                        </a>
                    <?php else: ?>
                        <a class="nav-link" href="<?= url('search/startups') ?>">
                            <i class="fas fa-search me-2"></i>Find Startups
                        </a>
                    <?php endif; ?>
                    <a class="nav-link" href="<?= url('messages') ?>">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                    
                    <hr class="my-3">
                    
                    <a class="nav-link" href="<?= url('settings') ?>">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                    <a class="nav-link" href="<?= url('logout') ?>">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded mb-4">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1"><?= $title ?? 'Dashboard' ?></span>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= url('profile/edit') ?>">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?= url('settings') ?>">
                                        <i class="fas fa-cog me-2"></i>Settings
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= url('logout') ?>">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

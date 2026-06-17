<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Portal System</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-canvas">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
    </div>

    <!-- Navbar -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="glass-navbar">
        <a href="<?php echo $_SESSION['user_role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'; ?>" class="logo">
            <div class="logo-icon">U</div>
            <span class="logo-text">UniPortal</span>
        </a>
        <div class="nav-links">
            <span class="user-badge">
                <span class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></span>
                <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
            </span>
            <a href="logout.php" class="btn-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Logout
            </a>
        </div>
    </nav>
    <?php endif; ?>

    <main class="app-container">
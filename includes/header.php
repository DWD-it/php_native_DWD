<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Portal System</title>
    <!-- Custom Premium Glassmorphism CSS -->
    <link rel="stylesheet" href="/css/style.css?v=<?php echo time(); ?>">
    <!-- Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Background Animated Blobs -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    
    <!-- Navbar (Only show if logged in) -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="glass-navbar">
        <div class="logo">
            <h1>UniPortal</h1>
        </div>
        <div class="nav-links">
            <span class="user-badge">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>
    <?php endif; ?>

    <main class="app-container">

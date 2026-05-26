<?php
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

require_once __DIR__ . '/../classes/User.php';
// Create a concrete class to instantiate for login purposes since User is abstract
class AuthUser extends User {
    public function getDashboardUrl() { return ''; }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $authUser = new AuthUser();
    if ($authUser->login($email, $password)) {
        // Securely start session and regenerate ID to prevent Session Fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $authUser->getId();
        $_SESSION['user_name'] = $authUser->getName();
        $_SESSION['user_role'] = $authUser->getRole();

        if ($authUser->getRole() === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="login-container glass-panel">
    <div class="login-header">
        <h2>Welcome Back</h2>
        <p>Sign in to the University Portal</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" class="login-form">
        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="admin@university.edu">
        </div>
        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>
    
    <div class="login-footer">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

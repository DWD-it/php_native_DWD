<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

require_once __DIR__ . '/../classes/User.php';

class AuthUser extends User {
    public function getDashboardUrl() { return ''; }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $authUser = new AuthUser();
    if ($authUser->login($email, $password)) {
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
        $error = "❌ Invalid email or password. Please try again.";
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="login-wrapper">
    <div class="login-container glass-panel animate-fade-up">
        <div class="login-brand">
            <div class="brand-icon">🏫</div>
            <h2>Welcome Back</h2>
            <p>Sign in to access the University Portal</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="input-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon">📧</span>
                    <input type="email" id="email" name="email" required placeholder="admin@university.edu" autofocus>
                </div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔑</span>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <span>🚀</span> Sign In
            </button>
        </form>

        <div class="login-divider">or</div>

        <div class="login-footer">
            <p>Don't have an account? <a href="register.php">Create one now</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
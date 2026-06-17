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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';

    if ($name && $email && $password) {
        try {
            require_once __DIR__ . '/../config/Database.php';
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "❌ This email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role]);
                $success = "✅ Registration successful! You can now login.";
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Please fill in all fields.";
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="login-wrapper">
    <div class="login-container glass-panel animate-fade-up">
        <div class="login-brand">
            <div class="brand-icon" style="background: var(--gradient-secondary);">📝</div>
            <h2>Create Account</h2>
            <p>Join the University Portal community</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="input-group">
                <label for="name">Full Name</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" id="name" name="name" required placeholder="John Doe">
                </div>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon">📧</span>
                    <input type="email" id="email" name="email" required placeholder="student@university.edu">
                </div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔑</span>
                    <input type="password" id="password" name="password" required placeholder="Create a strong password">
                </div>
            </div>
            <div class="input-group">
                <label for="role">Role</label>
                <div class="input-wrapper">
                    <span class="input-icon">🎯</span>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success btn-block btn-lg">
                <span>✨</span> Create Account
            </button>
        </form>

        <div class="login-divider">or</div>

        <div class="login-footer">
            <p>Already have an account? <a href="index.php">Sign in here</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
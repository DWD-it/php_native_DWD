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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic registration logic here (placeholder)
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';

    if ($name && $email && $password) {
        try {
            require_once __DIR__ . '/../config/Database.php';
            $db = Database::getInstance()->getConnection();
            
            // Check if email exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role]);
                $success = "Registration successful! You can now login.";
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="login-container glass-panel">
    <div class="login-header">
        <h2>Create Account</h2>
        <p>Join the University Portal</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" class="login-form">
        <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required placeholder="John Doe">
        </div>
        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="student@university.edu">
        </div>
        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>
        <div class="input-group">
            <label for="role">Role</label>
            <select id="role" name="role" required style="width: 100%; padding: 0.8rem 1rem; background: rgba(0, 0, 0, 0.2); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--text-main); font-size: 1rem; outline: none;">
                <option value="student" style="color: black;">Student</option>
                <option value="admin" style="color: black;">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
    
    <div class="login-footer">
        <p>Already have an account? <a href="index.php">Sign In here</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

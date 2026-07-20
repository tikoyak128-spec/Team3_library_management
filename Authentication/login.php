<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Database/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: ../Dashboard/index.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login — Library Pro</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="brand">📚 Library Pro</div>
        <h2>Welcome back</h2>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Log In</button>
        </form>
        <div class="switch-link">Don't have an account? <a href="register.php">Register</a></div>
    </div>
</div>
</body>
</html>

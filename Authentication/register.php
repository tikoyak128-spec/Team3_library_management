<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Database/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with this email address already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
            );

            if ($stmt->execute([$name, $email, $hash])) {
                $_SESSION['success_msg'] = 'Account created successfully! Please log in.';
                header("Location: login.php");
                exit;
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Library Pro</title>
    
    <!-- Google Fonts & Font Awesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg-surface: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-lg: 16px;
            --radius-sm: 10px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-surface);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Split-screen Layout Container */
        .auth-container {
            display: flex;
            width: 100%;
            max-width: 1050px;
            min-height: 680px;
            margin: 1.5rem;
            background: #ffffff;
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.03);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        /* Left Side Hero Panel */
        .auth-hero {
            flex: 1;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
            color: #ffffff;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .auth-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .hero-content h1 {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 1rem;
        }

        .hero-content p {
            font-size: 0.975rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .hero-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .hero-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
        }

        .hero-features i {
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .hero-footer {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.65);
        }

        /* Right Side Form Panel */
        .auth-form-wrapper {
            flex: 1.1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 1.75rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.35rem;
        }

        .form-header p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* Alert Styling */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box i {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .input-box input {
            width: 100%;
            padding: 0.7rem 1rem 0.7rem 2.75rem;
            font-size: 0.9rem;
            color: var(--text-dark);
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            outline: none;
            transition: all 0.2s ease;
        }

        .input-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .input-box input:focus + i {
            color: var(--primary);
        }

        /* Buttons & Footer Link */
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background-color: var(--primary);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        .switch-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .switch-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .switch-link a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* Responsive Breakpoints */
        @media (max-width: 850px) {
            .auth-hero {
                display: none;
            }
            .auth-container {
                max-width: 450px;
                min-height: auto;
            }
            .auth-form-wrapper {
                padding: 2.5rem 2rem;
            }
        }
    </style>
</head>
<body>

<div class="auth-container">
    <!-- Left Hero Section -->
    <div class="auth-hero">
        <div class="hero-brand">
            <i class="fa-solid fa-book-open-reader"></i> Library Pro
        </div>
        
        <div class="hero-content">
            <h1>Join our library network today.</h1>
            <p>Create an account to manage your library assets, borrow books, and streamline your operations.</p>
            
            <ul class="hero-features">
                <li><i class="fa-solid fa-check"></i> Quick setup in under 2 minutes</li>
                <li><i class="fa-solid fa-check"></i> Secure role-based management</li>
                <li><i class="fa-solid fa-check"></i> Full catalog access</li>
            </ul>
        </div>

        <div class="hero-footer">
            &copy; <?= date('Y') ?> Library Pro System. All rights reserved.
        </div>
    </div>

    <!-- Right Register Form Section -->
    <div class="auth-form-wrapper">
        <div class="form-header">
            <h2>Create account</h2>
            <p>Get started with your Library Pro account.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-box">
                    <input type="text" id="name" name="name" required placeholder="John Doe" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-box">
                    <input type="email" id="email" name="email" required placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <i class="fa-regular fa-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-box">
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-box">
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Create Account <i class="fa-solid fa-arrow-right" style="margin-left: 0.4rem;"></i>
            </button>
        </form>

        <div class="switch-link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

</body>
</html>
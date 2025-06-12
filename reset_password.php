<?php
// Password reset handler
// reset_password.php

require_once __DIR__ . '/includes/User.php';

// Start session to handle messages
session_start();

$error = '';
$success = '';
$token = '';

// Get token from URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    $error = 'Invalid reset link';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please fill in all fields';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $user = new User();
        $result = $user->resetPassword($token, $newPassword);
        
        if ($result['success']) {
            $success = 'Password reset successfully! You can now log in with your new password.';
            // Redirect to login page after 3 seconds
            header("refresh:3;url=login.php");
        } else {
            $error = $result['message'];
        }
    }
}

// Check if already logged in
$user = new User();
if ($user->isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - FrogBytes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables for theming */
        :root {
            /* Dark theme (default) */
            --bg-primary: #0D1117;
            --bg-secondary: #161B22;
            --border-color: #30363D;
            --text-primary: #C9D1D9;
            --text-secondary: #8B949E;
            --text-tertiary: #E0E0E0;
            --accent-primary: #39FF14;
            --accent-secondary: #32CD32;
            --accent-tertiary: #00FF32;
            --error-color: #FF8A80;
            --success-color: #69F0AE;
            --shadow-accent: rgba(57, 255, 20, 0.1);
            --shadow-accent-hover: rgba(57, 255, 20, 0.3);
            --glow-accent: rgba(57, 255, 20, 0.5);
        }

        /* Light theme */
        body.light-theme {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F6F8FA;
            --border-color: #D0D7DE;
            --text-primary: #24292F;
            --text-secondary: #656D76;
            --text-tertiary: #24292F;
            --accent-primary: #39FF14;
            --accent-secondary: #32CD32;
            --accent-tertiary: #00FF32;
            --error-color: #DA3633;
            --success-color: #28a745;
            --shadow-accent: rgba(57, 255, 20, 0.1);
            --shadow-accent-hover: rgba(57, 255, 20, 0.2);
            --glow-accent: rgba(57, 255, 20, 0.4);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            background-image: linear-gradient(45deg, rgba(20, 25, 35, 0.5) 25%, transparent 25%, transparent 75%, rgba(20, 25, 35, 0.5) 75%, rgba(20, 25, 35, 0.5)),
                              linear-gradient(45deg, rgba(20, 25, 35, 0.5) 25%, transparent 25%, transparent 75%, rgba(20, 25, 35, 0.5) 75%, rgba(20, 25, 35, 0.5));
            background-size: 6px 6px;
            background-position: 0 0, 3px 3px;
            margin: 0; 
            padding: 0;
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body.light-theme {
            background-image: linear-gradient(45deg, rgba(200, 205, 215, 0.3) 25%, transparent 25%, transparent 75%, rgba(200, 205, 215, 0.3) 75%, rgba(200, 205, 215, 0.3)),
                              linear-gradient(45deg, rgba(200, 205, 215, 0.3) 25%, transparent 25%, transparent 75%, rgba(200, 205, 215, 0.3) 75%, rgba(200, 205, 215, 0.3));
        }

        .header {
            background: var(--bg-secondary);
            color: var(--text-tertiary);
            padding: 15px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--accent-primary);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .header .logo {
            font-size: 20px;
            font-weight: 600;
            color: var(--accent-primary);
        }
        .header .logo a {
            color: var(--accent-primary);
            text-decoration: none;
            font-size: 20px;
            font-weight: 600;
            transition: color 0.2s;
        }
        .header .logo a:hover {
            color: var(--accent-tertiary);
        }

        .container {
            max-width: 420px;
            margin: 60px auto;
            background: var(--bg-secondary);
            padding: 35px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            box-shadow: 0 0 15px var(--shadow-accent);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        h1 {
            margin-top: 0;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
            color: var(--accent-primary);
        }
        .container p { 
            text-align: center;
            margin-bottom: 25px;
            color: var(--text-secondary);
            font-size: 15px;
        }
        .error {
            color: var(--error-color);
            margin-bottom: 15px;
            font-weight: 500;
            text-align: center;
            background-color: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 4px;
            border: 1px solid var(--error-color);
        }
        .success {
            color: var(--success-color);
            margin-bottom: 15px;
            font-weight: 500;
            text-align: center;
            background-color: rgba(105, 240, 174, 0.1);
            padding: 10px;
            border-radius: 4px;
            border: 1px solid var(--success-color);
        }
        label {
            display: block;
            margin: 18px 0 8px;
            font-weight: 500;
            font-size: 14px;
            color: var(--text-secondary);
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 15px;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input[type="password"]:focus {
            border-color: var(--accent-primary);
            outline: none;
            box-shadow: 0 0 0 2px var(--shadow-accent-hover);
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background: var(--accent-primary);
            color: var(--bg-primary);
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        button:hover {
            background: var(--accent-secondary);
            box-shadow: 0 0 10px var(--glow-accent);
        }
        .link {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
        .link a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 500;
        }
        .link a:hover {
            text-decoration: underline;
            color: var(--accent-tertiary);
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo"><a href="./index.php">FrogBytes</a></div>
</div>

<div class="container">
    <h1>Reset Password</h1>
    <p>Enter your new password below.</p>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($token) && !$success): ?>
    <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required minlength="8">
        
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        
        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>

    <div class="link">
        <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>

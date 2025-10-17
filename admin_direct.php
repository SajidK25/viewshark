<?php
/*******************************************************************************************************************
| EasyStream Direct Admin Access
| Bypasses core loading issues for immediate admin access
|*******************************************************************************************************************/

define('_ISVALID', true);

// Start session
session_start();

// Handle login
if ($_POST['login']) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    try {
        $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check admin credentials
        $stmt = $pdo->prepare("SELECT cfg_value FROM db_settings WHERE cfg_name = ?");
        $stmt->execute(['backend_username']);
        $admin_user = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT cfg_value FROM db_settings WHERE cfg_name = ?");
        $stmt->execute(['backend_password']);
        $admin_pass = $stmt->fetchColumn();
        
        if ($username === $admin_user && $password === $admin_pass) {
            $_SESSION['ADMIN_NAME'] = $admin_user;
            $_SESSION['ADMIN_PASS'] = $admin_pass;
            $_SESSION['admin_logged_in'] = true;
            
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials";
        }
        
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle logout
if ($_GET['logout']) {
    session_destroy();
    header("Location: admin_direct.php");
    exit;
}

// Check if already logged in
if ($_SESSION['admin_logged_in']) {
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>EasyStream Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            max-width: 400px; 
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo h1 {
            font-size: 2.2em;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .logo p {
            color: #666;
            font-size: 1em;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>🔐 Admin Login</h1>
            <p>EasyStream Administration</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="info">
            <strong>Default Credentials:</strong><br>
            Username: admin<br>
            Password: admin123
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" value="1" class="btn">
                🚀 Login to Admin Panel
            </button>
        </form>
    </div>
</body>
</html>
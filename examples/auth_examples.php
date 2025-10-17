<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);
require_once '../f_core/config.core.php';

$auth = VAuth::getInstance();
$security = VSecurity::getInstance();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyStream Authentication Examples</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .success { color: green; padding: 10px; background: #f0f8f0; border: 1px solid #d4edda; border-radius: 4px; }
        .error { color: red; padding: 10px; background: #f8f0f0; border: 1px solid #f5c6cb; border-radius: 4px; }
        .info { color: blue; padding: 10px; background: #f0f4f8; border: 1px solid #bee5eb; border-radius: 4px; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .user-info { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔐 EasyStream Authentication System Examples</h1>
    
    <?php
    // Handle form submissions
    $message = '';
    $messageType = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = VSecurity::postParam('action', 'string');
        
        switch ($action) {
            case 'register':
                if (VSecurity::validateCSRFFromPost('register')) {
                    $userData = [
                        'username' => VSecurity::postParam('username', 'string'),
                        'email' => VSecurity::postParam('email', 'email'),
                        'password' => $_POST['password'] ?? ''
                    ];
                    
                    $result = $auth->register($userData);
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'login':
                if (VSecurity::validateCSRFFromPost('login')) {
                    $identifier = VSecurity::postParam('identifier', 'string');
                    $password = $_POST['password'] ?? '';
                    $rememberMe = VSecurity::postParam('remember_me', 'boolean', false);
                    
                    $result = $auth->login($identifier, $password, $rememberMe);
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'logout':
                if (VSecurity::validateCSRFFromPost('logout')) {
                    $result = $auth->logout();
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'verify_email':
                $token = VSecurity::postParam('token', 'string');
                $result = $auth->verifyEmail($token);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'request_reset':
                if (VSecurity::validateCSRFFromPost('password_reset')) {
                    $email = VSecurity::postParam('email', 'email');
                    $result = $auth->requestPasswordReset($email);
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'reset_password':
                if (VSecurity::validateCSRFFromPost('password_reset')) {
                    $token = VSecurity::postParam('token', 'string');
                    $password = $_POST['password'] ?? '';
                    $result = $auth->resetPassword($token, $password);
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
        }
    }
    
    // Display message
    if ($message) {
        echo "<div class='{$messageType}'>{$message}</div>";
    }
    
    // Check authentication status
    $isAuthenticated = $auth->isAuthenticated();
    $currentUser = $auth->getCurrentUser();
    ?>
    
    <div class="info">
        <strong>Authentication Status:</strong> 
        <?php if ($isAuthenticated): ?>
            ✅ Authenticated as <strong><?= htmlspecialchars($currentUser['username']) ?></strong>
        <?php else: ?>
            ❌ Not authenticated
        <?php endif; ?>
    </div>
    
    <?php if ($isAuthenticated): ?>
        <div class="user-info">
            <h3>Current User Information</h3>
            <p><strong>User ID:</strong> <?= htmlspecialchars($currentUser['user_id']) ?></p>
            <p><strong>Username:</strong> <?= htmlspecialchars($currentUser['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($currentUser['email']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($currentUser['role']) ?></p>
        </div>
        
        <div class="section">
            <h2>🚪 Logout</h2>
            <form method="POST">
                <input type="hidden" name="action" value="logout">
                <?= VSecurity::getCSRFField('logout') ?>
                <button type="submit">Logout</button>
            </form>
        </div>
    <?php else: ?>
        
        <div class="section">
            <h2>📝 User Registration</h2>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <?= VSecurity::getCSRFField('register') ?>
                
                <div class="form-group">
                    <label for="reg_username">Username:</label>
                    <input type="text" id="reg_username" name="username" required 
                           pattern="[a-zA-Z0-9]{3,50}" 
                           title="3-50 alphanumeric characters only">
                </div>
                
                <div class="form-group">
                    <label for="reg_email">Email:</label>
                    <input type="email" id="reg_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="reg_password">Password:</label>
                    <input type="password" id="reg_password" name="password" required 
                           minlength="8"
                           title="At least 8 characters with uppercase, lowercase, number, and special character">
                    <small>Must contain: uppercase, lowercase, number, and special character</small>
                </div>
                
                <button type="submit">Register</button>
            </form>
        </div>
        
        <div class="section">
            <h2>🔑 User Login</h2>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <?= VSecurity::getCSRFField('login') ?>
                
                <div class="form-group">
                    <label for="login_identifier">Username or Email:</label>
                    <input type="text" id="login_identifier" name="identifier" required>
                </div>
                
                <div class="form-group">
                    <label for="login_password">Password:</label>
                    <input type="password" id="login_password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="remember_me" value="1">
                        Remember me for 30 days
                    </label>
                </div>
                
                <button type="submit">Login</button>
            </form>
        </div>
        
    <?php endif; ?>
    
    <div class="section">
        <h2>📧 Email Verification</h2>
        <p>If you registered but haven't verified your email, enter your verification token here:</p>
        <form method="POST">
            <input type="hidden" name="action" value="verify_email">
            
            <div class="form-group">
                <label for="verify_token">Verification Token:</label>
                <input type="text" id="verify_token" name="token" required 
                       pattern="[a-f0-9]{64}" 
                       title="64-character hexadecimal token">
            </div>
            
            <button type="submit">Verify Email</button>
        </form>
    </div>
    
    <div class="section">
        <h2>🔄 Password Reset</h2>
        
        <h3>Request Password Reset</h3>
        <form method="POST">
            <input type="hidden" name="action" value="request_reset">
            <?= VSecurity::getCSRFField('password_reset') ?>
            
            <div class="form-group">
                <label for="reset_email">Email:</label>
                <input type="email" id="reset_email" name="email" required>
            </div>
            
            <button type="submit">Request Password Reset</button>
        </form>
        
        <h3>Reset Password with Token</h3>
        <form method="POST">
            <input type="hidden" name="action" value="reset_password">
            <?= VSecurity::getCSRFField('password_reset') ?>
            
            <div class="form-group">
                <label for="reset_token">Reset Token:</label>
                <input type="text" id="reset_token" name="token" required 
                       pattern="[a-f0-9]{64}" 
                       title="64-character hexadecimal token">
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="password" required 
                       minlength="8"
                       title="At least 8 characters with uppercase, lowercase, number, and special character">
                <small>Must contain: uppercase, lowercase, number, and special character</small>
            </div>
            
            <button type="submit">Reset Password</button>
        </form>
    </div>
    
    <div class="section">
        <h2>🔧 API Examples</h2>
        <p>The authentication system also provides a REST API at <code>/api/auth.php</code></p>
        
        <h3>Available Endpoints:</h3>
        <ul>
            <li><strong>POST /api/auth.php?action=register</strong> - Register new user</li>
            <li><strong>POST /api/auth.php?action=login</strong> - Login user</li>
            <li><strong>POST /api/auth.php?action=logout</strong> - Logout user</li>
            <li><strong>GET /api/auth.php?action=me</strong> - Get current user info</li>
            <li><strong>GET /api/auth.php?action=status</strong> - Get authentication status</li>
            <li><strong>POST /api/auth.php?action=verify_email</strong> - Verify email</li>
            <li><strong>POST /api/auth.php?action=request_password_reset</strong> - Request password reset</li>
            <li><strong>POST /api/auth.php?action=reset_password</strong> - Reset password</li>
            <li><strong>GET /api/auth.php?action=csrf_token</strong> - Get CSRF token</li>
        </ul>
        
        <h3>Example JavaScript Usage:</h3>
        <pre><code>// Get CSRF token
const tokenResponse = await fetch('/api/auth.php?action=csrf_token&for=login');
const tokenData = await tokenResponse.json();

// Login user
const loginResponse = await fetch('/api/auth.php?action=login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        identifier: 'username',
        password: 'password',
        csrf_token: tokenData.token,
        remember_me: true
    })
});

const loginResult = await loginResponse.json();
console.log(loginResult);</code></pre>
    </div>
    
    <div class="section">
        <h2>🛡️ Security Features</h2>
        <ul>
            <li><strong>CSRF Protection:</strong> All forms include CSRF tokens</li>
            <li><strong>Rate Limiting:</strong> Login attempts and password resets are rate limited</li>
            <li><strong>Password Strength:</strong> Enforced strong password requirements</li>
            <li><strong>Session Security:</strong> Secure session management with Redis support</li>
            <li><strong>Input Validation:</strong> All inputs are validated and sanitized</li>
            <li><strong>Email Verification:</strong> Optional email verification for new accounts</li>
            <li><strong>Remember Me:</strong> Secure remember me functionality</li>
            <li><strong>Audit Logging:</strong> All authentication events are logged</li>
            <li><strong>IP Tracking:</strong> Login attempts tracked by IP address</li>
            <li><strong>Session Regeneration:</strong> Session IDs regenerated on login</li>
        </ul>
    </div>
    
    <script>
        // Add some client-side validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            
            passwordInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const password = this.value;
                    const hasUpper = /[A-Z]/.test(password);
                    const hasLower = /[a-z]/.test(password);
                    const hasNumber = /\d/.test(password);
                    const hasSpecial = /[@$!%*?&]/.test(password);
                    const isLongEnough = password.length >= 8;
                    
                    const isStrong = hasUpper && hasLower && hasNumber && hasSpecial && isLongEnough;
                    
                    this.style.borderColor = isStrong ? 'green' : 'red';
                });
            });
        });
    </script>
</body>
</html>
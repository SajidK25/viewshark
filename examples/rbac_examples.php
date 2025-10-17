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
$rbac = VRBAC::getInstance();
$middleware = VMiddleware::getInstance();
$security = VSecurity::getInstance();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyStream RBAC System Examples</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #005a87; }
        button.danger { background: #dc3545; }
        button.danger:hover { background: #c82333; }
        button.success { background: #28a745; }
        button.success:hover { background: #218838; }
        .success { color: green; padding: 10px; background: #f0f8f0; border: 1px solid #d4edda; border-radius: 4px; }
        .error { color: red; padding: 10px; background: #f8f0f0; border: 1px solid #f5c6cb; border-radius: 4px; }
        .info { color: blue; padding: 10px; background: #f0f4f8; border: 1px solid #bee5eb; border-radius: 4px; }
        .warning { color: orange; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .user-info { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .permission-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 15px 0; }
        .permission-item { padding: 10px; border: 1px solid #ddd; border-radius: 4px; text-align: center; }
        .permission-granted { background: #d4edda; border-color: #c3e6cb; }
        .permission-denied { background: #f8d7da; border-color: #f5c6cb; }
        .role-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; margin: 2px; }
        .role-guest { background: #6c757d; color: white; }
        .role-member { background: #17a2b8; color: white; }
        .role-verified { background: #28a745; color: white; }
        .role-premium { background: #ffc107; color: black; }
        .role-moderator { background: #fd7e14; color: white; }
        .role-admin { background: #dc3545; color: white; }
        .role-superadmin { background: #6f42c1; color: white; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .status-active { color: green; font-weight: bold; }
        .status-suspended { color: orange; font-weight: bold; }
        .status-banned { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🛡️ EasyStream Role-Based Access Control (RBAC) System</h1>
    
    <?php
    // Handle form submissions
    $message = '';
    $messageType = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = VSecurity::postParam('action', 'string');
        
        switch ($action) {
            case 'change_role':
                if (VSecurity::validateCSRFFromPost('admin_action')) {
                    $userId = VSecurity::postParam('user_id', 'int');
                    $newRole = VSecurity::postParam('new_role', 'string');
                    $reason = VSecurity::postParam('reason', 'string');
                    
                    $currentUser = $auth->getCurrentUser();
                    if ($currentUser && $rbac->hasPermission('user.manage')) {
                        $result = $rbac->changeUserRole($userId, $newRole, $currentUser['user_id'], $reason);
                        $message = $result ? 'Role changed successfully' : 'Failed to change role';
                        $messageType = $result ? 'success' : 'error';
                    } else {
                        $message = 'Permission denied';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'grant_permission':
                if (VSecurity::validateCSRFFromPost('admin_action')) {
                    $userId = VSecurity::postParam('user_id', 'int');
                    $permission = VSecurity::postParam('permission', 'string');
                    $expiresAt = VSecurity::postParam('expires_at', 'string');
                    
                    $currentUser = $auth->getCurrentUser();
                    if ($currentUser && $rbac->hasPermission('user.manage')) {
                        $result = $rbac->grantPermission($userId, $permission, $currentUser['user_id'], $expiresAt ?: null);
                        $message = $result ? 'Permission granted successfully' : 'Failed to grant permission';
                        $messageType = $result ? 'success' : 'error';
                    } else {
                        $message = 'Permission denied';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'revoke_permission':
                if (VSecurity::validateCSRFFromPost('admin_action')) {
                    $userId = VSecurity::postParam('user_id', 'int');
                    $permission = VSecurity::postParam('permission', 'string');
                    
                    $currentUser = $auth->getCurrentUser();
                    if ($currentUser && $rbac->hasPermission('user.manage')) {
                        $result = $rbac->revokePermission($userId, $permission, $currentUser['user_id']);
                        $message = $result ? 'Permission revoked successfully' : 'Failed to revoke permission';
                        $messageType = $result ? 'success' : 'error';
                    } else {
                        $message = 'Permission denied';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'suspend_user':
                if (VSecurity::validateCSRFFromPost('admin_action')) {
                    $userId = VSecurity::postParam('user_id', 'int');
                    $reason = VSecurity::postParam('reason', 'string');
                    $expiresAt = VSecurity::postParam('expires_at', 'string');
                    
                    $currentUser = $auth->getCurrentUser();
                    if ($currentUser && $rbac->hasPermission('user.ban')) {
                        $result = $rbac->suspendUser($userId, $reason, $currentUser['user_id'], $expiresAt ?: null);
                        $message = $result ? 'User suspended successfully' : 'Failed to suspend user';
                        $messageType = $result ? 'success' : 'error';
                    } else {
                        $message = 'Permission denied';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'ban_user':
                if (VSecurity::validateCSRFFromPost('admin_action')) {
                    $userId = VSecurity::postParam('user_id', 'int');
                    $reason = VSecurity::postParam('reason', 'string');
                    $permanent = VSecurity::postParam('permanent', 'boolean', false);
                    
                    $currentUser = $auth->getCurrentUser();
                    if ($currentUser && $rbac->hasPermission('user.ban')) {
                        $result = $rbac->banUser($userId, $reason, $currentUser['user_id'], $permanent);
                        $message = $result ? 'User banned successfully' : 'Failed to ban user';
                        $messageType = $result ? 'success' : 'error';
                    } else {
                        $message = 'Permission denied';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Invalid CSRF token';
                    $messageType = 'error';
                }
                break;
                
            case 'reinstate_user':
                if (VSecurity::validateCSRFFromPost('admin_action')) {
                    $userId = VSecurity::postParam('user_id', 'int');
                    $reason = VSecurity::postParam('reason', 'string');
                    
                    $currentUser = $auth->getCurrentUser();
                    if ($currentUser && $rbac->hasPermission('user.ban')) {
                        $result = $rbac->reinstateUser($userId, $reason, $currentUser['user_id']);
                        $message = $result ? 'User reinstated successfully' : 'Failed to reinstate user';
                        $messageType = $result ? 'success' : 'error';
                    } else {
                        $message = 'Permission denied';
                        $messageType = 'error';
                    }
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
            <span class="role-badge role-<?= $currentUser['role'] ?>"><?= strtoupper($currentUser['role']) ?></span>
        <?php else: ?>
            ❌ Not authenticated - <a href="auth_examples.php">Login here</a>
        <?php endif; ?>
    </div>
    
    <?php if ($isAuthenticated): ?>
        
        <div class="section">
            <h2>🔐 Your Current Permissions</h2>
            <?php
            $userPermissions = $rbac->getUserPermissions($currentUser['user_id']);
            $allPermissions = VRBAC::PERMISSIONS;
            ?>
            
            <div class="permission-grid">
                <?php foreach ($allPermissions as $permission => $description): ?>
                    <?php $hasPermission = in_array($permission, $userPermissions); ?>
                    <div class="permission-item <?= $hasPermission ? 'permission-granted' : 'permission-denied' ?>">
                        <strong><?= htmlspecialchars($permission) ?></strong><br>
                        <small><?= htmlspecialchars($description) ?></small><br>
                        <?= $hasPermission ? '✅ Granted' : '❌ Denied' ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="section">
            <h2>🧪 Permission Testing</h2>
            <p>Test specific permissions with your current role:</p>
            
            <div class="permission-grid">
                <?php
                $testPermissions = [
                    'content.view', 'content.create', 'content.moderate',
                    'admin.dashboard', 'user.ban', 'api.admin',
                    'upload.large_files', 'feature.beta'
                ];
                
                foreach ($testPermissions as $permission):
                    $hasPermission = $rbac->hasPermission($permission);
                ?>
                    <div class="permission-item <?= $hasPermission ? 'permission-granted' : 'permission-denied' ?>">
                        <strong><?= htmlspecialchars($permission) ?></strong><br>
                        <?= $hasPermission ? '✅ You have this permission' : '❌ You lack this permission' ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if ($rbac->hasPermission('user.manage')): ?>
            
            <div class="section">
                <h2>👥 User Management</h2>
                <p>Manage other users (Admin/Moderator only):</p>
                
                <?php
                // Get list of users for management
                global $class_database;
                $db = $class_database->dbConnection();
                $sql = "SELECT user_id, username, email, role, status, created_at, last_login FROM db_users ORDER BY created_at DESC LIMIT 10";
                $result = $db->Execute($sql);
                ?>
                
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($result && !$result->EOF): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($result->fields['username']) ?></strong><br>
                                    <small><?= htmlspecialchars($result->fields['email']) ?></small>
                                </td>
                                <td>
                                    <span class="role-badge role-<?= $result->fields['role'] ?>">
                                        <?= strtoupper($result->fields['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-<?= $result->fields['status'] ?>">
                                        <?= strtoupper($result->fields['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $result->fields['last_login'] ? date('Y-m-d H:i', strtotime($result->fields['last_login'])) : 'Never' ?>
                                </td>
                                <td>
                                    <?php if ($result->fields['user_id'] != $currentUser['user_id']): ?>
                                        <button onclick="showUserActions(<?= $result->fields['user_id'] ?>, '<?= htmlspecialchars($result->fields['username']) ?>')">
                                            Manage
                                        </button>
                                    <?php else: ?>
                                        <em>You</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $result->MoveNext(); ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- User Management Modal -->
            <div id="userModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%;">
                    <h3>Manage User: <span id="modalUsername"></span></h3>
                    
                    <div class="form-group">
                        <h4>Change Role</h4>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="action" value="change_role">
                            <input type="hidden" name="user_id" id="modalUserId">
                            <?= VSecurity::getCSRFField('admin_action') ?>
                            
                            <select name="new_role" required>
                                <option value="">Select Role</option>
                                <?php foreach (VRBAC::ROLE_HIERARCHY as $role => $level): ?>
                                    <option value="<?= $role ?>"><?= ucfirst($role) ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <input type="text" name="reason" placeholder="Reason for role change" required>
                            <button type="submit">Change Role</button>
                        </form>
                    </div>
                    
                    <div class="form-group">
                        <h4>Grant Custom Permission</h4>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="action" value="grant_permission">
                            <input type="hidden" name="user_id" id="modalUserId2">
                            <?= VSecurity::getCSRFField('admin_action') ?>
                            
                            <select name="permission" required>
                                <option value="">Select Permission</option>
                                <?php foreach (VRBAC::PERMISSIONS as $permission => $description): ?>
                                    <option value="<?= $permission ?>"><?= $permission ?> - <?= $description ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <input type="datetime-local" name="expires_at" placeholder="Expiration (optional)">
                            <button type="submit" class="success">Grant Permission</button>
                        </form>
                    </div>
                    
                    <?php if ($rbac->hasPermission('user.ban')): ?>
                        <div class="form-group">
                            <h4>Suspend User</h4>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="action" value="suspend_user">
                                <input type="hidden" name="user_id" id="modalUserId3">
                                <?= VSecurity::getCSRFField('admin_action') ?>
                                
                                <input type="text" name="reason" placeholder="Suspension reason" required>
                                <input type="datetime-local" name="expires_at" placeholder="Expiration (optional)">
                                <button type="submit" class="danger">Suspend User</button>
                            </form>
                        </div>
                        
                        <div class="form-group">
                            <h4>Ban User</h4>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="action" value="ban_user">
                                <input type="hidden" name="user_id" id="modalUserId4">
                                <?= VSecurity::getCSRFField('admin_action') ?>
                                
                                <input type="text" name="reason" placeholder="Ban reason" required>
                                <label>
                                    <input type="checkbox" name="permanent" value="1">
                                    Permanent ban
                                </label>
                                <button type="submit" class="danger">Ban User</button>
                            </form>
                        </div>
                        
                        <div class="form-group">
                            <h4>Reinstate User</h4>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="action" value="reinstate_user">
                                <input type="hidden" name="user_id" id="modalUserId5">
                                <?= VSecurity::getCSRFField('admin_action') ?>
                                
                                <input type="text" name="reason" placeholder="Reinstatement reason" required>
                                <button type="submit" class="success">Reinstate User</button>
                            </form>
                        </div>
                    <?php endif; ?>
                    
                    <button onclick="closeUserModal()" style="background: #6c757d;">Close</button>
                </div>
            </div>
            
        <?php endif; ?>
        
    <?php else: ?>
        
        <div class="warning">
            <p>You need to be logged in to see RBAC features. <a href="auth_examples.php">Login here</a></p>
        </div>
        
    <?php endif; ?>
    
    <div class="section">
        <h2>📋 Role Hierarchy & Permissions</h2>
        <p>EasyStream uses a hierarchical role system where higher roles inherit permissions from lower roles:</p>
        
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Level</th>
                    <th>Key Permissions</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="role-badge role-guest">GUEST</span></td>
                    <td>0</td>
                    <td>content.view, comment.view</td>
                    <td>Unregistered users - can only view content</td>
                </tr>
                <tr>
                    <td><span class="role-badge role-member">MEMBER</span></td>
                    <td>10</td>
                    <td>content.create, comment.create, upload.basic</td>
                    <td>Registered users - can create and interact</td>
                </tr>
                <tr>
                    <td><span class="role-badge role-verified">VERIFIED</span></td>
                    <td>20</td>
                    <td>content.publish, upload.document</td>
                    <td>Email verified users - can publish content</td>
                </tr>
                <tr>
                    <td><span class="role-badge role-premium">PREMIUM</span></td>
                    <td>30</td>
                    <td>upload.large_files, feature.beta</td>
                    <td>Premium subscribers - enhanced features</td>
                </tr>
                <tr>
                    <td><span class="role-badge role-moderator">MODERATOR</span></td>
                    <td>40</td>
                    <td>content.moderate, comment.moderate</td>
                    <td>Community moderators - can moderate content</td>
                </tr>
                <tr>
                    <td><span class="role-badge role-admin">ADMIN</span></td>
                    <td>50</td>
                    <td>admin.dashboard, user.manage, user.ban</td>
                    <td>Site administrators - full management access</td>
                </tr>
                <tr>
                    <td><span class="role-badge role-superadmin">SUPERADMIN</span></td>
                    <td>60</td>
                    <td>admin.system, ALL PERMISSIONS</td>
                    <td>Super administrators - complete system access</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>🔧 Middleware Examples</h2>
        <p>The RBAC system includes middleware for protecting routes and API endpoints:</p>
        
        <h3>PHP Middleware Usage:</h3>
        <pre><code>// Require authentication
$middleware->requireAuth();

// Require specific role
$middleware->requireRole('admin');

// Require specific permission
$middleware->requirePermission('content.moderate');

// Require multiple permissions (any)
$middleware->requirePermission(['content.edit', 'content.moderate']);

// Require content ownership
$middleware->requireContentOwnership($videoId, 'video');

// Chain multiple middleware
$middleware->requireAll([
    ['method' => 'requireAuth'],
    ['method' => 'requireRole', 'params' => ['verified']],
    ['method' => 'requirePermission', 'params' => ['content.create']]
]);

// API middleware
$middleware->requireAPI(true); // Require auth for API</code></pre>
        
        <h3>Example Protected Routes:</h3>
        <pre><code>// Admin dashboard
if (!$middleware->requireRole('admin')) {
    exit; // Redirects to access denied
}

// Content creation
if (!$middleware->requirePermission('content.create')) {
    exit; // Handles access denial
}

// User management
if (!$middleware->requirePermission('user.manage')) {
    exit;
}</code></pre>
    </div>
    
    <div class="section">
        <h2>🛡️ Security Features</h2>
        <ul>
            <li><strong>Hierarchical Roles:</strong> Higher roles inherit lower role permissions</li>
            <li><strong>Custom Permissions:</strong> Grant specific permissions to individual users</li>
            <li><strong>Permission Expiration:</strong> Set expiration dates for temporary permissions</li>
            <li><strong>Context-Based Access:</strong> Content ownership and contextual permissions</li>
            <li><strong>Audit Logging:</strong> All role changes and permission grants are logged</li>
            <li><strong>Session Integration:</strong> Seamless integration with authentication system</li>
            <li><strong>Middleware Protection:</strong> Easy route and API protection</li>
            <li><strong>Caching:</strong> Permission caching for performance</li>
            <li><strong>User Management:</strong> Suspend, ban, and reinstate users</li>
            <li><strong>API Integration:</strong> Full API support with permission checking</li>
        </ul>
    </div>
    
    <script>
        function showUserActions(userId, username) {
            document.getElementById('modalUsername').textContent = username;
            document.getElementById('modalUserId').value = userId;
            document.getElementById('modalUserId2').value = userId;
            document.getElementById('modalUserId3').value = userId;
            document.getElementById('modalUserId4').value = userId;
            document.getElementById('modalUserId5').value = userId;
            document.getElementById('userModal').style.display = 'block';
        }
        
        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUserModal();
            }
        });
    </script>
</body>
</html>
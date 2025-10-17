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

defined('_ISVALID') or header('Location: /error');

/**
 * Role-Based Access Control (RBAC) System
 */
class VRBAC
{
    private static $instance = null;
    private $db;
    private $logger;
    private $auth;
    private $cache = [];
    
    // Role hierarchy (higher number = more permissions)
    const ROLE_HIERARCHY = [
        'guest' => 0,
        'member' => 10,
        'verified' => 20,
        'premium' => 30,
        'moderator' => 40,
        'admin' => 50,
        'superadmin' => 60
    ];
    
    // Permission categories
    const PERMISSIONS = [
        // Content permissions
        'content.view' => 'View content',
        'content.create' => 'Create content',
        'content.edit' => 'Edit own content',
        'content.delete' => 'Delete own content',
        'content.publish' => 'Publish content',
        'content.moderate' => 'Moderate any content',
        
        // User permissions
        'user.view_profile' => 'View user profiles',
        'user.edit_profile' => 'Edit own profile',
        'user.manage' => 'Manage other users',
        'user.ban' => 'Ban/suspend users',
        
        // Comment permissions
        'comment.view' => 'View comments',
        'comment.create' => 'Create comments',
        'comment.edit' => 'Edit own comments',
        'comment.delete' => 'Delete own comments',
        'comment.moderate' => 'Moderate any comments',
        
        // Live streaming permissions
        'stream.view' => 'View live streams',
        'stream.create' => 'Create live streams',
        'stream.manage' => 'Manage own streams',
        'stream.moderate' => 'Moderate any streams',
        
        // Upload permissions
        'upload.video' => 'Upload videos',
        'upload.image' => 'Upload images',
        'upload.audio' => 'Upload audio',
        'upload.document' => 'Upload documents',
        'upload.large_files' => 'Upload large files',
        
        // Admin permissions
        'admin.dashboard' => 'Access admin dashboard',
        'admin.settings' => 'Manage site settings',
        'admin.users' => 'Manage users',
        'admin.content' => 'Manage all content',
        'admin.reports' => 'View reports and analytics',
        'admin.logs' => 'View system logs',
        'admin.system' => 'System administration',
        
        // API permissions
        'api.access' => 'Access API',
        'api.admin' => 'Access admin API endpoints',
        
        // Special permissions
        'bypass.rate_limit' => 'Bypass rate limiting',
        'bypass.content_filter' => 'Bypass content filters',
        'feature.beta' => 'Access beta features'
    ];
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        global $class_database;
        
        $this->db = $class_database ?: new VDatabase();
        $this->logger = VLogger::getInstance();
        $this->auth = VAuth::getInstance();
    }
    
    /**
     * Check if user has permission
     * @param string $permission Permission to check
     * @param int|null $userId User ID (null for current user)
     * @param array $context Additional context for permission check
     * @return bool True if user has permission
     */
    public function hasPermission($permission, $userId = null, $context = [])
    {
        try {
            // Get user data
            if ($userId === null) {
                $user = $this->auth->getCurrentUser();
                if (!$user) {
                    return $this->checkGuestPermission($permission);
                }
                $userId = $user['user_id'];
                $userRole = $user['role'];
                $userStatus = $user['status'] ?? 'active';
            } else {
                $userData = $this->getUserData($userId);
                if (!$userData) {
                    return false;
                }
                $userRole = $userData['role'];
                $userStatus = $userData['status'];
            }
            
            // Check if user is active
            if ($userStatus !== 'active') {
                $this->logger->logSecurityEvent('Permission check for inactive user', [
                    'user_id' => $userId,
                    'status' => $userStatus,
                    'permission' => $permission
                ]);
                return false;
            }
            
            // Check role-based permissions
            if ($this->checkRolePermission($userRole, $permission)) {
                return true;
            }
            
            // Check custom user permissions
            if ($this->checkUserPermission($userId, $permission)) {
                return true;
            }
            
            // Check context-specific permissions
            if (!empty($context) && $this->checkContextPermission($userId, $permission, $context)) {
                return true;
            }
            
            // Log permission denial for security monitoring
            $this->logger->logSecurityEvent('Permission denied', [
                'user_id' => $userId,
                'role' => $userRole,
                'permission' => $permission,
                'context' => $context
            ]);
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Permission check error', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'permission' => $permission
            ]);
            return false;
        }
    }
    
    /**
     * Check if user has any of the specified permissions
     * @param array $permissions Array of permissions to check
     * @param int|null $userId User ID
     * @param array $context Additional context
     * @return bool True if user has at least one permission
     */
    public function hasAnyPermission($permissions, $userId = null, $context = [])
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $userId, $context)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if user has all specified permissions
     * @param array $permissions Array of permissions to check
     * @param int|null $userId User ID
     * @param array $context Additional context
     * @return bool True if user has all permissions
     */
    public function hasAllPermissions($permissions, $userId = null, $context = [])
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission, $userId, $context)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check if user has minimum role level
     * @param string $requiredRole Required role
     * @param int|null $userId User ID
     * @return bool True if user has required role or higher
     */
    public function hasRole($requiredRole, $userId = null)
    {
        if ($userId === null) {
            $user = $this->auth->getCurrentUser();
            if (!$user) {
                return $requiredRole === 'guest';
            }
            $userRole = $user['role'];
        } else {
            $userData = $this->getUserData($userId);
            if (!$userData) {
                return false;
            }
            $userRole = $userData['role'];
        }
        
        $userLevel = self::ROLE_HIERARCHY[$userRole] ?? 0;
        $requiredLevel = self::ROLE_HIERARCHY[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Grant permission to user
     * @param int $userId User ID
     * @param string $permission Permission to grant
     * @param string $grantedBy Who granted the permission
     * @param string|null $expiresAt Expiration date (optional)
     * @return bool Success status
     */
    public function grantPermission($userId, $permission, $grantedBy, $expiresAt = null)
    {
        try {
            // Check if permission already exists
            $sql = "SELECT permission_id FROM db_user_permissions 
                    WHERE user_id = ? AND permission = ? AND (expires_at IS NULL OR expires_at > NOW())";
            $result = $this->db->dbConnection()->Execute($sql, [$userId, $permission]);
            
            if ($result && !$result->EOF) {
                return true; // Permission already exists
            }
            
            // Insert new permission
            $insertSql = "INSERT INTO db_user_permissions (user_id, permission, granted_by, granted_at, expires_at) 
                         VALUES (?, ?, ?, NOW(), ?)";
            $insertResult = $this->db->dbConnection()->Execute($insertSql, [$userId, $permission, $grantedBy, $expiresAt]);
            
            if ($insertResult) {
                $this->logger->info('Permission granted', [
                    'user_id' => $userId,
                    'permission' => $permission,
                    'granted_by' => $grantedBy,
                    'expires_at' => $expiresAt
                ]);
                
                // Clear cache
                $this->clearUserCache($userId);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Grant permission error', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'permission' => $permission
            ]);
            return false;
        }
    }
    
    /**
     * Revoke permission from user
     * @param int $userId User ID
     * @param string $permission Permission to revoke
     * @param string $revokedBy Who revoked the permission
     * @return bool Success status
     */
    public function revokePermission($userId, $permission, $revokedBy)
    {
        try {
            $sql = "UPDATE db_user_permissions 
                    SET revoked_at = NOW(), revoked_by = ? 
                    WHERE user_id = ? AND permission = ? AND revoked_at IS NULL";
            $result = $this->db->dbConnection()->Execute($sql, [$revokedBy, $userId, $permission]);
            
            if ($result) {
                $this->logger->info('Permission revoked', [
                    'user_id' => $userId,
                    'permission' => $permission,
                    'revoked_by' => $revokedBy
                ]);
                
                // Clear cache
                $this->clearUserCache($userId);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Revoke permission error', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'permission' => $permission
            ]);
            return false;
        }
    }
    
    /**
     * Change user role
     * @param int $userId User ID
     * @param string $newRole New role
     * @param string $changedBy Who changed the role
     * @param string $reason Reason for role change
     * @return bool Success status
     */
    public function changeUserRole($userId, $newRole, $changedBy, $reason = '')
    {
        try {
            // Validate role
            if (!array_key_exists($newRole, self::ROLE_HIERARCHY)) {
                return false;
            }
            
            // Get current role
            $userData = $this->getUserData($userId);
            if (!$userData) {
                return false;
            }
            
            $oldRole = $userData['role'];
            
            // Update user role
            $sql = "UPDATE db_users SET role = ?, updated_at = NOW() WHERE user_id = ?";
            $result = $this->db->dbConnection()->Execute($sql, [$newRole, $userId]);
            
            if ($result) {
                // Log role change
                $this->logger->info('User role changed', [
                    'user_id' => $userId,
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'changed_by' => $changedBy,
                    'reason' => $reason
                ]);
                
                // Record in role history
                $historySql = "INSERT INTO db_role_history (user_id, old_role, new_role, changed_by, reason, changed_at) 
                              VALUES (?, ?, ?, ?, ?, NOW())";
                $this->db->dbConnection()->Execute($historySql, [$userId, $oldRole, $newRole, $changedBy, $reason]);
                
                // Clear cache
                $this->clearUserCache($userId);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Change role error', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'new_role' => $newRole
            ]);
            return false;
        }
    }
    
    /**
     * Suspend user account
     * @param int $userId User ID
     * @param string $reason Suspension reason
     * @param string $suspendedBy Who suspended the user
     * @param string|null $expiresAt Suspension expiry (null for permanent)
     * @return bool Success status
     */
    public function suspendUser($userId, $reason, $suspendedBy, $expiresAt = null)
    {
        try {
            // Update user status
            $sql = "UPDATE db_users SET status = 'suspended', updated_at = NOW() WHERE user_id = ?";
            $result = $this->db->dbConnection()->Execute($sql, [$userId]);
            
            if ($result) {
                // Record suspension
                $suspensionSql = "INSERT INTO db_user_suspensions (user_id, reason, suspended_by, suspended_at, expires_at) 
                                 VALUES (?, ?, ?, NOW(), ?)";
                $this->db->dbConnection()->Execute($suspensionSql, [$userId, $reason, $suspendedBy, $expiresAt]);
                
                // Invalidate user sessions
                $this->invalidateUserSessions($userId);
                
                $this->logger->warning('User suspended', [
                    'user_id' => $userId,
                    'reason' => $reason,
                    'suspended_by' => $suspendedBy,
                    'expires_at' => $expiresAt
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Suspend user error', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return false;
        }
    }
    
    /**
     * Ban user account
     * @param int $userId User ID
     * @param string $reason Ban reason
     * @param string $bannedBy Who banned the user
     * @param bool $permanent Is ban permanent
     * @return bool Success status
     */
    public function banUser($userId, $reason, $bannedBy, $permanent = false)
    {
        try {
            // Update user status
            $sql = "UPDATE db_users SET status = 'banned', updated_at = NOW() WHERE user_id = ?";
            $result = $this->db->dbConnection()->Execute($sql, [$userId]);
            
            if ($result) {
                // Record ban
                $banSql = "INSERT INTO db_user_bans (user_id, reason, banned_by, banned_at, permanent) 
                          VALUES (?, ?, ?, NOW(), ?)";
                $this->db->dbConnection()->Execute($banSql, [$userId, $reason, $bannedBy, $permanent ? 1 : 0]);
                
                // Invalidate user sessions
                $this->invalidateUserSessions($userId);
                
                $this->logger->warning('User banned', [
                    'user_id' => $userId,
                    'reason' => $reason,
                    'banned_by' => $bannedBy,
                    'permanent' => $permanent
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Ban user error', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return false;
        }
    }
    
    /**
     * Unban/unsuspend user account
     * @param int $userId User ID
     * @param string $reason Reason for lifting restriction
     * @param string $liftedBy Who lifted the restriction
     * @return bool Success status
     */
    public function reinstateUser($userId, $reason, $liftedBy)
    {
        try {
            // Update user status
            $sql = "UPDATE db_users SET status = 'active', updated_at = NOW() WHERE user_id = ?";
            $result = $this->db->dbConnection()->Execute($sql, [$userId]);
            
            if ($result) {
                // Update suspension/ban records
                $updateSql = "UPDATE db_user_suspensions SET lifted_at = NOW(), lifted_by = ?, lift_reason = ? 
                             WHERE user_id = ? AND lifted_at IS NULL";
                $this->db->dbConnection()->Execute($updateSql, [$liftedBy, $reason, $userId]);
                
                $updateBanSql = "UPDATE db_user_bans SET lifted_at = NOW(), lifted_by = ?, lift_reason = ? 
                                WHERE user_id = ? AND lifted_at IS NULL";
                $this->db->dbConnection()->Execute($updateBanSql, [$liftedBy, $reason, $userId]);
                
                $this->logger->info('User reinstated', [
                    'user_id' => $userId,
                    'reason' => $reason,
                    'lifted_by' => $liftedBy
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->logger->error('Reinstate user error', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return false;
        }
    }
    
    /**
     * Get user permissions
     * @param int $userId User ID
     * @return array Array of permissions
     */
    public function getUserPermissions($userId)
    {
        $cacheKey = "user_permissions_{$userId}";
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        try {
            $permissions = [];
            
            // Get role-based permissions
            $userData = $this->getUserData($userId);
            if ($userData) {
                $permissions = array_merge($permissions, $this->getRolePermissions($userData['role']));
            }
            
            // Get custom user permissions
            $sql = "SELECT permission FROM db_user_permissions 
                    WHERE user_id = ? AND revoked_at IS NULL 
                    AND (expires_at IS NULL OR expires_at > NOW())";
            $result = $this->db->dbConnection()->Execute($sql, [$userId]);
            
            if ($result) {
                while (!$result->EOF) {
                    $permissions[] = $result->fields['permission'];
                    $result->MoveNext();
                }
            }
            
            $permissions = array_unique($permissions);
            $this->cache[$cacheKey] = $permissions;
            
            return $permissions;
            
        } catch (Exception $e) {
            $this->logger->error('Get user permissions error', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return [];
        }
    }
    
    /**
     * Get role permissions
     * @param string $role Role name
     * @return array Array of permissions
     */
    public function getRolePermissions($role)
    {
        $cacheKey = "role_permissions_{$role}";
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        try {
            $sql = "SELECT permission FROM db_role_permissions WHERE role = ?";
            $result = $this->db->dbConnection()->Execute($sql, [$role]);
            
            $permissions = [];
            if ($result) {
                while (!$result->EOF) {
                    $permissions[] = $result->fields['permission'];
                    $result->MoveNext();
                }
            }
            
            $this->cache[$cacheKey] = $permissions;
            return $permissions;
            
        } catch (Exception $e) {
            $this->logger->error('Get role permissions error', [
                'error' => $e->getMessage(),
                'role' => $role
            ]);
            return [];
        }
    }
    
    /**
     * Permission middleware for route protection
     * @param string|array $requiredPermissions Required permission(s)
     * @param array $context Additional context
     * @return bool True if access granted
     */
    public function requirePermission($requiredPermissions, $context = [])
    {
        if (is_string($requiredPermissions)) {
            $hasPermission = $this->hasPermission($requiredPermissions, null, $context);
        } else {
            $hasPermission = $this->hasAnyPermission($requiredPermissions, null, $context);
        }
        
        if (!$hasPermission) {
            $this->handleAccessDenied($requiredPermissions, $context);
            return false;
        }
        
        return true;
    }
    
    /**
     * Role middleware for route protection
     * @param string $requiredRole Required role
     * @return bool True if access granted
     */
    public function requireRole($requiredRole)
    {
        if (!$this->hasRole($requiredRole)) {
            $this->handleAccessDenied("role:{$requiredRole}");
            return false;
        }
        
        return true;
    }
    
    /**
     * Private helper methods
     */
    
    private function checkGuestPermission($permission)
    {
        $guestPermissions = [
            'content.view',
            'comment.view',
            'stream.view',
            'user.view_profile'
        ];
        
        return in_array($permission, $guestPermissions);
    }
    
    private function checkRolePermission($role, $permission)
    {
        $rolePermissions = $this->getRolePermissions($role);
        return in_array($permission, $rolePermissions);
    }
    
    private function checkUserPermission($userId, $permission)
    {
        $userPermissions = $this->getUserPermissions($userId);
        return in_array($permission, $userPermissions);
    }
    
    private function checkContextPermission($userId, $permission, $context)
    {
        // Context-specific permission checks (e.g., content ownership)
        if (isset($context['content_owner_id']) && $context['content_owner_id'] == $userId) {
            $ownerPermissions = ['content.edit', 'content.delete', 'comment.moderate'];
            return in_array($permission, $ownerPermissions);
        }
        
        return false;
    }
    
    private function getUserData($userId)
    {
        $cacheKey = "user_data_{$userId}";
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        $sql = "SELECT user_id, username, email, role, status FROM db_users WHERE user_id = ?";
        $result = $this->db->dbConnection()->Execute($sql, [$userId]);
        
        if ($result && !$result->EOF) {
            $userData = $result->fields;
            $this->cache[$cacheKey] = $userData;
            return $userData;
        }
        
        return null;
    }
    
    private function invalidateUserSessions($userId)
    {
        $sql = "DELETE FROM db_sessions WHERE user_id = ?";
        $this->db->dbConnection()->Execute($sql, [$userId]);
    }
    
    private function clearUserCache($userId)
    {
        unset($this->cache["user_data_{$userId}"]);
        unset($this->cache["user_permissions_{$userId}"]);
    }
    
    private function handleAccessDenied($requiredPermissions, $context = [])
    {
        $user = $this->auth->getCurrentUser();
        
        $this->logger->logSecurityEvent('Access denied', [
            'user_id' => $user['user_id'] ?? null,
            'required_permissions' => $requiredPermissions,
            'context' => $context,
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
        
        // Send appropriate response based on request type
        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied. Insufficient permissions.',
                'required_permissions' => $requiredPermissions
            ]);
            exit;
        } else {
            // Redirect to access denied page or login
            if (!$user) {
                header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            } else {
                header('Location: /access-denied');
            }
            exit;
        }
    }
    
    private function isApiRequest()
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0 ||
               strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }
}
<?php
/*******************************************************************************************************************
| EasyStream Enhanced User Management
| Advanced user management with search, filtering, and bulk operations
|*******************************************************************************************************************/

define('_ISVALID', true);
session_start();

// Check if logged in
if (!$_SESSION['admin_logged_in']) {
    header("Location: admin_direct.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle actions
    if ($_POST['action']) {
        $response = ['success' => false, 'message' => ''];
        
        switch ($_POST['action']) {
            case 'update_user_status':
                $user_id = (int)$_POST['user_id'];
                $status = $_POST['status'];
                
                $stmt = $pdo->prepare("UPDATE db_accountuser SET usr_status = ? WHERE usr_id = ?");
                if ($stmt->execute([$status, $user_id])) {
                    $response = ['success' => true, 'message' => 'User status updated successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to update user status'];
                }
                break;
                
            case 'bulk_action':
                $user_ids = $_POST['user_ids'];
                $bulk_action = $_POST['bulk_action'];
                
                if (!empty($user_ids) && is_array($user_ids)) {
                    $placeholders = str_repeat('?,', count($user_ids) - 1) . '?';
                    
                    switch ($bulk_action) {
                        case 'activate':
                            $stmt = $pdo->prepare("UPDATE db_accountuser SET usr_status = 'active' WHERE usr_id IN ($placeholders)");
                            break;
                        case 'deactivate':
                            $stmt = $pdo->prepare("UPDATE db_accountuser SET usr_status = 'inactive' WHERE usr_id IN ($placeholders)");
                            break;
                        case 'delete':
                            $stmt = $pdo->prepare("UPDATE db_accountuser SET usr_status = 'deleted' WHERE usr_id IN ($placeholders)");
                            break;
                    }
                    
                    if ($stmt->execute($user_ids)) {
                        $response = ['success' => true, 'message' => 'Bulk action completed successfully'];
                    } else {
                        $response = ['success' => false, 'message' => 'Failed to complete bulk action'];
                    }
                }
                break;
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Get filter parameters
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? '';
    $sort = $_GET['sort'] ?? 'usr_datereg';
    $order = $_GET['order'] ?? 'DESC';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = 20;
    $offset = ($page - 1) * $per_page;
    
    // Build query
    $where_conditions = ["usr_status != 'deleted'"];
    $params = [];
    
    if ($search) {
        $where_conditions[] = "(usr_user LIKE ? OR usr_email LIKE ? OR usr_fname LIKE ? OR usr_lname LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    }
    
    if ($status_filter) {
        $where_conditions[] = "usr_status = ?";
        $params[] = $status_filter;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM db_accountuser WHERE $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_users = $stmt->fetchColumn();
    $total_pages = ceil($total_users / $per_page);
    
    // Get users
    $sql = "SELECT usr_id, usr_user, usr_email, usr_fname, usr_lname, usr_status, usr_datereg, 
                   (SELECT COUNT(*) FROM db_videofiles WHERE usr_id = db_accountuser.usr_id AND deleted = '0') as video_count,
                   (SELECT COUNT(*) FROM db_livefiles WHERE usr_id = db_accountuser.usr_id AND deleted = '0') as stream_count
            FROM db_accountuser 
            WHERE $where_clause 
            ORDER BY $sort $order 
            LIMIT $per_page OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get user statistics
    $stats_sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN usr_status = 'active' THEN 1 END) as active,
                    COUNT(CASE WHEN usr_status = 'inactive' THEN 1 END) as inactive,
                    COUNT(CASE WHEN usr_status = 'suspended' THEN 1 END) as suspended,
                    COUNT(CASE WHEN DATE(usr_datereg) = CURDATE() THEN 1 END) as today
                  FROM db_accountuser 
                  WHERE usr_status != 'deleted'";
    $stmt = $pdo->query($stats_sql);
    $user_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>EasyStream User Management</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5em;
            font-weight: 700;
        }
        .btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        
        /* Controls */
        .controls {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .controls-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-control {
            padding: 10px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-primary {
            background: #667eea;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background: #5a67d8;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .status-suspended {
            background: #fff3cd;
            color: #856404;
        }
        
        /* Action buttons */
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.85em;
            cursor: pointer;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-decoration: none;
            color: #495057;
        }
        .pagination .current {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        /* Bulk actions */
        .bulk-actions {
            display: none;
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            align-items: center;
            gap: 15px;
        }
        .bulk-actions.show {
            display: flex;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .controls-row {
                flex-direction: column;
                align-items: stretch;
            }
            .table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>👥 User Management</h1>
            <div>
                <a href="admin_enhanced_dashboard.php" class="btn">🏠 Dashboard</a>
                <a href="admin_direct.php?logout=1" class="btn">🚪 Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($user_stats['total']) ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($user_stats['active']) ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($user_stats['inactive']) ?></div>
                <div class="stat-label">Inactive Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($user_stats['suspended']) ?></div>
                <div class="stat-label">Suspended Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($user_stats['today']) ?></div>
                <div class="stat-label">New Today</div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls">
            <form method="GET" class="controls-row">
                <input type="text" name="search" class="form-control" placeholder="Search users..." 
                       value="<?= htmlspecialchars($search) ?>" style="min-width: 250px;">
                
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $status_filter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
                
                <select name="sort" class="form-control">
                    <option value="usr_datereg" <?= $sort === 'usr_datereg' ? 'selected' : '' ?>>Registration Date</option>
                    <option value="usr_user" <?= $sort === 'usr_user' ? 'selected' : '' ?>>Username</option>
                    <option value="usr_email" <?= $sort === 'usr_email' ? 'selected' : '' ?>>Email</option>
                    <option value="usr_status" <?= $sort === 'usr_status' ? 'selected' : '' ?>>Status</option>
                </select>
                
                <select name="order" class="form-control">
                    <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Descending</option>
                    <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                </select>
                
                <button type="submit" class="btn btn-primary">🔍 Search</button>
                <a href="?" class="btn btn-secondary">🔄 Reset</a>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulk-actions">
            <span><strong id="selected-count">0</strong> users selected</span>
            <select id="bulk-action-select" class="form-control">
                <option value="">Choose action...</option>
                <option value="activate">Activate</option>
                <option value="deactivate">Deactivate</option>
                <option value="suspend">Suspend</option>
            </select>
            <button type="button" class="btn btn-primary" onclick="executeBulkAction()">Execute</button>
            <button type="button" class="btn btn-secondary" onclick="clearSelection()">Clear</button>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>Users (<?= number_format($total_users) ?> total)</h3>
                <div>
                    <button type="button" class="btn btn-primary" onclick="toggleSelectAll()">Select All</button>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Videos</th>
                        <th>Streams</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #666;">
                                No users found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="<?= $user['usr_id'] ?>">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($user['usr_user']) ?></strong>
                                    <br><small style="color: #666;">ID: <?= $user['usr_id'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($user['usr_email']) ?></td>
                                <td><?= htmlspecialchars($user['usr_fname'] . ' ' . $user['usr_lname']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $user['usr_status'] ?>">
                                        <?= ucfirst($user['usr_status']) ?>
                                    </span>
                                </td>
                                <td><?= number_format($user['video_count']) ?></td>
                                <td><?= number_format($user['stream_count']) ?></td>
                                <td><?= date('M j, Y', strtotime($user['usr_datereg'])) ?></td>
                                <td>
                                    <?php if ($user['usr_status'] === 'active'): ?>
                                        <button class="action-btn btn-warning" onclick="updateUserStatus(<?= $user['usr_id'] ?>, 'inactive')">
                                            Deactivate
                                        </button>
                                    <?php else: ?>
                                        <button class="action-btn btn-success" onclick="updateUserStatus(<?= $user['usr_id'] ?>, 'active')">
                                            Activate
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="action-btn btn-danger" onclick="updateUserStatus(<?= $user['usr_id'] ?>, 'suspended')">
                                        Suspend
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">« Previous</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Selection management
        let selectedUsers = new Set();
        
        function updateSelectionUI() {
            const count = selectedUsers.size;
            document.getElementById('selected-count').textContent = count;
            document.getElementById('bulk-actions').classList.toggle('show', count > 0);
        }
        
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            
            selectAll.checked = !selectAll.checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
                if (selectAll.checked) {
                    selectedUsers.add(parseInt(checkbox.value));
                } else {
                    selectedUsers.delete(parseInt(checkbox.value));
                }
            });
            
            updateSelectionUI();
        }
        
        function clearSelection() {
            selectedUsers.clear();
            document.querySelectorAll('.user-checkbox, #select-all').forEach(cb => cb.checked = false);
            updateSelectionUI();
        }
        
        // Individual checkbox handling
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('user-checkbox')) {
                const userId = parseInt(e.target.value);
                if (e.target.checked) {
                    selectedUsers.add(userId);
                } else {
                    selectedUsers.delete(userId);
                }
                updateSelectionUI();
            }
        });
        
        // User status update
        function updateUserStatus(userId, status) {
            if (!confirm(`Are you sure you want to ${status} this user?`)) return;
            
            const formData = new FormData();
            formData.append('action', 'update_user_status');
            formData.append('user_id', userId);
            formData.append('status', status);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
        
        // Bulk actions
        function executeBulkAction() {
            const action = document.getElementById('bulk-action-select').value;
            if (!action) {
                alert('Please select an action');
                return;
            }
            
            if (!confirm(`Are you sure you want to ${action} ${selectedUsers.size} users?`)) return;
            
            const formData = new FormData();
            formData.append('action', 'bulk_action');
            formData.append('bulk_action', action);
            selectedUsers.forEach(userId => {
                formData.append('user_ids[]', userId);
            });
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    </script>
</body>
</html>
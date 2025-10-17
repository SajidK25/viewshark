<?php
// Quick setup with minimal form
define('_ISVALID', true);

if ($_POST['setup']) {
    try {
        $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // First, check if db_settings table exists and has correct structure
        try {
            $stmt = $pdo->query("DESCRIBE db_settings");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('cfg_value', $columns)) {
                // Table exists but wrong structure, drop and recreate
                $pdo->exec("DROP TABLE IF EXISTS db_settings");
            }
        } catch (Exception $e) {
            // Table doesn't exist, that's fine
        }
        
        // Create the settings table with correct structure
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `db_settings` (
              `cfg_name` varchar(100) NOT NULL,
              `cfg_value` text,
              PRIMARY KEY (`cfg_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Create other essential tables
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `db_users` (
              `usr_id` int(11) NOT NULL AUTO_INCREMENT,
              `usr_user` varchar(50) NOT NULL,
              `usr_email` varchar(100) NOT NULL,
              `usr_password` varchar(255) NOT NULL,
              `usr_fname` varchar(50) DEFAULT NULL,
              `usr_lname` varchar(50) DEFAULT NULL,
              `usr_status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
              `usr_signup_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `usr_last_login` datetime DEFAULT NULL,
              PRIMARY KEY (`usr_id`),
              UNIQUE KEY `idx_username` (`usr_user`),
              UNIQUE KEY `idx_email` (`usr_email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Create other tables from the SQL file
        $sql_file = file_get_contents('deploy/create_missing_tables.sql');
        $statements = explode(';', $sql_file);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !stripos($statement, 'CREATE TABLE IF NOT EXISTS `db_settings`')) {
                try {
                    $pdo->exec($statement);
                } catch (Exception $e) {
                    // Continue if table already exists
                }
            }
        }

        // Insert settings
        $settings = [
            'backend_username' => 'admin',
            'backend_password' => 'admin123',
            'backend_email' => 'admin@easystream.local',
            'site_name' => 'EasyStream',
            'setup_complete' => '1',
            'main_url' => 'http://localhost:8083'
        ];

        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO db_settings (cfg_name, cfg_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE cfg_value = ?");
            $stmt->execute([$key, $value, $value]);
        }
        
        echo "<h1>✅ Setup Complete!</h1>";
        echo "<p>Admin Panel: <a href='/admin'>http://localhost:8083/admin</a></p>";
        echo "<p>Username: admin</p>";
        echo "<p>Password: admin123</p>";
        echo "<script>setTimeout(function(){ window.location.href = '/admin'; }, 3000);</script>";
        exit;
        
    } catch (Exception $e) {
        echo "<h1>❌ Error: " . $e->getMessage() . "</h1>";
    }
}
?>

<h1>EasyStream Quick Setup</h1>
<form method="POST">
    <button type="submit" name="setup" value="1" style="padding: 20px 40px; font-size: 18px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer;">
        🚀 Setup EasyStream Now
    </button>
</form>

<p>This will create all tables and set up admin account (admin/admin123)</p>
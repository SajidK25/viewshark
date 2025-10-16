<?php
/*******************************************************************************************************************
| Example: Secure Form Implementation
| This file demonstrates how to use the new security features
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once 'f_core/config.core.php';

// Example: Handling a secure form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validate_csrf('user_update')) {
        die('CSRF token validation failed');
    }
    
    // Rate limiting check
    $user_ip = $_SERVER['REMOTE_ADDR'];
    if (!check_rate_limit('form_submit_' . $user_ip, 5, 300)) {
        die('Rate limit exceeded. Please try again later.');
    }
    
    // Secure parameter extraction
    $username = post_param('username', 'alphanum', '', ['min_length' => 3, 'max_length' => 20]);
    $email = post_param('email', 'email');
    $age = post_param('age', 'int', 0, ['min' => 13, 'max' => 120]);
    $bio = post_param('bio', 'html'); // Will be sanitized by VFilter
    
    // Validate required fields
    if (empty($username) || empty($email)) {
        $error = 'Username and email are required';
    } else {
        // Process the form (save to database, etc.)
        // The database class now uses prepared statements automatically
        
        $update_data = [
            'usr_user' => $username,
            'usr_email' => $email,
            'usr_age' => $age,
            'usr_bio' => $bio
        ];
        
        $success = $class_database->doUpdate('db_accountuser', 'usr_id', $update_data, $_SESSION['USER_ID']);
        
        if ($success) {
            log_security_event('user_profile_updated', ['user_id' => $_SESSION['USER_ID']]);
            $message = 'Profile updated successfully';
        } else {
            $error = 'Failed to update profile';
        }
    }
}

// Example: Secure file upload
if (isset($_FILES['avatar'])) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    $validation = validate_file_upload($_FILES['avatar'], $allowedTypes, $maxSize);
    
    if ($validation['valid']) {
        // Process the file upload
        $uploadDir = 'f_data/data_userfiles/avatars/';
        $filename = uniqid() . '_' . basename($_FILES['avatar']['name']);
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
            log_security_event('avatar_uploaded', ['user_id' => $_SESSION['USER_ID'], 'filename' => $filename]);
            $avatar_message = 'Avatar uploaded successfully';
        } else {
            $avatar_error = 'Failed to upload avatar';
        }
    } else {
        $avatar_error = 'Invalid file: ' . $validation['error'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Form Example</title>
</head>
<body>
    <h1>Secure User Profile Form</h1>
    
    <?php if (isset($error)): ?>
        <div style="color: red;"><?= secure_output($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($message)): ?>
        <div style="color: green;"><?= secure_output($message) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <?= csrf_field('user_update') ?>
        
        <div>
            <label>Username:</label>
            <input type="text" name="username" value="<?= secure_output(get_param('username', 'alphanum', '')) ?>" required>
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="<?= secure_output(get_param('email', 'email', '')) ?>" required>
        </div>
        
        <div>
            <label>Age:</label>
            <input type="number" name="age" value="<?= secure_output(get_param('age', 'int', '')) ?>" min="13" max="120">
        </div>
        
        <div>
            <label>Bio:</label>
            <textarea name="bio"><?= secure_output(get_param('bio', 'string', '')) ?></textarea>
        </div>
        
        <div>
            <label>Avatar:</label>
            <input type="file" name="avatar" accept="image/*">
            <?php if (isset($avatar_error)): ?>
                <div style="color: red;"><?= secure_output($avatar_error) ?></div>
            <?php endif; ?>
            <?php if (isset($avatar_message)): ?>
                <div style="color: green;"><?= secure_output($avatar_message) ?></div>
            <?php endif; ?>
        </div>
        
        <button type="submit">Update Profile</button>
    </form>
    
    <script>
        // Example of secure JavaScript output
        var userMessage = <?= secure_js($message ?? '') ?>;
        if (userMessage) {
            console.log('Success: ' + userMessage);
        }
    </script>
</body>
</html>
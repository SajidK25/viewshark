<?php
define('_ISVALID', true);
include_once __DIR__ . '/../f_core/config.core.php';

header('Content-Type: application/json');

// Require login
if (!VSession::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

$action = VSecurity::getParam('action', 'alpha', 'export');
$uid    = (int) $_SESSION['USER_ID'];

// Basic rate limit
if (!VSecurity::checkRateLimit('privacy_' . $uid, 5, 60)) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Too many requests']);
    exit;
}

switch ($action) {
    case 'export':
        // TODO: Collect actual data
        $bundle = [
            'user' => [
                'id' => $uid,
                'username' => $_SESSION['USER_NAME'] ?? null,
                'display_name' => $_SESSION['USER_DNAME'] ?? null,
            ],
            'files' => [],
            'subscriptions' => [],
        ];
        echo json_encode(['status' => 'ok', 'data' => $bundle]);
        break;

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !VSecurity::validateCSRFFromPost('privacy_delete')) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF or method']);
            exit;
        }
        // TODO: Implement soft-delete/anonymization workflow
        VLogger::getInstance()->warning('User requested account deletion', ['user_id' => $uid]);
        http_response_code(202);
        echo json_encode(['status' => 'accepted', 'message' => 'Deletion request received']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
}


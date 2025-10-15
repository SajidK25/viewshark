<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';
require_once __DIR__ . '/../config/config.php';

use Donations\AnalyticsHandler;
use Donations\GoalHandler;
use Donations\NotificationHandler;

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/f_modules/m_frontend/m_donations/api', '', $path);
$path = trim($path, '/');

// Get request body
$body = json_decode(file_get_contents('php://input'), true);

// Validate API key
$headers = getallheaders();
$api_key = $headers['Authorization'] ?? null;

if (!$api_key) {
    http_response_code(401);
    echo json_encode(['error' => 'API key is required']);
    exit();
}

// Validate API key against database
$sql = "SELECT user_id FROM api_keys WHERE api_key = ? AND is_active = 1";
$user = db()->getRow($sql, [$api_key]);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API key']);
    exit();
}

$streamer_id = $user['user_id'];

// Route requests
try {
    switch ($path) {
        case 'analytics':
            $handler = new AnalyticsHandler();
            switch ($method) {
                case 'GET':
                    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
                    $end_date = $_GET['end_date'] ?? date('Y-m-d');
                    echo json_encode($handler->getAnalytics($streamer_id, $start_date, $end_date));
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'analytics/summary':
            $handler = new AnalyticsHandler();
            switch ($method) {
                case 'GET':
                    echo json_encode($handler->getSummary($streamer_id));
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'analytics/top-donors':
            $handler = new AnalyticsHandler();
            switch ($method) {
                case 'GET':
                    $limit = $_GET['limit'] ?? 10;
                    echo json_encode($handler->getTopDonors($streamer_id, $limit));
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'goals':
            $handler = new GoalHandler();
            switch ($method) {
                case 'GET':
                    echo json_encode($handler->getStreamerGoals($streamer_id));
                    break;
                case 'POST':
                    if (!isset($body['title']) || !isset($body['target_amount'])) {
                        throw new Exception('Missing required fields');
                    }
                    $goal_id = $handler->createGoal(
                        $streamer_id,
                        $body['title'],
                        $body['description'] ?? '',
                        $body['target_amount'],
                        $body['end_date'] ?? null
                    );
                    echo json_encode(['success' => true, 'goal_id' => $goal_id]);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'goals/active':
            $handler = new GoalHandler();
            switch ($method) {
                case 'GET':
                    echo json_encode($handler->getActiveGoals($streamer_id));
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'goals/milestones':
            $handler = new GoalHandler();
            switch ($method) {
                case 'POST':
                    if (!isset($body['goal_id']) || !isset($body['title']) || !isset($body['target_amount'])) {
                        throw new Exception('Missing required fields');
                    }
                    $milestone_id = $handler->addMilestone(
                        $body['goal_id'],
                        $body['title'],
                        $body['description'] ?? '',
                        $body['target_amount'],
                        $body['reward_description'] ?? ''
                    );
                    echo json_encode(['success' => true, 'milestone_id' => $milestone_id]);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'notifications':
            $handler = new NotificationHandler();
            switch ($method) {
                case 'GET':
                    $limit = $_GET['limit'] ?? 20;
                    echo json_encode($handler->getAllNotifications($streamer_id, $limit));
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        case 'notifications/unread':
            $handler = new NotificationHandler();
            switch ($method) {
                case 'GET':
                    $limit = $_GET['limit'] ?? 10;
                    echo json_encode($handler->getUnreadNotifications($streamer_id, $limit));
                    break;
                case 'POST':
                    if (!isset($body['notification_ids'])) {
                        throw new Exception('Missing notification IDs');
                    }
                    $success = $handler->markAsRead($body['notification_ids']);
                    echo json_encode(['success' => $success]);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 
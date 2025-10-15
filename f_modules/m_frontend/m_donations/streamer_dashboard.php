<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Load Square configuration
$square_config = require_once __DIR__ . '/config.square.php';

// Initialize donation handler
require_once __DIR__ . '/donate.php';
$donation_handler = new DonationHandler($class_database);

// Get streamer information
$streamer_id = $_SESSION['user_id'];
$sql = "SELECT username, display_name, donation_balance FROM users WHERE user_id = ?";
$streamer = $class_database->getRow($sql, [$streamer_id]);

// Get donation history
$donations = $donation_handler->getDonationHistory($streamer_id);

// Handle payout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_payout'])) {
    $result = $donation_handler->requestPayout($streamer_id);
    if ($result['success']) {
        $success_message = 'Payout request submitted successfully!';
    } else {
        $error_message = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Dashboard - <?php echo htmlspecialchars($streamer['display_name'] ?? $streamer['username']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .balance-card {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .donation-table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-4">Donation Dashboard</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="balance-card">
                    <h3>Current Balance</h3>
                    <h2 class="display-4">$<?php echo number_format($streamer['donation_balance'], 2); ?></h2>
                    <?php if ($streamer['donation_balance'] >= $square_config['streamer']['min_balance']): ?>
                        <form method="post" class="mt-3">
                            <button type="submit" name="request_payout" class="btn btn-light">
                                Request Payout
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-light">
                            Minimum balance for payout: $<?php echo number_format($square_config['streamer']['min_balance'], 2); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>Donation Link</h3>
                        <p>Share this link with your viewers to receive donations:</p>
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/donate.php?streamer_id=' . $streamer_id; ?>" readonly>
                            <button class="btn btn-primary" onclick="copyToClipboard(this)">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card donation-table">
            <div class="card-body">
                <h3>Recent Donations</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Donor</th>
                                <th>Message</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($donation['created_at'])); ?></td>
                                    <td>$<?php echo number_format($donation['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($donation['donor_name'] ?: 'Anonymous'); ?></td>
                                    <td><?php echo htmlspecialchars($donation['message'] ?: '-'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $donation['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($donation['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(button) {
            const input = button.parentElement.querySelector('input');
            input.select();
            document.execCommand('copy');
            
            // Show feedback
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
    </script>
</body>
</html> 
<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';

// Load Square configuration
$square_config = require_once __DIR__ . '/config.square.php';

// Get streamer information
$streamer_id = $_GET['streamer_id'] ?? 0;
$sql = "SELECT username, display_name FROM users WHERE user_id = ?";
$streamer = $class_database->getRow($sql, [$streamer_id]);

if (!$streamer) {
    die('Invalid streamer');
}

// Initialize donation handler
require_once __DIR__ . '/donate.php';
$donation_handler = new DonationHandler($class_database);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate to <?php echo htmlspecialchars($streamer['display_name'] ?? $streamer['username']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .donation-amount-btn {
            margin: 5px;
            min-width: 80px;
        }
        .custom-amount-input {
            max-width: 150px;
        }
        .donation-form {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="donation-form">
            <h2 class="text-center mb-4">Donate to <?php echo htmlspecialchars($streamer['display_name'] ?? $streamer['username']); ?></h2>
            
            <form id="donationForm" class="needs-validation" novalidate>
                <input type="hidden" name="streamer_id" value="<?php echo $streamer_id; ?>">
                
                <div class="mb-3">
                    <label for="donorName" class="form-label">Your Name (Optional)</label>
                    <input type="text" class="form-control" id="donorName" name="donor_name">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Donation Amount</label>
                    <div class="d-flex flex-wrap justify-content-center mb-3">
                        <?php foreach ($square_config['square']['default_amounts'] as $amount): ?>
                            <button type="button" class="btn btn-outline-primary donation-amount-btn" 
                                    data-amount="<?php echo $amount; ?>">
                                $<?php echo number_format($amount, 2); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control custom-amount-input" id="customAmount" 
                               name="amount" min="<?php echo $square_config['square']['min_donation']; ?>" 
                               max="<?php echo $square_config['square']['max_donation']; ?>" 
                               step="0.01" required>
                    </div>
                    <div class="form-text">
                        Minimum: $<?php echo number_format($square_config['square']['min_donation'], 2); ?><br>
                        Maximum: $<?php echo number_format($square_config['square']['max_donation'], 2); ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="message" class="form-label">Message (Optional)</label>
                    <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Donate Now</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('donationForm');
            const customAmount = document.getElementById('customAmount');
            const amountButtons = document.querySelectorAll('.donation-amount-btn');
            
            // Handle preset amount buttons
            amountButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const amount = this.dataset.amount;
                    customAmount.value = amount;
                    customAmount.focus();
                });
            });
            
            // Form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
                
                const formData = new FormData(form);
                const data = {
                    streamer_id: formData.get('streamer_id'),
                    amount: parseFloat(formData.get('amount')),
                    donor_name: formData.get('donor_name'),
                    message: formData.get('message')
                };
                
                try {
                    const response = await fetch('process_donation.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Redirect to Square payment page
                        window.location.href = result.payment_url;
                    } else {
                        alert(result.message || 'An error occurred. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
</body>
</html> 
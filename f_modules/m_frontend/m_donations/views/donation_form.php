<?php
$title = "Donate to {$streamer['display_name']}";
include_once '../../../f_core/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Donate to <?php echo htmlspecialchars($streamer['display_name']); ?></h3>
                </div>
                <div class="card-body">
                    <form id="donation-form">
                        <input type="hidden" name="streamer_id" value="<?php echo $streamer['user_id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Donation Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       name="amount" 
                                       min="<?php echo $config['min_donation']; ?>" 
                                       max="<?php echo $config['max_donation']; ?>" 
                                       step="0.01" 
                                       required>
                            </div>
                            <div class="form-text">
                                Minimum: $<?php echo number_format($config['min_donation'], 2); ?><br>
                                Maximum: $<?php echo number_format($config['max_donation'], 2); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quick Select Amount</label>
                            <div class="btn-group w-100">
                                <?php foreach ($config['default_amounts'] as $amount): ?>
                                    <button type="button" 
                                            class="btn btn-outline-primary quick-amount" 
                                            data-amount="<?php echo $amount; ?>">
                                        $<?php echo number_format($amount, 2); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message (Optional)</label>
                            <textarea class="form-control" 
                                      name="message" 
                                      rows="3" 
                                      maxlength="200"></textarea>
                            <div class="form-text">Maximum 200 characters</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Donate Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Square Payment Form -->
<div id="payment-form-container" style="display: none;">
    <div id="payment-form"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donation-form');
    const amountInput = form.querySelector('input[name="amount"]');
    const quickAmountButtons = document.querySelectorAll('.quick-amount');
    const paymentFormContainer = document.getElementById('payment-form-container');
    const paymentForm = document.getElementById('payment-form');

    // Handle quick amount selection
    quickAmountButtons.forEach(button => {
        button.addEventListener('click', function() {
            amountInput.value = this.dataset.amount;
            amountInput.dispatchEvent(new Event('change'));
        });
    });

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        try {
            const response = await fetch('process_donation.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Initialize Square payment form
                const paymentForm = new SqPaymentForm({
                    applicationId: '<?php echo $config['application_id']; ?>',
                    locationId: '<?php echo $config['location_id']; ?>',
                    inputClass: 'sq-input',
                    inputStyles: [{
                        fontSize: '16px',
                        fontFamily: 'Helvetica Neue',
                        padding: '16px',
                        color: '#373F4A',
                        backgroundColor: 'transparent',
                        lineHeight: '20px',
                        placeholderColor: '#999',
                        _webkitFontSmoothing: 'antialiased',
                        _mozOsxFontSmoothing: 'grayscale'
                    }],
                    cardNumber: {
                        elementId: 'sq-card-number',
                        placeholder: '•••• •••• •••• ••••'
                    },
                    cvv: {
                        elementId: 'sq-cvv',
                        placeholder: 'CVV'
                    },
                    expirationDate: {
                        elementId: 'sq-expiration-date',
                        placeholder: 'MM/YY'
                    },
                    postalCode: {
                        elementId: 'sq-postal-code',
                        placeholder: 'Postal'
                    },
                    callbacks: {
                        cardNonceResponseReceived: function(err, nonce, cardData) {
                            if (err) {
                                console.error('Error generating card nonce:', err);
                                alert('Error processing payment. Please try again.');
                                return;
                            }

                            // Send payment to server
                            fetch('process_payment.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    nonce: nonce,
                                    amount: formData.get('amount'),
                                    streamer_id: formData.get('streamer_id'),
                                    message: formData.get('message')
                                })
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    alert('Thank you for your donation!');
                                    window.location.reload();
                                } else {
                                    alert(result.error || 'Error processing payment. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error processing payment. Please try again.');
                            });
                        },
                        unsupportedBrowserDetected: function() {
                            alert('Your browser is not supported. Please use a modern browser.');
                        }
                    }
                });

                // Show payment form
                paymentFormContainer.style.display = 'block';
                paymentForm.build();
            } else {
                alert(result.error || 'Error processing donation. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error processing donation. Please try again.');
        }
    });
});
</script>

<?php include_once '../../../f_core/footer.php'; ?> 
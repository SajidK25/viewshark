<?php
$title = "Donation Analytics";
include_once '../../../f_core/header.php';

use Donations\AnalyticsHandler;

$analytics_handler = new AnalyticsHandler();
$summary = $analytics_handler->getSummary($streamer_id);
$trends = $analytics_handler->getTrends($streamer_id);
$top_donors = $analytics_handler->getTopDonors($streamer_id);
?>

<div class="container mt-4">
    <h2>Donation Analytics</h2>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Donations</h5>
                    <h2 class="card-text"><?php echo number_format($summary['total_donations']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Amount</h5>
                    <h2 class="card-text">$<?php echo number_format($summary['total_amount'], 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Average Donation</h5>
                    <h2 class="card-text">$<?php echo number_format($summary['average_donation'], 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Unique Donors</h5>
                    <h2 class="card-text"><?php echo number_format($summary['unique_donors']); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Donation Trends</h5>
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <!-- Top Donors -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Top Donors</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Donations</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_donors as $donor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donor['display_name']); ?></td>
                                <td><?php echo number_format($donor['donation_count']); ?></td>
                                <td>$<?php echo number_format($donor['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for the trends chart
    const trendsData = <?php echo json_encode($trends); ?>;
    const dates = trendsData.map(item => item.date);
    const amounts = trendsData.map(item => item.total);
    const counts = trendsData.map(item => item.count);

    // Create trends chart
    const ctx = document.getElementById('trendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Daily Amount',
                data: amounts,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Number of Donations',
                data: counts,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Amount ($)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Number of Donations'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>

<?php include_once '../../../f_core/footer.php'; ?> 
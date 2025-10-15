<?php
$title = "Donation Goals";
include_once '../../../f_core/header.php';

use Donations\GoalHandler;

$goal_handler = new GoalHandler();
$active_goals = $goal_handler->getActiveGoals($streamer_id);
$all_goals = $goal_handler->getStreamerGoals($streamer_id);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Donation Goals</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGoalModal">
            Create New Goal
        </button>
    </div>

    <!-- Active Goals -->
    <div class="row mb-4">
        <div class="col-12">
            <h3>Active Goals</h3>
        </div>
        <?php foreach ($active_goals as $goal): ?>
            <?php 
            $progress = ($goal['current_amount'] / $goal['target_amount']) * 100;
            $milestones = $goal_handler->getGoalMilestones($goal['goal_id']);
            ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($goal['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($goal['description']); ?></p>
                        
                        <!-- Progress Bar -->
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo $progress; ?>%"
                                 aria-valuenow="<?php echo $progress; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?php echo number_format($progress, 1); ?>%
                            </div>
                        </div>
                        
                        <!-- Amount Info -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>$<?php echo number_format($goal['current_amount'], 2); ?> raised</span>
                            <span>Goal: $<?php echo number_format($goal['target_amount'], 2); ?></span>
                        </div>

                        <!-- Milestones -->
                        <?php if (!empty($milestones)): ?>
                            <h6>Milestones</h6>
                            <div class="list-group">
                                <?php foreach ($milestones as $milestone): ?>
                                    <?php 
                                    $milestone_progress = ($goal['current_amount'] / $milestone['target_amount']) * 100;
                                    $milestone_progress = min($milestone_progress, 100);
                                    ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($milestone['title']); ?></h6>
                                                <small><?php echo htmlspecialchars($milestone['description']); ?></small>
                                            </div>
                                            <span class="badge <?php echo $milestone['is_achieved'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo $milestone['is_achieved'] ? 'Achieved' : 'In Progress'; ?>
                                            </span>
                                        </div>
                                        <div class="progress mt-2">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $milestone_progress; ?>%"
                                                 aria-valuenow="<?php echo $milestone_progress; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                $<?php echo number_format($goal['current_amount'], 2); ?> / 
                                                $<?php echo number_format($milestone['target_amount'], 2); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Completed Goals -->
    <div class="row">
        <div class="col-12">
            <h3>Completed Goals</h3>
        </div>
        <?php foreach ($all_goals as $goal): ?>
            <?php if ($goal['status'] === 'completed'): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($goal['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($goal['description']); ?></p>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: 100%"
                                     aria-valuenow="100" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    100%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Completed on <?php echo date('M d, Y', strtotime($goal['updated_at'])); ?></span>
                                <span>Total: $<?php echo number_format($goal['current_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Goal Modal -->
<div class="modal fade" id="createGoalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Goal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createGoalForm">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="target_amount" 
                                   min="1" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date (Optional)</label>
                        <input type="date" class="form-control" name="end_date">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createGoalBtn">Create Goal</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createGoalForm = document.getElementById('createGoalForm');
    const createGoalBtn = document.getElementById('createGoalBtn');

    createGoalBtn.addEventListener('click', async function() {
        const formData = new FormData(createGoalForm);
        
        try {
            const response = await fetch('create_goal.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                window.location.reload();
            } else {
                alert(result.error || 'Error creating goal. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error creating goal. Please try again.');
        }
    });
});
</script>

<?php include_once '../../../f_core/footer.php'; ?> 
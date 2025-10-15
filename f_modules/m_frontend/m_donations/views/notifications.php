<?php
$title = "Donation Notifications";
include_once '../../../f_core/header.php';

use Donations\NotificationHandler;

$notification_handler = new NotificationHandler();
$notifications = $notification_handler->getAllNotifications($streamer_id);
$unread_count = $notification_handler->getUnreadCount($streamer_id);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Donation Notifications</h2>
        <?php if ($unread_count > 0): ?>
            <button type="button" class="btn btn-primary" id="markAllReadBtn">
                Mark All as Read
            </button>
        <?php endif; ?>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($notifications)): ?>
                <div class="text-center py-5">
                    <h5>No notifications yet</h5>
                    <p class="text-muted">You'll see notifications here when you receive donations, achieve goals, or reach milestones.</p>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-primary'; ?>"
                             data-notification-id="<?php echo $notification['notification_id']; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                                    </small>
                                </div>
                                <?php if (!$notification['is_read']): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary mark-read-btn">
                                        Mark as Read
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark individual notification as read
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', async function() {
            const notificationId = this.closest('.list-group-item').dataset.notificationId;
            
            try {
                const response = await fetch('mark_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        notification_ids: [notificationId]
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.closest('.list-group-item').classList.remove('list-group-item-primary');
                    this.remove();
                    
                    // Update unread count
                    const unreadCount = document.querySelectorAll('.list-group-item-primary').length;
                    if (unreadCount === 0) {
                        document.getElementById('markAllReadBtn')?.remove();
                    }
                } else {
                    alert(result.error || 'Error marking notification as read. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error marking notification as read. Please try again.');
            }
        });
    });

    // Mark all notifications as read
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', async function() {
            try {
                const response = await fetch('mark_all_read.php', {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.querySelectorAll('.list-group-item-primary').forEach(item => {
                        item.classList.remove('list-group-item-primary');
                    });
                    document.querySelectorAll('.mark-read-btn').forEach(btn => btn.remove());
                    this.remove();
                } else {
                    alert(result.error || 'Error marking notifications as read. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error marking notifications as read. Please try again.');
            }
        });
    }
});
</script>

<?php include_once '../../../f_core/footer.php'; ?> 
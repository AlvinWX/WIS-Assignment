<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users (Only Admins can view and block/unblock users)
auth('admin'); // Ensure only admins can access this page

// Get logged-in admin tier (e.g., 'high', 'low')
$loggedInAdminTier = $_SESSION['admin_tier'] ?? 'Low'; // Default to 'low' if not set

// Fetch all admins and members
$admins = $_db->query('SELECT admin_id, admin_name, status FROM admin')->fetchAll();
$members = $_db->query('SELECT member_id, member_name, status FROM member')->fetchAll();

// Handle block/unblock request
if (is_post()) {
    $user_id = req('user_id');
    $user_type = req('user_type');

    if ($user_id == '' || ($user_type != 'member' && $user_type != 'admin')) {
        $_err['user'] = 'Invalid user or user type';
    }

    if (!$_err) {
        // Determine the table and field based on user type
        if ($user_type == 'member') {
            $table = 'member';
        } else {
            $table = 'admin';
        }

        // Fetch current status
        $stm = $_db->prepare("SELECT status FROM $table WHERE ${user_type}_id = ?");
        $stm->execute([$user_id]);
        $current_status = $stm->fetchColumn();

        // Toggle the status between 'active' and 'blocked'
        $new_status = ($current_status == 'active') ? 'blocked' : 'active';

        // Update the status
        $stm = $_db->prepare("UPDATE $table SET status = ? WHERE ${user_type}_id = ?");
        $stm->execute([$new_status, $user_id]);

        temp('info', ucfirst($user_type) . ' status updated to ' . $new_status);
        redirect('/page/lauwenjie/user/block_unblock.php'); // Refresh the page after action
    }
}

// ----------------------------------------------------------------------------

$_title = 'Admin | Manage Users';
include '../../../_head.php';
?>
<link rel="stylesheet" href="/css/wj_app.css">
<div class="block-con"></div>
<div class="block-container">
<h2>Admins</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($admins as $a): ?>
    <tr>
        <td><?= htmlspecialchars($a->admin_id) ?></td>
        <td><?= htmlspecialchars($a->admin_name) ?></td>
        <td class="<?= htmlspecialchars($a->status) ?>"><?= ucfirst($a->status) ?></td>
        <td>
            <?php if ($loggedInAdminTier === 'High'): ?>
                <?php if ($a->status === 'active'): ?>
                    <button class="red-btn" data-id="<?= $a->admin_id ?>" data-type="admin" data-action="block">Block</button>
                <?php elseif ($a->status === 'blocked'): ?>
                    <button class="blue-btn" data-id="<?= $a->admin_id ?>" data-type="admin" data-action="unblock">Unblock</button>
                <?php endif; ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="block-container">
<h2>Members</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($members as $member): ?>
            <tr>
                <td><?= htmlspecialchars($member->member_id) ?></td>
                <td><?= htmlspecialchars($member->member_name) ?></td>
                <td class="<?= htmlspecialchars($member->status) ?>"><?= ucfirst($member->status) ?></td>
                <td>
                    <form method="post" style="display:inline;" id="blockUnblockForm">
                        <input type="hidden" name="user_id" value="<?= $member->member_id ?>">
                        <input type="hidden" name="user_type" value="member">
                        <button type="button" class="confirm-action-btn" data-id="<?= $member->member_id ?>" data-type="member" data-status="<?= $member->status ?>">
                            <?= $member->status == 'active' ? 'Block' : 'Unblock' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
<script>
    // Handle the double confirmation
    const confirmButtons = document.querySelectorAll('.confirm-action-btn');
    let selectedButton = null;

    confirmButtons.forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const action = status === 'active' ? 'block' : 'unblock';
            selectedButton = this;

            // Show the confirmation alert box
            const isConfirmed = window.confirm("Are you sure you want to " + action + " this user?");
            
            if (isConfirmed) {
                // Submit the form for the selected button
                const form = selectedButton.closest('form');
                form.submit();
            }
        });
    });
</script>

<?php
include '../../../_foot.php';
?>

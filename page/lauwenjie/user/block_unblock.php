<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users (Only Admins can view and block/unblock users)
auth('admin'); // Ensure only admins can access this page

// Get logged-in admin tier (e.g., 'high', 'low')
$loggedInAdminTier = $_SESSION['admin_tier'] ?? 'low'; // Default to 'low' if not set

// Get the filter and search criteria from the GET request
$filter_type = req('filter_type', 'member'); // Default to 'member' if not set
$search_id = req('search_id', ''); // Search by ID

// Prepare the base query for admins and members
$admin_query = 'SELECT admin_id, admin_name, status FROM admin WHERE 1';
$member_query = 'SELECT member_id, member_name, status FROM member WHERE 1';

// If a search ID is provided, add it to the query
if ($search_id) {
    if ($filter_type === 'admin') {
        $admin_query .= ' AND admin_id = :search_id';
    } else {
        $member_query .= ' AND member_id = :search_id';
    }
}

// Fetch admins and members based on filter type and search criteria
$admin_stm = $_db->prepare($admin_query);
$member_stm = $_db->prepare($member_query);

// Bind the search ID parameter if it's provided
if ($search_id) {
    $admin_stm->bindValue(':search_id', $search_id);
    $member_stm->bindValue(':search_id', $search_id);
}

// Execute the queries
$admins = $admin_stm->fetchAll();
$members = $member_stm->fetchAll();

// Handle block/unblock request
if (is_post()) {
    $user_id = req('user_id');
    $user_type = req('user_type');

    if ($user_id == '' || ($user_type != 'member' && $user_type != 'admin')) {
        $_err['user'] = 'Invalid user or user type';
    }

    if (!$_err) {
        // Determine the table and field based on user type
        $table = $user_type === 'member' ? 'member' : 'admin';

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
<style>
    .filter-container{
        margin-top: 200px;
    }
</style>
<!-- Filter and Search Form -->
<div class="filter-container">
    <form method="get">
        <div class="filter-type">
            <select name="filter_type">
                <option value="member" <?= $filter_type === 'member' ? 'selected' : '' ?>>Members</option>
                <option value="admin" <?= $filter_type === 'admin' ? 'selected' : '' ?>>Admins</option>
            </select>
        </div>
        <div class="search-id">
            <input type="text" name="search_id" placeholder="Search by ID" value="<?= htmlspecialchars($search_id) ?>">
        </div>
        <button type="submit">Filter/Search</button>
    </form>
</div>

<!-- Admins Table -->
<?php if ($filter_type === 'admin'): ?>
<div class="login-container">
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
                <?php if ($loggedInAdminTier === 'high'): ?>
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
<?php endif; ?>

<!-- Members Table -->
<?php if ($filter_type === 'member'): ?>
<div class="login-container">
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
<?php endif; ?>

<script>
    // Handle the confirmation for blocking/unblocking
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

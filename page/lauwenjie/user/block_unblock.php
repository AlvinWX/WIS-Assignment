<?php
include '../../../_base.php';

auth('admin');

$loggedInAdminTier = $_SESSION['admin_tier'] ?? 'Low';

// Get search, filter, and sort parameters
$search = req('search') ?? '';
$filter_status = req('status_filter') ?? '';
$sort_order = req('sort') === 'desc' ? 'DESC' : 'ASC';

// Build query for admins
$query = "SELECT admin_id, admin_name, status FROM admin";
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "admin_name LIKE ?";
    $params[] = '%' . $search . '%';
}
if ($filter_status) {
    $conditions[] = "status = ?";
    $params[] = $filter_status;
}
if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}
$query .= " ORDER BY admin_id $sort_order";
$stm = $_db->prepare($query);
$stm->execute($params);
$admins = $stm->fetchAll();

// Build query for members
$query = "SELECT member_id, member_name, status FROM member";
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "member_name LIKE ?";
    $params[] = '%' . $search . '%';
}
if ($filter_status) {
    $conditions[] = "status = ?";
    $params[] = $filter_status;
}
if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}
$query .= " ORDER BY member_id $sort_order";
$stm = $_db->prepare($query);
$stm->execute($params);
$members = $stm->fetchAll();

if (is_post()) {
    $user_id = req('user_id');
    $user_type = req('user_type');

    if ($user_id == '' || ($user_type != 'member' && $user_type != 'admin')) {
        $_err['user'] = 'Invalid user or user type';
    }

    if (!$_err) {
        $table = ($user_type == 'member') ? 'member' : 'admin';

        $stm = $_db->prepare("SELECT status FROM $table WHERE ${user_type}_id = ?");
        $stm->execute([$user_id]);
        $current_status = $stm->fetchColumn();

        $new_status = ($current_status == 'active') ? 'blocked' : 'active';

        $stm = $_db->prepare("UPDATE $table SET status = ? WHERE ${user_type}_id = ?");
        $stm->execute([$new_status, $user_id]);

        temp('info', ucfirst($user_type) . ' status updated to ' . $new_status);
        redirect('/page/lauwenjie/user/block_unblock.php');
    }
}

$_title = 'Admin | Manage Users';
include '../../../_head.php';
?>
<link rel="stylesheet" href="/css/wj_app.css">
<div class="block-con">
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>">
        <select name="status_filter">
            <option value="">All</option>
            <option value="active" <?= $filter_status == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="blocked" <?= $filter_status == 'blocked' ? 'selected' : '' ?>>Blocked</option>
        </select>
        <select name="sort">
            <option value="asc" <?= $sort_order == 'ASC' ? 'selected' : '' ?>>Sort by ID (ASC)</option>
            <option value="desc" <?= $sort_order == 'DESC' ? 'selected' : '' ?>>Sort by ID (DESC)</option>
        </select>
        <button type="submit">Apply</button>
    </form>

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
                        <button class="confirm-action-btn" data-id="<?= $a->admin_id ?>" data-type="admin" data-status="<?= $a->status ?>">
                            <?= $a->status == 'active' ? 'Block' : 'Unblock' ?>
                        </button>
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
                    <button class="confirm-action-btn" data-id="<?= $member->member_id ?>" data-type="member" data-status="<?= $member->status ?>">
                        <?= $member->status == 'active' ? 'Block' : 'Unblock' ?>
                    </button>
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
<?php include '../../../_foot.php'; ?>

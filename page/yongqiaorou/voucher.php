<?php
require '../../_base.php';
//-----------------------------------------------------------------------------


$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

// ----------------------------------------------------------------------------
$_title = 'Voucher List';
include '../../_admin_head.php';
?>

<form method="GET" action="" class="search-form" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <!-- Product Search Fields -->
    <div style="display: flex; align-items: center; gap: 10px;">
        <select id="searchField" name="search_field" style="padding: 5px; width: 150px;">
            <option value="voucher_name" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'voucher_name' ? 'selected' : '' ?>>Voucher Name</option>
            <option value="voucher_desc" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'voucher_desc' ? 'selected' : '' ?>>Description</option>
            <option value="voucher_points" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'voucher_points' ? 'selected' : '' ?>>Points to Redeem</option>
            <option value="voucher_min_spend" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'voucher_min_spend' ? 'selected' : '' ?>>Min Spend</option>
            <option value="voucher_discount" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'voucher_discount' ? 'selected' : '' ?>>Discounts</option>
        </select>

        <!-- Dynamic input fields -->
        <div id="textInputGroup" style="display: none; flex: 1;">
            <input type="text" id="textInput" name="search" placeholder="Search" style="padding: 5px; width: 220px;" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>

        <div id="numberInputGroup" style="display: none; flex: 1;">
            <input type="number" min="0.01" max="100000" step="0.01" id="minValueInput" name="min_value" placeholder="Min Value" style="padding: 5px; width: 100px;" value="<?= isset($_GET['min_value']) ? htmlspecialchars($_GET['min_value']) : '' ?>">
            <input type="number" min="0.01" max="100000" step="0.01" id="maxValueInput" name="max_value" placeholder="Max Value" style="padding: 5px; width: 100px;" value="<?= isset($_GET['max_value']) ? htmlspecialchars($_GET['max_value']) : '' ?>">
        </div>

        <button type="submit" style="padding: 5px 10px;">Search</button>
    </div>

</form>

<?php
$query = 'SELECT * FROM voucher v WHERE v.voucher_status = 1';
$bindValues = [];

$min_value = isset($_GET['min_value']) ? floatval($_GET['min_value']) : null;
$max_value = isset($_GET['max_value']) ? floatval($_GET['max_value']) : null;
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : null;
$search_value = isset($_GET['search']) ? $_GET['search'] : null;

if (!empty($min_value) && !empty($max_value) && is_numeric($min_value) && is_numeric($max_value)) {
    $query .= " AND $search_field >= :min_value AND $search_field <= :max_value";
    $bindValues[':min_value'] = $min_value;
    $bindValues[':max_value'] = $max_value;
} elseif (!empty($search_value)) {
    $query .= " AND $search_field LIKE :search_value";
    $bindValues[':search_value'] = '%' . $search_value . '%';
}

$stmt = $_db->prepare($query);
foreach ($bindValues as $param => $value) {
    $stmt->bindValue($param, $value);
}
$stmt->execute();
$arr = $stmt->fetchAll();

if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>

<div style="text-align: center; margin-top: 20px;">
<table class="table" style="margin-left: auto; margin-right: auto;">
    <tr>
        <th>Id</th>
        <th>Voucher Name</th>
        <th>Description</th>
        <th>Points to Redeem</th>
        <th>Min Spend</th>
        <th>Discounts</th>
        <th>Voucher Image</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($arr as $v): ?>
    <tr>
        <td><?= $v->voucher_id ?></td>
        <td><?= $v->voucher_name ?></td>
        <td><?= $v->voucher_desc ?></td>
        <td><?= $v->voucher_points ?></td>
        <td><?= $v->voucher_min_spend ?></td>
        <td><?= $v->voucher_discount ?></td>
        <td><img src="../../images/voucher_pic/<?= $v->voucher_img ?>"></td>
        <td>
            <button data-get="voucher_detail.php?id=<?= $v->voucher_id ?>">Detail</button><br>
            <button data-get="voucher_update.php?id=<?= $v->voucher_id ?>">Update</button><br>
            <button data-post="voucher_delete.php?id=<?= $v->voucher_id ?> "data-confirm>Delete</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
</div> 
<?php }else{?>
    <p style="color:red;">No record found.</p>
<?php }?>
<a href="voucher_recover.php"><span id="dot" class="dot_left" style="color:rgb(245, 167, 167);"><i class="fa fa-trash" aria-hidden="true"></i></span></a>
<a href="voucher_add.php"><span id="dot" class="dot_right"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchField = document.getElementById('searchField');
        const textInputGroup = document.getElementById('textInputGroup');
        const numberInputGroup = document.getElementById('numberInputGroup');
        const minInput = document.getElementById('minValueInput');
        const maxInput = document.getElementById('maxValueInput');

        // Function to toggle input fields based on selected option
        const toggleInputFields = () => {
            const selectedValue = searchField.value;

            if (selectedValue === 'voucher_points' || selectedValue === 'voucher_min_spend' || selectedValue === 'voucher_discount') {
                numberInputGroup.style.display = 'block';
                textInputGroup.style.display = 'none';
            } else {
                numberInputGroup.style.display = 'none';
                textInputGroup.style.display = 'block';
            }
        };

        // Prevent users from entering 0 or negative values
        const validateInput = (event) => {
            const value = parseFloat(event.target.value);
            if (value <= 0) {
                alert('Value must be greater than 0.');
                event.target.value = ''; // Clear the input field
            }
        };

        // Initialize on page load
        toggleInputFields();

        // Add event listener for dropdown changes
        searchField.addEventListener('change', toggleInputFields);

        // Add event listeners for input validation
        minInput.addEventListener('change', validateInput);
        maxInput.addEventListener('change', validateInput);
    });
</script>

<?php
include '../../_foot.php';
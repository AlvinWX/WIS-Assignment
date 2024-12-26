<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM category WHERE category_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    extract((array)$s);
}

if (is_post()) {
    $id = req('id'); // <-- From URL
    $category_name = req('category_name');
    $category_desc = req('category_desc');

    // Validate name
    if ($category_name == '') {
        $_err['category_name'] = 'Required';
    } else if (strlen($category_name) > 100) {
        $_err['category_name'] = 'Maximum length 100';
    }

    // Validate desc
    if ($category_desc == '') {
        $_err['category_desc'] = 'Required';
    } else if (strlen($category_desc) > 1000) {
        $_err['category_desc'] = 'Maximum length 1000';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE category
                            SET category_name = ?, category_desc = ?, category_last_update = ?, admin_id = ?
                            WHERE category_id = ?');
        $stm->execute([$category_name, $category_desc, date("Y-m-d H:i:s"), $admin_id, $id]);

        temp('info', 'Category updated');
        redirect('/page/yongqiaorou/category.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update';
include '../../_admin_head.php';
?>

<button data-get="/page/yongqiaorou/category.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<form method="post" class="form">
    <label for="category_id">Id</label>
    <b><?= $id ?></b>
    <?= err('id') ?>

    <label for="category_name">Category Name</label>
    <?= html_text('category_name', 'maxlength="100"') ?>
    <?= err('category_name') ?>

    <label>Description</label>
    <?= html_text('category_desc', 'maxlength="1000"') ?>
    <?= err('category_desc') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_admin_foot.php';
?>

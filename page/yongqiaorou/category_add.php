<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

if (is_post()) {
    // Input
    $category_id         = req('category_id');
    $category_name       = req('category_name');
    $category_desc     = req('category_desc');

    if ($category_name == '') {
        $_err['category_name'] = 'Required';
    }
    else if (strlen($category_name) > 100) {
        $_err['category_name'] = 'Maximum length 100';
    }

    // Validate desc
    if ($category_desc == '') {
        $_err['category_desc'] = 'Required';
    }
    else if (strlen($category_desc) > 1000) {
        $_err['category_desc'] = 'Maximum length 1000';
    }

    // Output
    if (!$_err) {
        $arr = $_db->query('SELECT * FROM category ORDER BY category_id DESC LIMIT 1')->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($arr)) {
            $category_id = $arr[0]['category_id'];
            $numeric_part = substr($category_id, 2);
            $incremented_numeric = str_pad((int)$numeric_part + 1, strlen($numeric_part), '0', STR_PAD_LEFT);
            $category_id = "PC" . $incremented_numeric;
        } else {
            $category_id = "PC00001";
        }

        $stm = $_db->prepare('INSERT INTO category
                              (category_id, category_name, category_desc, category_last_update)
                              VALUES(?, ?, ?, ?)');
        $stm->execute([$category_id, $category_name, $category_desc, date("Y-m-d H:i:s")]);

        temp('info', 'Category added.');
        redirect('category.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Insert';
include '../../_admin_head.php';
?>

<button data-get="/page/yongqiaorou/category.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<form method="post" class="form">
    <label for="id">Id</label>
    <?php 
    $arr = $_db->query('SELECT * FROM category ORDER BY category_id DESC LIMIT 1')->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($arr)) {
        $category_id = $arr[0]['category_id'];
        $numeric_part = substr($category_id, 2); 
        $incremented_numeric = str_pad((int)$numeric_part + 1, strlen($numeric_part), '0', STR_PAD_LEFT);
        $category_id = "PC" . $incremented_numeric;
        echo $category_id;
    } else {
        $category_id = "PC00001";
        echo $category_id;
    }
    ?>
    <?= err('') ?>
    
    <label for="category_name">Category Name</label>
    <?= html_text('category_name', 'maxlength="100"') ?>
    <?= err('category_name') ?>

    <label>Description</label>
    <?= html_text('category_desc',  'maxlength="1000"') ?>
    <?= err('category_desc') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>
<?php
include '../../_admin_foot.php';
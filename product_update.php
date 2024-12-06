<?php
require '_base.php';
// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    /*$name = $s->name;
    $gender = $s->gender;
    $program_id = $s->program_id;*/

    extract((array)$s);

}

if (is_post()) {
    // Input
    $id         = req('id'); // <-- From URL
    $product_name       = req('product_name');
    $product_desc     = req('product_desc');
    $product_price = req('product_price');
    $product_stock = req('product_stock');
    $category_id = req('category_id');

    // Validate id <-- NO NEED
    
    // Validate name
    if ($product_name == '') {
        $_err['product_name'] = 'Required';
    }
    else if (strlen($product_name) > 100) {
        $_err['product_name'] = 'Maximum length 100';
    }

    // Validate desc
    if ($product_desc == '') {
        $_err['product_desc'] = 'Required';
    }
    else if (strlen($product_desc) > 1000) {
        $_err['product_desc'] = 'Maximum length 1000';
    }

    // Validate program_id
    if ($product_price == '') {
        $_err['product_price'] = 'Required';
    }

    // Validate program_id
    if ($product_stock == '') {
        $_err['product_stock'] = 'Required';
    }

    // Validate program_id
    if ($category_id == '') {
        $_err['category_id'] = 'Required';
    }
    else if (!array_key_exists($category_id, $_categories)) {
        $_err['category_id'] = 'Invalid value';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE product
                              SET product_name = ?, product_desc = ?, product_price = ?, product_stock = ?, category_id = ?
                              WHERE product_id = ?');
        $stm->execute([$product_name, $product_desc, $product_price, $product_stock, $category_id, $id]);

        temp('info', 'Product updated');
        redirect('/product.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update';
include '_admin_head.php';
?>

<form method="post" class="form">
    <label for="product_id">Id</label>
    <b><?= $id ?></b>
    <?= err('id') ?>
    
    <label for="product_name">Product Name</label>
    <?= html_text('product_name', 'maxlength="100"') ?>
    <?= err('product_name') ?>

    <label>Description</label>
    <?= html_text('product_desc',  'maxlength="1000"') ?>
    <?= err('product_desc') ?>

    <label>Product Price</label>
    <?= html_number('product_price', 0, 10000, 0.01, 'placeholder="0.00"') ?>
    <?= err('product_price') ?>

    <label>Current Stock</label>
    <?= html_number('product_stock', 0, 1000000, 1, 'placeholder="0"') ?>
    <?= err('product_stock') ?>

    <label for="category_id">Category</label>
    <?= html_select('category_id', $_categories) ?>
    <?= err('category_id') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '_admin_foot.php';
<?php
require '_base.php';
// ----------------------------------------------------------------------------

if (is_post()) {
    // Input
    $product_id         = req('product_id');
    $product_name       = req('product_name');
    $product_desc     = req('product_desc');
    $product_price = req('product_price');
    $product_stock = req('product_stock');
    $category_id = req('category_id');
    // $product_photo = req('product_photo');
    
    // $product_photo = basename($product_photo);
    // unlink("images/$product_photo");
        
    // temp('info', 'Photo deleted');
    // redirect('demo2.php');
    // Validate id
    // if ($id == '') {
    //     $_err['id'] = 'Required';
    // }
    // else if (!preg_match('/^\d{2}[A-Z]{3}\d{5}$/', $id)) {
    //     $_err['id'] = 'Invalid format';
    // }
    // else if (!is_unique($id, 'student', 'id')) {
    //     $_err['id'] = 'Duplicated';
    // }
    
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

    
    $f = get_file('product_photo');

    // Validate: photo (file)
    if ($f == null) {
        $_err['product_photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['product_photo'] = 'Must be image';
    }
    else if ($f->size > 1 * 1024 * 1024) {
        $_err['product_photo'] = 'Maximum 1MB';
    }

    if (!$_err) {
        //move_uploaded_file($f->tmp_name, "uploads/$f->name");
        
    }

    // Output
    if (!$_err) {
        $product_photo = uniqid() . '.jpg';

        require_once 'lib/SimpleImage.php';
        $img = new SimpleImage();
        $img->fromFile($f->tmp_name)
            ->thumbnail(200,200)
            ->toFile("images/$product_photo",'image/jpeg');
        // $fullUrl = $protocol . $host . rtrim($path, '/') . "/images/$product_photo";
        $arr = $_db->query('SELECT * FROM product ORDER BY product_id DESC LIMIT 1')->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($arr)) {
            $product_id = $arr[0]['product_id'];
            $numeric_part = substr($product_id, 2); 
            $incremented_numeric = str_pad((int)$numeric_part + 1, strlen($numeric_part), '0', STR_PAD_LEFT);
            $product_id = "PD" . $incremented_numeric;
        } else {
            $product_id = "PD00001";
        }
        $stm = $_db->prepare('INSERT INTO product
                              (product_id, product_name, product_img, product_desc, product_price, product_stock, product_last_update, admin_id, category_id)
                              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stm->execute([$product_id, $product_name, $product_photo, $product_desc, $product_price, $product_stock, date("Y-m-d H:i:s"), 0, $category_id]);
        temp('info', 'Product added.');
        redirect('product.php');
    }


}

// ----------------------------------------------------------------------------
$_title = 'Insert';
include '_admin_head.php';
?>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="id">Id</label>
    <?php 
    $arr = $_db->query('SELECT * FROM product ORDER BY product_id DESC LIMIT 1')->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($arr)) {
        $product_id = $arr[0]['product_id'];
        $numeric_part = substr($product_id, 2); 
        $incremented_numeric = str_pad((int)$numeric_part + 1, strlen($numeric_part), '0', STR_PAD_LEFT);
        $product_id = "PD" . $incremented_numeric;
        echo $product_id;
    } else {
        $product_id = "PD00001";
        echo $product_id;
    }
    ?>
    <?= err('') ?>
    
    <label for="product_name">Product Name</label>
    <?= html_text('product_name', 'maxlength="100"') ?>
    <?= err('product_name') ?>

    <label for="product_photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('product_photo','image/*') ?>
        <img src="/images/product_photo.jpg">
    </label>
    <?= err('product_photo') ?>

    <label>Description</label>
    <?= html_text('product_desc',  'maxlength="10"') ?>
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
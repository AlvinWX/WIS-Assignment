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

    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    extract((array)$s);
}

if (is_post()) {
    $id = req('id'); // <-- From URL
    $product_name = req('product_name');
    $product_cover = req('product_cover');
    $product_resources = req('product_resources');
    $product_youtube_url = req('product_youtube_url');
    $product_desc = req('product_desc');
    $product_price = req('product_price');
    $product_stock = req('product_stock');
    $category_id = req('category_id');

    // Validate name
    if ($product_name == '') {
        $_err['product_name'] = 'Required';
    } else if (strlen($product_name) > 100) {
        $_err['product_name'] = 'Maximum length 100';
    }

    // Validate desc
    if ($product_desc == '') {
        $_err['product_desc'] = 'Required';
    } else if (strlen($product_desc) > 1000) {
        $_err['product_desc'] = 'Maximum length 1000';
    }

    // Validate price
    if ($product_price == '') {
        $_err['product_price'] = 'Required';
    }

    // Validate stock
    if ($product_stock == '') {
        $_err['product_stock'] = 'Required';
    }

    // Validate category_id
    if ($category_id == '') {
        $_err['category_id'] = 'Required';
    } else if (!array_key_exists($category_id, $_categories)) {
        $_err['category_id'] = 'Invalid value';
    }

    $cover_file = isset($_FILES['product_cover']) ? $_FILES['product_cover'] : null;
    if ($cover_file && $cover_file['error'] == UPLOAD_ERR_OK) {
        $product_cover = uniqid() . '.jpg';  // Generate a unique file name

        require_once '../../lib/SimpleImage.php';
        $img = new SimpleImage();
        $img->fromFile($cover_file['tmp_name'])
            ->thumbnail(200, 200)
            ->toFile("images/$product_cover", 'image/jpeg');
    }

    // Handle product_photo (multiple images)
    $photo_files = isset($_FILES['product_photo']) ? $_FILES['product_photo'] : json_decode($s['product_resources'], true);
    $photo_resources = [];
    if ($photo_files && is_array($photo_files['name'])) {
        foreach ($photo_files['name'] as $index => $name) {
            $tmp_name = $photo_files['tmp_name'][$index];
            if(!empty($tmp_name)){
                $type = mime_content_type($tmp_name);
                $size = $photo_files['size'][$index];
    
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $_err['product_photo'] = 'All files must be images';
                } else if ($size > 1 * 1024 * 1024) {
                    $_err['product_photo'] = 'Each image must be under 1MB';
                } else {
                    if(empty($product_cover)){
                        $_err['product_cover'] = 'Cover Picture is required';
                    }else{
                        $unique_name = uniqid() . '.' . pathinfo($name, PATHINFO_EXTENSION);
                        move_uploaded_file($tmp_name, "../../uploads/$unique_name");
                        $photo_resources[] = $unique_name;
                    }
                }
            }
            
        }
        
    }

    // Output
    if (!$_err) {
        if(json_encode($photo_resources) != "[]"){
            $stm = $_db->prepare('UPDATE product
                                SET product_name = ?, product_desc = ?, product_price = ?, product_stock = ?, product_cover = ?, product_resources = ?, product_youtube_url, admin_id = ?, product_last_update = ?, category_id = ?
                                WHERE product_id = ?');
            $stm->execute([$product_name, $product_desc, $product_price, $product_stock, $product_cover, json_encode($photo_resources), $product_youtube_url, $admin_id, date("Y-m-d H:i:s"), $category_id, $id]);

            temp('info', 'Product updated');
            redirect('/page/yongqiaorou/product.php');

        }else{
            $stm = $_db->prepare('UPDATE product
                                SET product_name = ?, product_desc = ?, product_price = ?, product_stock = ?,  product_youtube_url =? , admin_id = ?, product_last_update = ?, category_id = ?
                                WHERE product_id = ?');
            $stm->execute([$product_name, $product_desc, $product_price, $product_stock, $product_youtube_url, $admin_id, date("Y-m-d H:i:s"), $category_id, $id]);

            temp('info', 'Product updated');
            redirect('/page/yongqiaorou/product.php');
        }
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update';
include '../../_admin_head.php';
?>

<button data-get="/page/yongqiaorou/product.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="product_id">Id</label>
    <b><?= $id ?></b>
    <?= err('id') ?>

    <label for="product_name">Product Name</label>
    <?= html_text('product_name', 'maxlength="100"') ?>
    <?= err('product_name') ?>

    <label>Description</label>
    <?= html_text('product_desc', 'maxlength="1000"') ?>
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

    <label for="product_cover">Cover Picture</label>
    <div>
        <?= html_file('product_cover', 'image/*', 'hidden id="product_cover"') ?>
        <img id="preview" src="images/<?= $product_cover ?>" style="width: 200px; height: 200px;">
    </div>
    <?= err('product_cover') ?>

    <label for="product_photo">Extra Resources</label>
    <label class="upload" tabindex="0">
        <?= html_file('product_photo[]', 'image/*', 'multiple') ?>
    </label>
    <div id="product_photo_previews">
    <?= err('product_photo') ?></div>
    
    <label>Youtube URL</label>
    <?= html_text('product_youtube_url',  'maxlength="1000"') ?>
    <?= err('product_youtube_url') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<script>
    existingResources.forEach(resource => {
        const previewElement = document.createElement('img');  // Always create an img element for image resources

        previewElement.src = `../../uploads/${resource}`;  // Ensure correct path for image resource
        previewElement.style.maxWidth = '200px'; 
        previewElement.style.margin = '5px'; 
        
        // Append the image preview to the preview container
        $('#product_photo_previews').append(previewElement);
    });

</script>

<?php
include '../../_foot.php';
?>

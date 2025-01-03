<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_post()) {
    // Input
    $product_id         = req('product_id');
    $product_name       = req('product_name');
    $product_desc     = req('product_desc');
    $product_price = req('product_price');
    $product_stock = req('product_stock');
    $product_youtube_url = req('product_youtube_url');
    $category_id = req('category_id');

    // $product_cover = req('product_cover');
    
    // $product_cover = basename($product_cover);
    // unlink("images/$product_cover");
        
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
    }
    else if (!array_key_exists($category_id, $_categories)) {
        $_err['category_id'] = 'Invalid value';
    }

    
    // Handle product_cover (single image)
    $cover_file = isset($_FILES['product_cover']) ? $_FILES['product_cover'] : null;
    if ($cover_file && $cover_file['error'] == UPLOAD_ERR_OK) {
        $product_cover = uniqid() . '.jpg'; 

        require_once '../../lib/SimpleImage.php';
        $img = new SimpleImage();
        $img->fromFile($cover_file['tmp_name'])
            ->thumbnail(200, 200)
            ->toFile("../../images/product_pic/$product_cover", 'image/jpeg');
    } else {
        $_err['product_cover'] = 'Cover Picture is required';
    }

    // Handle product_photo (multiple images)
    $photo_files = isset($_FILES['product_photo']) ? $_FILES['product_photo'] : null;
    $photo_resources = [];

    if (empty($photo_files) || empty($photo_files['name'][0])) {
        $_err['product_photo'] = 'At least one extra photo is required. ';
    } else if (is_array($photo_files['name'])) {
        foreach ($photo_files['name'] as $index => $name) {
            $tmp_name = $photo_files['tmp_name'][$index];
            $size = $photo_files['size'][$index];
            $error = $photo_files['error'][$index];

            if ($error === UPLOAD_ERR_NO_FILE || empty($name)) {
                $_err['product_photo'] = 'At least one extra photo is required. ';
                break;
            }

            $extension = pathinfo($name, PATHINFO_EXTENSION);
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $_err['product_photo'] = 'All files must be images. Please upload again.';
            } else if ($size > 1 * 1024 * 1024) {
                $_err['product_photo'] = 'Each image must be under 1MB. Please upload again.';
            } else {
                $unique_name = uniqid() . '.' . $extension;
                if (move_uploaded_file($tmp_name, "../../images/uploads/products/$unique_name")) {
                    $photo_resources[] = $unique_name;
                } else {
                    $_err['product_photo'] = 'Failed to upload file. Please upload again.';
                }
            }
        }
    }


    // Validate youtube url
    if (!empty($product_youtube_url) && strpos($product_youtube_url, 'https://www.youtube.com/watch?v=') === 0) {
        $prefix = 'https://www.youtube.com/watch?v=';
        $product_youtube_url = str_replace($prefix, '', $product_youtube_url);
    } else if(!empty($product_youtube_url) && (strpos($product_youtube_url, 'https://www.youtube.com/watch?v=') != 0)){
        $_err['product_youtube_url'] = 'Invalid YouTube URL format. It must start with "https://www.youtube.com/watch?v="';
    }

    // Output
    if (!$_err) {      
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
                              (product_id, product_name, product_cover, product_resources, product_youtube_url, product_desc, product_price, product_stock, product_last_update, admin_id, category_id)
                              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stm->execute([$product_id, $product_name, $product_cover, json_encode($photo_resources), $product_youtube_url, $product_desc, $product_price, $product_stock, date("Y-m-d H:i:s"), $admin_id, $category_id]);

        temp('info', 'Product added.');
        redirect('product.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Add Product';
include '../../_admin_head.php';
?>
<button data-get="/page/yongqiaorou/product.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

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

    <label for="product_cover" style="height:180px; width:150px; padding:10px; margin-top:auto">Cover Picture
    <div class="tooltip">
        <i class="fa fa-question-circle" aria-hidden="true"></i>
        <span class="tooltiptext">Tick ✔️ at Extra Resources to Have Cover Picture</span>
    </div>
    </label>
    <div>
        <?= html_file('product_cover', 'image/*', 'hidden id="product_cover"') ?>
        <img id="preview" src="../../images/photo.jpg" style="width: 200px; height: 200px;">
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
    const existingResources = <?php echo json_encode($product_resources); ?>;

    existingResources.forEach(resource => {
        const ext = resource.split('.').pop().toLowerCase();
        const previewElement = document.createElement(ext === 'mp4' || ext === 'avi' ? 'video' : 'img');

        previewElement.src = `../../images/uploads/products/${resource}`;
        previewElement.style.maxWidth = '200px'; 
        previewElement.style.margin = '5px'; 
        
        // if (ext === 'mp4' || ext === 'avi') {
        //     previewElement.controls = true; 
        // }
        
        $('#product_photo_previews').append(previewElement);
    });

</script>
<?php
include '../../_foot.php';
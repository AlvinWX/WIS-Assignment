<?php
require '_base.php';
//-----------------------------------------------------------------------------

// // TODO
$arr = $_db->query('SELECT * FROM product WHERE product_status=1')->fetchAll();
$photos = glob('images/*.jpg');
$photos = array_map('basename',$photos);

// ----------------------------------------------------------------------------
$_title = 'Product List';
include '_admin_head.php';
?>
<form method="GET" action="" class="search-form"  style="text-align: right; margin-bottom: 20px;">
    <select name="search_field">
        <option value="product_name" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_name' ? 'selected' : '' ?>>Product Name</option>
        <option value="product_desc" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_desc' ? 'selected' : '' ?>>Description</option>
        <option value="product_price" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_price' ? 'selected' : '' ?>>Price</option>
        <option value="product_category" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_category' ? 'selected' : '' ?>>Category</option>
    </select>

    <input type="text" name="search" placeholder="Search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    <button type="submit">Search</button>
</form>

<?php
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : 'product_name';
$search_value = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT p.* FROM product p WHERE p.product_status = 1";

if ($search_field == 'product_category' && $search_value) {
    // Fetch all category IDs matching the search value
    $category_query = "SELECT category_id FROM category WHERE category_name LIKE :category_name";
    $category_stmt = $_db->prepare($category_query);
    $category_stmt->bindValue(':category_name', '%' . $search_value . '%');
    $category_stmt->execute();
    
    // Fetch all category IDs
    $categories = $category_stmt->fetchAll();
    
    // Check if any categories were found
    if ($categories) {
        // Extract category IDs into an array
        $category_ids = array_column($categories, 'category_id');
        
        // Build the query to check for products that belong to any of the matching categories
        $query .= " AND p.category_id IN (" . implode(',', array_fill(0, count($category_ids), '?')) . ")";
    } else {
        // No matching categories, so set the result array to empty
        $arr = [];
    }
} else {
    if ($search_value) {
        $query .= " AND p.$search_field LIKE :search_value";
    }
}

$stmt = $_db->prepare($query);

// Bind category IDs for the IN clause if applicable
if ($search_field == 'product_category' && $search_value && !empty($category_ids)) {
    foreach ($category_ids as $index => $category_id) {
        $stmt->bindValue($index + 1, $category_id);  // Bind each category ID to the placeholders
    }
} elseif ($search_value) {
    $stmt->bindValue(':search_value', '%' . $search_value . '%');
}

$stmt->execute();
$arr = $stmt->fetchAll();

if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>

<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Product Cover</th>
        <th>Product Resources</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock Left</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($arr as $s): ?>
    <tr>
        <td><?= $s->product_id ?></td>
        <td><?= $s->product_name ?></td>
        <td><img src="/images/<?= $s->product_cover ?>"></td>
        <td>
        <?php if (!empty($s->product_resources)): ?>
            <?php
                $resources = json_decode($s->product_resources, true); // Decode the JSON array
                if ($resources): ?>
                    <div id="carousel-<?= $s->product_id ?>" class="carousel slide custom-carousel" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($resources as $index => $resource): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <?php if (strpos(mime_content_type("uploads/$resource"), 'image/') !== false): ?>
                                        <img class="d-block w-100 custom-carousel-item" src="/uploads/<?= $resource ?>" alt="Resource <?= $index + 1 ?>">
                                    <?php elseif (strpos(mime_content_type("uploads/$resource"), 'video/') !== false): ?>
                                        <video class="d-block w-100 custom-carousel-item" controls>
                                            <source src="/uploads/<?= $resource ?>" type="video/<?= pathinfo($resource, PATHINFO_EXTENSION) ?>">
                                        </video>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Controls -->
                        <a class="carousel-control-prev" href="#carousel-<?= $s->product_id ?>" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel-<?= $s->product_id ?>" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </td>
        <td><?= $s->product_desc ?></td>
        <td><?= $s->product_price ?></td>
        <td>
            <?= $s->product_stock ?>
            <br>
            <?php 
            if($s->product_stock <= 2){ 
                echo "<span style='color:red;'> Low Stock!!</span>";
                temp('info', $s->product_name." is low stock now!!");
            }else if($s->product_stock > 2){
                echo "<span style='color:green;'> Good </span>";
            }
            ?>
        </td>
        <td>
            <!-- TODO -->
            <button data-get="product_detail.php?id=<?= $s->product_id ?>">Detail</button>
            <button data-get="product_update.php?id=<?= $s->product_id ?>">Update</button>
            <button data-post="product_delete.php?id=<?= $s->product_id ?> "data-confirm>Delete</button>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php }else{?>
    <p style="color:red;">No record found.</p>
<?php }?>
<a href="product_recover.php"><span id="dot" class="dot_left" style="color: #FB0606;"><i class="fa fa-trash" aria-hidden="true"></i></span></a>
<a href="product_add.php"><span id="dot" class="dot_right"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

<?php
include '_admin_foot.php';
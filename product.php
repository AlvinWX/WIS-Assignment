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
<form method="GET" action="" style="text-align: right; margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Search by Product Name" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 5px;">
    <button type="submit" style="padding: 5px;">Search</button>
</form>

<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = 'SELECT * FROM product WHERE product_status = 1';
if ($search) {
    $query .= ' AND product_name LIKE :search';
}

$stmt = $_db->prepare($query);
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
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
    <p style="color:red;">You have not add any product yet</p>
<?php }?>
<a href="product_recover.php"><span id="dot" class="dot_left" style="color: #FB0606;"><i class="fa fa-trash" aria-hidden="true"></i></span></a>
<a href="product_add.php"><span id="dot" class="dot_right"><i class="fa fa-plus" aria-hidden="true"></i></span></a>

<?php
include '_admin_foot.php';
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

<?php if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>
<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Product Image</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock Left</th>
    </tr>

    <?php foreach ($arr as $s): ?>
    <tr>
        <td><?= $s->product_id ?></td>
        <td><?= $s->product_name ?></td>
        <td><img src="/images/<?= $s->product_img ?>"></td>
        <td><?= $s->product_desc ?></td>
        <td><?= $s->product_price ?></td>
        <td><?= $s->product_stock ?></td>
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
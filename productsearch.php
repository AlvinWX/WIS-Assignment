<?php
require '_base.php';

$productName = req('product_name');
$productCategory = req('category_id');

// $stm = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_status=1 ORDER BY product_sold DESC LIMIT 5');
$stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_name LIKE ? AND (p.category_id = ? or ?)');
$stm->execute(["%$productName%", $productCategory, $productCategory == null]);
$arr= $stm->fetchAll();
include '_head.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/productbox.css">
    <link rel="stylesheet" href="css/productsearch.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Search Product: <?=$productName ?></title>
</head>
<body>
    <form class="searchmenu">
        <div class="searchbar"><?= html_search('product_name', 'placeholder=\'Search your product here...\'') ?></div>
        <div class="categoryselect"><?= html_select('category_id', $_categories, 'All') ?></div>
        <div class="searchbutton"><button>Search Products</button></div>
    </form>

    <section class="products" id="products">
        <div class="heading">
            <h1>Search Results: <?=$productName ?> </h1>
            <p><?= count($arr) ?> product(s) found</p>
        </div>

        <div class="products-container">
        <?php foreach ($arr as $s): ?>
            <div class="box" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <img src="images/<?= $s->product_img ?>" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->category_name?></span>
                <h2 data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_name?></h2>
                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>">RM <?= $s->product_price?></h3>
                <i class='bx bx-cart-alt' onclick="" ></i>
                <i class='bx bx-heart' ></i>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_sold?> sold</span>
            </div>
            <?php endforeach ?>
            </div>
            

    </section>

</body>
</html>

<?php
include '_foot.php';
?>
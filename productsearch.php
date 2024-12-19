<?php
require '_base.php';
include '_head.php';

$productName = req('product_name');
$productCategory = req('category_id');
$minPrice = req('minprice');
$maxPrice = req('maxprice');

$fields = [
    'product_name' => 'Product Name',
    'product_price' => 'Product Price',
    'product_sold' => 'Product Sold',
    'product_stock' => 'Product Stock'
];

$sort = req('sort');
key_exists($sort, $fields) || 
$sort = 'product_name';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) ||
$dir = 'asc';

$searchString = "";

$nameQuery = "";
if($productName != null){
    $nameQuery = "'%$productName%'";
    $searchString = $searchString . $productName;
} else{
    $nameQuery = "'%'";
}

$categoryQuery = "";
if($productCategory != null){
    $categoryQuery = " AND p.category_id = '$productCategory'";
}

$priceQuery = "";
if($minPrice != null && $maxPrice != null){
    $priceQuery = " AND product_price BETWEEN " . $minPrice . " AND " . $maxPrice;
    $searchString = $searchString . " from RM " . sprintf('%.2f', $minPrice) . " to RM " . sprintf('%.2f', $maxPrice);
}
    
$query = 'SELECT * FROM product p 
JOIN category c ON p.category_id = c.category_id 
WHERE product_status=1 AND product_stock > 0
AND product_name LIKE ' . $nameQuery
. $categoryQuery
. $priceQuery
. ' ORDER BY ' . $sort . ' ' . $dir;

$page = req('page', 1);
require_once 'lib/SimplePager.php';
$p = new SimplePager($query, [], 10, $page);
$arr = $p->result;


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
    <title>Search Product <?=$searchString ?></title>
</head>
<body>
    <form class="searchmenu">
        <div class="searchbar"><?= html_search('product_name', 'placeholder=\'Search your product here...\'') ?></div>
        <div class="categoryselect"><?= html_select('category_id', $_categories, 'All') ?></div>
        <div class="text"> RM </div>
        <div class="minpricerange"><?= html_number('minprice', 0, 100, 1) ?></div>
        <div class="text"> - RM </div>
        <div class="maxpricerange"><?= html_number('maxprice', 0, 100, 1) ?></div>
        <div class="searchbutton"><button>Search Products</button></div>

    </form>

    <section class="products" id="products">
        <div class="heading">
            <h1>Search Results: <?=$searchString ?> </h1>
            <?= $p->count ?> of <?= $p->item_count ?> product(s) |
            Page <?= $p->page ?> of <?= $p->page_count ?>
        </div>

        <section class="sort">
            <p>Sort by:</p><?= sort_buttons($productName, $productCategory, $minPrice, $maxPrice, $fields, $sort, $dir , "page=$page")?>
        </section>

        <div class="paging">
            <p>Select page:</p><?= $p->html("product_name=$productName&category_id=$productCategory&minprice=$minPrice&maxprice=$maxPrice&sort=$sort&dir=$dir") ?>
        </div>
    
        <div class="products-container">
        <?php foreach ($arr as $s): ?>
            <div class="box" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <img src="images/<?= $s->product_img ?>" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->category_name?></span>
                <h2 data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_name?></h2>
                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>">RM <?= sprintf('%.2f', $s->product_price) ?></h3>
                <i class='bx bx-cart-alt'></i>
                <i class='bx bx-heart' ></i>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>
            </div>
            
    </section>

</body>
</html>

<?php
include '_foot.php';
?>
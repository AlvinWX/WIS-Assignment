<?php
require '../../_base.php';
include '../../_head.php';

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

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

$fullPath = $_SERVER['REQUEST_URI'];
$_SESSION['path_details'] = $fullPath;

$_SESSION['add_quantity_details'] = [
    'product_name' => $productName,
    'product_category' => $productCategory,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'sort' => $sort,
    'dir' => $dir
];

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
require_once '../../lib/SimplePager.php';
$p = new SimplePager($query, [], 10, $page);
$arr = $p->result;

//Retrieve member cart
$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]);
$shoppingCart = $get_cart_stm -> fetch();

//Retrieve member wishlist
$get_wishlist_stm = $_db -> prepare('SELECT * FROM wishlist w JOIN member m ON m.member_id = w.member_id WHERE w.member_id = ?');
$get_wishlist_stm -> execute([$member_id]);
$wishlist = $get_wishlist_stm -> fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/productbox.css">
    <link rel="stylesheet" href="../../css/productsearch.css">
    <link rel="stylesheet" href="../../css/flash.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="../../js/wishlist.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <title>Search Product <?=$searchString ?></title>
</head>
<body>
    <div id="info"><?= temp('info')?></div>
    <form class="searchmenu">
        <div class="searchbar"><?= html_search('product_name', 'placeholder=\'Search your product here...\'') ?></div>
        <div class="categoryselect"><?= html_select('category_id', $_categories, 'All') ?></div>
        <div class="text"> RM </div>
        <div class="minpricerange"><?= html_number('minprice', 0, 1000, 1) ?></div>
        <div class="text"> - RM </div>
        <div class="maxpricerange"><?= html_number('maxprice', 0, 1000, 1) ?></div>
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
            <div class="box">
                <img src="../../images/product_pic/<?= $s->product_cover ?>" data-get="productinfo.php?id=<?= $s->product_id ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->category_name?></span>
                <h2 class="product-name" data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_name?></h2>
                <?php 
                    $get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
                    $get_cart_product -> execute([$s->product_id, $shoppingCart->cart_id]);
                    $cartProductFound = $get_cart_product -> fetch();
                    if($cartProductFound != null){ ?>
                        <h2 class="selected" data-get="productinfo.php?id=<?= $s->product_id ?>">Selected: <?= $cartProductFound->quantity?></h2>
                <?php  } else { ?>
                        <h2 class="selected"></h2>
                    <?php  } ?>
                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>">RM <?= sprintf('%.2f', $s->product_price) ?></h3>
                    <a class= "add-to-cart" href="addquantity.php?id=<?= $s->product_id ?>"><i class="bx bx-cart-alt"></i></a>
                    <?php
                        $check_wishlist_stm = $_db->prepare('SELECT COUNT(*) FROM wishlist_product WHERE wishlist_id = ? AND product_id = ?');
                        $check_wishlist_stm->execute([$wishlist->wishlist_id, $s->product_id]);
                        $isWished = $check_wishlist_stm->fetchColumn() == 0 ? false : true;
                    ?>
                    <svg class='bx bx-heart' viewBox='0 0 24 24' width='24' height='24' onclick="updateWishlist('<?= $s->product_id ?>', '<?= $isWished ? 'remove' : 'add' ?>', '<?= $wishlist->wishlist_id ?>' , this)">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="<?= $isWished ? '#ff007f' : 'none' ?>" stroke="#ff007f" stroke-width="2"/>
                    </svg>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>
            </div>
            
    </section>

</body>
</html>

<?php
include '../../_foot.php';
?>
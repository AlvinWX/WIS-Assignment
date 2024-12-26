<?php
require '../../_base.php';
include '../../_head.php';


$fields = [
    'product_name' => 'Product Name',
    'product_price' => 'Product Price',
    'product_sold' => 'Product Sold',
    'product_stock' => 'Product Stock'
];

//Retrieve member cart
$member_id = $user->member_id; 

$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]);
$shoppingCart = $get_cart_stm -> fetch();

//Retrieve member wishlist
$get_wishlist_stm = $_db -> prepare('SELECT * FROM wishlist w JOIN member m ON m.member_id = w.member_id WHERE w.member_id = ?');
$get_wishlist_stm -> execute([$member_id]);
$wishlist = $get_wishlist_stm -> fetch();

$sort = req('sort');
key_exists($sort, $fields) || 
$sort = 'product_name';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) ||
$dir = 'asc';
    
$wishlistQuery = " wishlist_id = " . $wishlist->wishlist_id;
$query = 'SELECT * FROM wishlist_product w
JOIN product p ON w.product_id = p.product_id
JOIN category c ON p.category_id = c.category_id 
WHERE product_status=1 AND product_stock > 0
AND ' . $wishlistQuery
. ' ORDER BY ' . $sort . ' ' . $dir;

// $query = 'SELECT * FROM product p 
// JOIN category c ON p.category_id = c.category_id 
// WHERE product_status=1 AND product_stock > 0
// AND product_name LIKE ' . $nameQuery
// . $categoryQuery
// . $priceQuery
// . ' ORDER BY ' . $sort . ' ' . $dir;

print_r($query);

$page = req('page', 1);
require_once '../../lib/SimplePager.php';
$p = new SimplePager($query, [], 10, $page);
$arr = $p->result;

//If add to cart button is pressed
if(isset($_POST['add-to-cart'], $_POST['product_id'])){
    
    $productID = $_POST['product_id'];

    //Retrieve product details
    $find_product_stm = $_db -> prepare('SELECT * FROM product WHERE product_id = ?');
    $find_product_stm -> execute([$productID]);
    $productFound = $find_product_stm -> fetch();

    //Check the product is add to cart before
    $check_record_stm = $_db -> prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
    $check_record_stm -> execute([$productID, $shoppingCart->cart_id]);
    $check_result = $check_record_stm -> fetch();

    if($check_result == null){
        //If no entry before
        $add_product_stm = $_db -> prepare('INSERT INTO cart_product (cart_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
        $add_product_stm -> execute([$shoppingCart->cart_id, $productID, $productFound-> product_price, 1]); //HERE NEED TO CHANGE AFTERWARDS
        temp('info', 'Item added to cart.');
        redirect("wishlist.php?sort={$sort}&dir={$dir}&page=$page");
    } else if($check_result->quantity >= $productFound->product_stock) {
        //If the product is add to cart before (but the selected quantity >= stock)
        temp('info', 'The product quantity cannot greater than the current product stock.');
        redirect("wishlist.php?sort={$sort}&dir={$dir}&page=$page");
    } else{
        //If the product is add to cart before
        $update_quantity_stm = $_db -> prepare('UPDATE cart_product SET quantity = ? WHERE cart_id = ? AND product_id = ?');
        $update_quantity_stm -> execute([$check_result->quantity + 1, $shoppingCart->cart_id, $productID]);
        temp('info', 'Item added to cart.');
        redirect("wishlist.php?sort={$sort}&dir={$dir}&page=$page");
    }
   
}

$fullPath = $_SERVER['REQUEST_URI'];

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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <title>My Wishlist</title>
</head>
<style>
    body{
        background:#f9e6e4;
    }
    .empty-box{
            margin: 35px;
            padding: 35px;
        }
    .heading{
        width: 100%;
    }
    .heading h1{
        text-align: center;
        font-weight: 900;
        font-size: 64px;
        color: #fc6c85;
        letter-spacing: 5px;
        padding: 0;
        margin:0;
    }
</style>
<body>
    <div id="info"><?= temp('info')?></div>
    <div class="empty-box"></div>

    <section class="products" id="products">
        <div class="heading">
            <h1>My Wishlist</h1>
            <?= $p->count ?> of <?= $p->item_count ?> product(s) |
            Page <?= $p->page ?> of <?= $p->page_count ?>
        </div>

        <section class="sort">
            <p>Sort by:</p><?= sort_buttons2($fields, $sort, $dir , "page=$page")?>
        </section>

        <div class="paging">
            <p>Select page:</p><?= $p->html("&sort=$sort&dir=$dir") ?>
        </div>
    
        <div class="products-container">
        <?php foreach ($arr as $s): ?>
            <div class="box">
                <img src="../yongqiaorou/images/<?= $s->product_cover ?>" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">
                <span data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->category_name?></span>
                <h2 class="product-name" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->product_name?></h2>

                <?php 
                    $get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
                    $get_cart_product -> execute([$s->product_id, $shoppingCart->cart_id]);
                    $cartProductFound = $get_cart_product -> fetch();
                    if($cartProductFound != null){ ?>
                        <h2 class="selected" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">Selected: <?= $cartProductFound->quantity?></h2>
                <?php  } else { ?>
                        <h2 class="selected"></h2>
                    <?php  }  ?>

                <h3 class="price" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>">RM <?= sprintf('%.2f', $s->product_price) ?></h3>
                <form method="post">
                    <input hidden type="text" name="product_id" value="<?= $s->product_id ?>">
                    <input type="submit" name="add-to-cart" class= "add-to-cart" value="+">
                <i class='bx bx-heart' ></i></form>
                <span class="sold" data-get="productinfo.php?id=<?= $s->product_id ?>&path=<?= $fullPath ?>"><?= $s->product_sold?> sold || <?= $s->product_stock?> left</span>
            </div>
            <?php endforeach ?>
            </div>
            
    </section>

</body>
</html>

<?php
include '../../_foot.php';
?>
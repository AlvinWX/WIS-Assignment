<?php
require '../../_base.php';

include '../../_head.php';

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

$id = req('id');

$fullPath = $_SESSION['path_details'];

$stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE product_id = ?'); 
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) { 
    redirect($fullPath);
}

//Retrieve member cart
$get_cart_stm = $_db -> prepare('SELECT * FROM cart c JOIN member m ON m.member_id = c.member_id WHERE c.member_id = ?');
$get_cart_stm -> execute([$member_id]);
$shoppingCart = $get_cart_stm -> fetch();

//Retrieve member wishlist
$get_wishlist_stm = $_db -> prepare('SELECT * FROM wishlist w JOIN member m ON m.member_id = w.member_id WHERE w.member_id = ?');
$get_wishlist_stm -> execute([$member_id]);
$wishlist = $get_wishlist_stm -> fetch();

//Used to display quantity
$currentQuantity = 1; //By default to 1
$get_cart_product = $_db->prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
$get_cart_product -> execute([$id, $shoppingCart->cart_id]);
$cartProductFound = $get_cart_product -> fetch();
if($cartProductFound != null){
    $currentQuantity = $cartProductFound-> quantity;
}

//Retrieve product details
$find_product_stm = $_db -> prepare('SELECT * FROM product WHERE product_id = ?');
$find_product_stm -> execute([$id]);
$productFound = $find_product_stm -> fetch();

//Check the product is add to cart before
$check_record_stm = $_db -> prepare('SELECT * FROM cart_product WHERE product_id = ? AND cart_id = ?');
$check_record_stm -> execute([$id, $shoppingCart->cart_id]);
$check_result = $check_record_stm -> fetch();

//If add to cart button is pressed
if(isset($_POST['add-to-cart'], $_POST['product_id'], $_POST['quantity'])){
    
    $productID = $_POST['product_id'];
    $productQuantity = (int)$_POST['quantity'];


    if($check_result == null){
        //If no entry before
        $add_product_stm = $_db -> prepare('INSERT INTO cart_product (cart_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
        $add_product_stm -> execute([$shoppingCart->cart_id, $productID, $productFound-> product_price, $productQuantity]); //HERE NEED TO CHANGE AFTERWARDS
    } else {
        //If the product is add to cart before
        $update_quantity_stm = $_db -> prepare('UPDATE cart_product SET quantity = ? WHERE cart_id = ? AND product_id = ?');
        $update_quantity_stm -> execute([$productQuantity, $shoppingCart->cart_id, $productID]);
    }
    
    temp('info', 'Item added to cart.');
    redirect($fullPath);
}

$resources = json_decode($s -> product_resources, true);

?>

<title><?= $s -> product_name ?> @ TAR GROCER</title>
<link rel="stylesheet" href="../../css/productinfo.css">
<link rel="stylesheet" href="../../css/flash.css">
<link rel="stylesheet" href="../../css/imageslider.css">
<script src="../../js/productinfo.js" defer></script>
<script src="../../js/wishlist.js" defer></script>
<script src="../../js/imageslider2.js" defer></script>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<script>
window.onload = function() {
    document.getElementById('spinnerValue').blur();
};
</script>

<body>
<div id="info"><?= temp('info')?></div>
    <div class="card-wrapper">
        <div class="card">
            <!--card left-->
            <div class="product-images">
            <div class = "slide-container" style="width: 560px; height: 560px;">
        
        <div class="slides">
        <img src="../../images/product_pic/<?= $s->product_cover ?>" alt="Resource <?= $index + 1 ?>" class="image active">
        <?php foreach ($resources as $index => $resource):?>
                            <?php if (strpos(mime_content_type("../../images/uploads/products/$resource"), 'image/') !== false): ?>
                                <img src="../../images/uploads/products/<?= $resource ?>" alt="Resource <?= $index + 1 ?>" class="image">
                            <?php elseif (strpos(mime_content_type("../../images/uploads/$resource"), 'video/') !== false): ?>
                                <video controls>
                                    <source src="../../images/uploads/<?= $resource ?>" type="video/<?= pathinfo($resource, PATHINFO_EXTENSION) ?>" class="image">
                                </video>
                            <?php endif; ?>
                        <?php endforeach; ?>
        </div>

        <div class="buttons">
            <span class="next">&#10095;</span>
            <span class="prev">&#10094;</span>
        </div>

        <div class="image-thumbnail">
            <img class="img active" src="../../images/product_pic/<?= $s->product_cover ?>" alt="Resource <?= $index + 1 ?>" attr='0' onclick="switchImage(this)">
            <?php for($i = 1; $i <= count($resources); $i++): $resource = $resources[$i-1] ?>
			    
                <?php if (strpos(mime_content_type("../../images/uploads/products/$resource"), 'image/') !== false): ?>
                                <img class="img" attr='<?= $i ?>' onclick="switchImage(this)" src="../../images/uploads/products/<?= $resource ?>" alt="Resource <?= $index + 1 ?>">
                            <?php elseif (strpos(mime_content_type("../../images/uploads/products/$resource"), 'video/') !== false): ?>
                                <video controls>
                                    <source class="img" attr='<?= $i ?>' onclick="switchImage(this)" src="../../images/uploads/products/<?= $resource ?>" type="video/<?= pathinfo($resource, PATHINFO_EXTENSION) ?>">
                                </video>
                            <?php endif; ?>
            <?php endfor; ?>
        </div>

            </div>

            <?php
                if($s->product_youtube_url != null):
            ?>
                <div class = "media">
                    <span>Relevant media:</span>
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $s -> product_youtube_url ?>" title="YouTube video player" frameborder="5" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            <?php endif ?>

            </div>
            <!--card right-->
            <div class = "product-content">
                <span class = "product-title"><?= $s -> product_name ?></span>

                <span class = "product-category" data-get="productsearch.php?product_name=&category_id=<?= $s->category_id ?>">Category: <?= $s -> category_name ?></span>

                <div class= "product-price">
                    RM <?= sprintf('%.2f', $s->product_price) ?>
                </div>

                <div class= "product-quantity">
                    <?= $s -> product_sold ?> sold || <?= $s -> product_stock ?> left
                </div>

                <div class="purchase-info">
                    <?php if($check_result != null && $check_result -> quantity > 0) { ?>
                    <button class="remove" onclick="confirmDelete('<?= $s->product_id ?>', '<?= $check_result->cart_id ?>', '<?= $fullPath ?>')">Remove</button>
                    <?php } ?>
                    <button class="decrease" onclick="decreaseValue()">-</button>
                    <form method="post" id="quantitySelect"><input type="number" blur name="quantity" id="spinnerValue" value="<?= $currentQuantity ?>" min="1" max="<?= $s -> product_stock ?>" step="1"></form>
                    <button class="increase" onclick="increaseValue()">+</button>
                    <input form="quantitySelect" hidden type="text" name="product_id" value="<?= $s->product_id ?>">
                    <input form="quantitySelect" type = "submit" name="add-to-cart" class="add-to-cart" value="Add to Cart">  
                    <?php
                        $check_wishlist_stm = $_db->prepare('SELECT COUNT(*) FROM wishlist_product WHERE wishlist_id = ? AND product_id = ?');
                        $check_wishlist_stm->execute([$wishlist->wishlist_id, $s->product_id]);
                        $isWished = $check_wishlist_stm->fetchColumn() == 0 ? false : true;
                    ?>
                    <svg class='bx bx-heart' viewBox='0 0 24 24' width='32' height='32' onclick="updateWishlist('<?= $s->product_id ?>', '<?= $isWished ? 'remove' : 'add' ?>', '<?= $wishlist->wishlist_id ?>' , this)">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="<?= $isWished ? '#ff007f' : 'none' ?>" stroke="#ff007f" stroke-width="2"/>
                    </svg>               
                </div>
                <div class = "product-desc">
                    <span>Product information:</span>
                    <p><?= $s -> product_desc ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="empty-box"></div>
</body>

<?php
include '../../_foot.php';
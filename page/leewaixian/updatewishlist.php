<?php
require '../../_base.php'; 

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (isset($_POST['product_id'], $_POST['action'])) {
    $product_id = $_POST['product_id'];
    $wishlist_id = $_POST['wishlist_id'];

    if ($_POST['action'] == 'add') {
        $stmt = $_db->prepare('INSERT INTO wishlist_product (wishlist_id, product_id) VALUES (?, ?)');
        $stmt->execute([$wishlist_id, $product_id]);
        echo "added";
    } elseif ($_POST['action'] == 'remove') {
        $stmt = $_db->prepare('DELETE FROM wishlist_product WHERE wishlist_id = ? AND product_id = ?');
        $stmt->execute([$wishlist_id, $product_id]);
        echo "removed";
    }
}
?>

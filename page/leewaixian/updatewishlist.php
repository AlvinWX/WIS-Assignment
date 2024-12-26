<?php
require '../../_base.php';  // Ensure this points to your database connection settings

if (isset($_POST['product_id'], $_POST['action'])) {
    $product_id = $_POST['product_id'];
    $wishlist_id = $_POST['wishlist_id'];  // Assume session contains wishlist_id

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

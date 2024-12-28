function updateWishlist(productId, action,wishlistId, element) {
    $.ajax({
        url: 'http://localhost:8000/page/leewaixian/updatewishlist.php',
        type: 'POST',
        data: { product_id: productId, action: action, wishlist_id: wishlistId },
        success: function(response) {
            //console.log(response);
            alert(response + " to " + wishlistId);
            if (response.trim() == 'added') {
                element.querySelector('path').setAttribute('fill', '#ff007f');  // Fill heart
                element.setAttribute('onclick', `updateWishlist('${productId}', 'remove', '${wishlistId}', this)`);  // Change action to remove
            } else if (response.trim() == 'removed') {
                element.querySelector('path').setAttribute('fill', 'none');  // Empty heart
                element.setAttribute('onclick', `updateWishlist('${productId}', 'add', '${wishlistId}', this)`);  // Change action to add
            }
        }
        
    });
}

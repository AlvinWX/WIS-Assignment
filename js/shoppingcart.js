function decreaseValue(i) {
    const input = document.getElementById('spinnerValue' + i);
    const currentValue = parseInt(input.value, 10);
    if (!isNaN(currentValue) && currentValue > parseInt(input.min, 10)) {
        input.value = currentValue - parseInt(input.step, 10);
        updatePrice(parseFloat(input.dataset.price), i);
        updateQuantityInDatabase(input.value, input.dataset.productId, input.dataset.cartId);
    }
}

function increaseValue(i) {
    const input = document.getElementById('spinnerValue' + i);
    const currentValue = parseInt(input.value, 10);
    if (!isNaN(currentValue) && currentValue < parseInt(input.max, 10)) {
        input.value = currentValue + parseInt(input.step, 10);
        updatePrice(parseFloat(input.dataset.price), i);
        updateQuantityInDatabase(input.value, input.dataset.productId, input.dataset.cartId);
    }
}


function confirmDelete(id, cart, page) {
    if (confirm("Are you sure you want to remove this item?")) {
        window.location.href = 'removeproduct.php?id=' + id + '&cart_id=' + cart + '&page=' + page;
    }
}

function updatePrice(oriPrice, i) {
    var quantity = document.getElementById('spinnerValue' + i).value;
    var total = oriPrice * quantity;
    document.getElementById('multipliedPrice' + i).innerHTML = '<h3 class="price">RM ' + total.toFixed(2) + '</h3>';
    updateSubtotal();
}

function updateSubtotal() {
    const prices = document.querySelectorAll('.multipliedPrice .price');
    let subtotal = 0;
    prices.forEach(price => {
        const value = parseFloat(price.textContent.replace('RM ', ''));
        if (!isNaN(value)) subtotal += value;
    });

    document.querySelector('.cart-subtotal .price').textContent = `RM ${subtotal.toFixed(2)}`;
}


function updateQuantityInDatabase(quantity, productId, cartId) {
    fetch('updatequantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `quantity=${quantity}&productId=${productId}&cartId=${cartId}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Update response:', data);
    })
    .catch(error => console.error('Error:', error));
}

function confirmClearCart(cart) {
    if (confirm("Are you sure you want to clear your cart?")) {
        window.location.href = 'clearcart.php?cart_id=' + cart;
    }
}
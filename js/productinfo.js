function decreaseValue() {
    const input = document.getElementById('spinnerValue');
    const currentValue = parseInt(input.value, 10);
    if (!isNaN(currentValue) && currentValue > parseInt(input.min, 10)) {
      input.value = currentValue - parseInt(input.step, 10);
    }
  }

function increaseValue() {
    const input = document.getElementById('spinnerValue');
    const currentValue = parseInt(input.value, 10);
    if (!isNaN(currentValue) && currentValue < parseInt(input.max, 10)) {
      input.value = currentValue + parseInt(input.step, 10);
    }
  }

function confirmDelete(id, cart, page) {
    if (confirm("Are you sure you want to remove this item?")) {
        window.location.href = 'removeproduct.php?id=' + id + '&cart_id=' + cart + '&page=' + page; // Redirect to PHP script
    }
}

document.getElementById('quantitySelect').addEventListener('submit', function(event) {
  var quantityInput = document.getElementById('spinnerValue');
  var quantity = parseInt(quantityInput.value, 10);
  var maxQuantity = parseInt(quantityInput.max, 10);
  var minQuantity = parseInt(quantityInput.min, 10);

  if (isNaN(quantity) || quantity < minQuantity || quantity > maxQuantity) {
      alert('Please enter a valid quantity between ' + minQuantity + ' and ' + maxQuantity + '.');
      event.preventDefault(); // Prevent form submission
  }
});



document.getElementById('payment').addEventListener('submit', function(event) {
    var addressSelected = document.querySelector('input[name="address"]:checked');
    if (!addressSelected) {
        alert('Please select a shipping address.');
        event.preventDefault(); 
    }
});

var selectfield = document.getElementById("selectfield");
var selecttext = document.getElementById("selecttext");
var options = document.getElementsByClassName("options");
var list = document.getElementById("list");
var arrowIcon = document.getElementById("arrowicon");

selectfield.onclick = function(){
    list.classList.toggle("hide");
    arrowIcon.classList.toggle("rotate");
}

for(option of options){
    option.onclick = function(){
        selecttext.innerHTML = this.textContent;
        list.classList.toggle("hide");
        arrowIcon.classList.toggle("rotate");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    var options = document.querySelectorAll('.options');

    options.forEach(function(option) {
        option.addEventListener('click', function() {
            var voucherName = this.querySelector('p').textContent;
            var voucherDiscount = parseFloat(this.querySelector('p').getAttribute('data-discount')) || 0;
            var voucherOwnedID = this.querySelector('p').getAttribute('data-id');

            document.getElementById('voucherinput').value = voucherName;
            document.getElementById('voucherdiscount').value = voucherDiscount;
            document.getElementById('voucherownedid').value = voucherOwnedID;
            document.getElementById('selecttext').textContent = voucherName;

            updateDisplay(voucherDiscount); 
        });
    });

    function updateDisplay(discount) {
        var subtotalText = document.getElementById('subtotal').textContent;
        var cleanedSubtotal = subtotalText.replace(/[^0-9.]/g, ''); 
        var subtotal = parseFloat(cleanedSubtotal);
        var newTotal = subtotal - discount;

        // Update display elements
        document.getElementById('discount').textContent = '- RM ' + discount.toFixed(2);
        document.getElementById('finaltotal').textContent = 'RM ' + newTotal.toFixed(2);
        document.getElementById('points').textContent = Math.floor(newTotal) + ' points';

        document.getElementById('formdiscount').value = discount.toFixed(2);
        document.getElementById('formtotal').value = newTotal.toFixed(2);
        document.getElementById('formpoints').value = Math.floor(newTotal);
    }
});






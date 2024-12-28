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
const images = document.querySelectorAll('.image-select a');
const imageButtons = [ ... images];
let = imageID = 1;

imageButtons.forEach((imageItem) => {
    imageItem.addEventListener('click', (event)=>{
        event.preventDefault();
        imageID = imageItem.dataset.id;
        slideImage();
    });
});

function slideImage(){
    const displayWidth = document.querySelector('.image-showcase img:first-child').clientWidth;

    document.querySelector('.image-showcase').style.transform = 
    `translateX(${- (imageID - 1) * displayWidth}px)`;
}


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
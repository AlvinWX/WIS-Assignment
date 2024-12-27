
function validatePhone() {
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const phonePattern = /^01[0-9]{8,9}$/;

    if (!phonePattern.test(phoneInput.value)) {
        phoneError.style.display = 'block';
    } else {
        phoneError.style.display = 'none';
    }
}



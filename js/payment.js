document.addEventListener('DOMContentLoaded', function () {
    const cardNumberInput = document.getElementById('card_number');
    const cardHolderNameInput = document.getElementById('card_holder_name');
    const expiryMonthInput = document.getElementById('expiry_month');
    const expiryYearInput = document.getElementById('expiry_year');
    const cardCVCInput = document.getElementById('card_cvc');
    const submitButton = document.querySelector('.submit-btn');

    submitButton.disabled = true;

    function validateCardDetails() {
        const cardNumber = cardNumberInput.value.trim();
        const cardHolderName = cardHolderNameInput.value.trim();
        const expiryMonth = expiryMonthInput.value.trim();
        const expiryYear = expiryYearInput.value.trim();
        const cardCVC = cardCVCInput.value.trim();
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();

        let isValid = true;

        if (!/^\d{16}$/.test(cardNumber)) {
            isValid = false;
        }

        if (cardHolderName === "") {
            isValid = false;
        }

        if (!/^(0[1-9]|1[0-2])$/.test(expiryMonth)) {
            isValid = false;
        }

        if (!/^\d{4}$/.test(expiryYear) || parseInt(expiryYear) < currentYear || (parseInt(expiryYear) === currentYear && parseInt(expiryMonth) < currentMonth)) {
            isValid = false;
        }

        if (!/^\d{3}$/.test(cardCVC)) {
            isValid = false;
        }

        submitButton.disabled = !isValid;
    }

    cardNumberInput.addEventListener('input', validateCardDetails);
    cardHolderNameInput.addEventListener('input', validateCardDetails);
    expiryMonthInput.addEventListener('input', validateCardDetails);
    expiryYearInput.addEventListener('input', validateCardDetails);
    cardCVCInput.addEventListener('input', validateCardDetails);

    validateCardDetails();
});

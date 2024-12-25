document.addEventListener('DOMContentLoaded', function () {
    const cardNumberInput = document.getElementById('card_number');
    const cardHolderNameInput = document.getElementById('card_holder_name');
    const expiryMonthInput = document.getElementById('expiry_month');
    const expiryYearInput = document.getElementById('expiry_year');
    const cardCVCInput = document.getElementById('card_cvc');
    const submitButton = document.querySelector('.submit-btn');

    // Initially disable submit button
    submitButton.disabled = true;

    function validateCardDetails() {
        const cardNumber = cardNumberInput.value.trim();
        const cardHolderName = cardHolderNameInput.value.trim();
        const expiryMonth = expiryMonthInput.value;
        const expiryYear = expiryYearInput.value;
        const cardCVC = cardCVCInput.value.trim();
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();

        let isValid = true;

        // Validate card number
        if (!/^\d{16}$/.test(cardNumber)) {
            isValid = false;
        }

        // Validate card holder name
        if (cardHolderName === "") {
            isValid = false;
        }

        // Validate expiry month and year
        if (expiryMonth === "month" || expiryYear === "year") {
            isValid = false;
        } else {
            const expiryDate = new Date(expiryYear, expiryMonth - 1);
            if (expiryDate < currentDate || expiryYear < currentYear || (expiryYear === currentYear && expiryMonth < currentMonth)) {
                isValid = false;
            }
        }

        // Validate CVC
        if (!/^\d{3}$/.test(cardCVC)) {
            isValid = false;
        }

        // Enable submit button if all validations pass
        submitButton.disabled = !isValid;
    }

    cardNumberInput.addEventListener('input', validateCardDetails);
    cardHolderNameInput.addEventListener('input', validateCardDetails);
    expiryMonthInput.addEventListener('change', validateCardDetails);
    expiryYearInput.addEventListener('change', validateCardDetails);
    cardCVCInput.addEventListener('input', validateCardDetails);
});

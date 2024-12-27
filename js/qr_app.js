// ============================================================================
// General Functions
// ============================================================================

// JavaScript to toggle the search bar visibility
document.addEventListener('DOMContentLoaded', function () {
    const searchIcon = document.getElementById('search-icon');
    const searchContainer = document.getElementById('search-container');

    searchIcon.addEventListener('click', function () {
        // Toggle the "show" class to show/hide the search bar
        searchContainer.classList.toggle('show');
    });
});
// ============================================================================
// Page Load (jQuery)
// ============================================================================

$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();
    
    // Confirmation message
    // $('[data-confirm]').on('click', e => {
    //     const text = e.target.dataset.confirm || 'Are you sure you want to delete the record?';
    //     if (!confirm(text)) {
    //         e.preventDefault();
    //         e.stopImmediatePropagation();
    //     }
    // });

    $('[data-post]').on('click', e => {
        e.preventDefault();
    
        // Check if the element has a `data-confirm` attribute
        const confirmText = e.target.dataset.confirm;
        if (confirmText) {
            if (!confirm(confirmText)) {
                // Stop execution if user cancels
                return false;
            }
        }
    
        // Proceed with POST request
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    
    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    // $('[data-post]').on('click', e => {
    //     e.preventDefault();
    //     const url = e.target.dataset.post;
    //     const f = $('<form>').appendTo(document.body)[0];
    //     f.method = 'POST';
    //     f.action = url || location;
    //     f.submit();
    // });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

    
// Photo preview for multiple files
// Sanitize the file name (optional)
function sanitizeFileName(fileName) {
    return fileName.replace(/\s+/g, '_').replace(/[^\w.-]/g, '');
}

$('label.upload input[type=file]').on('change', function(e) {
    const files = e.target.files;
    const previewContainer = $('#product_photo_previews');
    previewContainer.empty();  // Clear previous previews

    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {  // Only process image files
            // Create the image element
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.maxWidth = '200px';  // Adjust size as needed
            img.style.margin = '5px';  // Add space between previews
            img.style.position = 'relative';  // Ensure positioning for checkbox

            // Get the sanitized file name for product_cover input
            const fileName = sanitizeFileName(file.name);

            // Create the checkbox element
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.classList.add('cover-checkbox');
            checkbox.style.position = 'absolute';
            checkbox.style.top = '5px';
            checkbox.style.right = '15px';
            checkbox.style.zIndex = '1';

            // Create a container div to hold both the image and the checkbox
            const container = document.createElement('div');
            container.style.position = 'relative';  // Make sure the checkbox stays within the image
            container.style.display = 'inline-block';  // Ensure images are inline
            container.appendChild(img);
            container.appendChild(checkbox);

            // Append the container with the image and checkbox to the preview container
            previewContainer.append(container);

            // Add an event listener to handle checkbox selection
            checkbox.addEventListener('change', function() {
                // Uncheck all checkboxes before checking the selected one
                const checkboxes = document.querySelectorAll('.cover-checkbox');
                checkboxes.forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;  // Uncheck other checkboxes
                    }
                });

                // Update the product_cover input value with the sanitized file name of the selected image
                if (checkbox.checked) {
                    // Set the default image when no checkbox is selected
                    document.getElementById('preview').src = img.src;
                    
                    // Programmatically trigger the file input to accept the selected image
                    const fileInput = document.getElementById('product_cover');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;  // Set the files on the input element
                } else {
                    alert("You have not choose the cover picture yet");

                }
            });
        }
    });
});
 

});
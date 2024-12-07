// JavaScript to toggle the search bar visibility
document.addEventListener('DOMContentLoaded', function () {
    const searchIcon = document.getElementById('search-icon');
    const searchContainer = document.getElementById('search-container');

    searchIcon.addEventListener('click', function () {
        // Toggle the "show" class to show/hide the search bar
        searchContainer.classList.toggle('show');
    });
});
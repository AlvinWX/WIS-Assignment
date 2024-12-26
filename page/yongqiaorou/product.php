<?php
require '../../_base.php';

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    temp('info',"Unauthourized Access");
    redirect('../../login.php');
}

$categories = $_db->query("SELECT * FROM category WHERE category_status = 1")->fetchAll();
$query = "SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE p.product_status = 1";

// Initialize binding values for filters
$bindValues = [];

// Handle category filter if it's set
if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
    $query .= " AND p.category_id = :category_id";
    $bindValues[':category_id'] = $_GET['category_id'];
}

// Handle price filter if it's set
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
if ($min_price != '' && $max_price != '') {
    $query .= " AND p.product_price BETWEEN :min_price AND :max_price";
    $bindValues[':min_price'] = $min_price;
    $bindValues[':max_price'] = $max_price;
}

// Handle search field and search value if set
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : 'product_name';
$search_value = isset($_GET['search']) ? $_GET['search'] : '';
if ($search_value != '') {
    $query .= " AND p.$search_field LIKE :search_value";
    $bindValues[':search_value'] = '%' . $search_value . '%';
}

// Prepare and execute the query
$stmt = $_db->prepare($query);
foreach ($bindValues as $param => $value) {
    $stmt->bindValue($param, $value);
}
$stmt->execute();
$arr = $stmt->fetchAll();

$_title = 'Product List'; 
include '../../_admin_head.php'; 
?>

<form method="GET" action="" class="search-form" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <!-- Category Dropdown -->
    <div style="display: flex; align-items: center; gap: 10px;">
        <select id="categoryFilter" name="category_id" style="padding: 5px; width: 150px;">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category->category_id ?>" <?= isset($_GET['category_id']) && $_GET['category_id'] == $category->category_id ? 'selected' : '' ?>><?= $category->category_name ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" style="padding: 5px 10px;">Filter</button>
    </div>

    <!-- Product Search Fields -->
    <div style="display: flex; align-items: center; gap: 10px;">
        <select id="searchField" name="search_field" style="padding: 5px; width: 150px;">
            <option value="product_name" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_name' ? 'selected' : '' ?>>Product Name</option>
            <option value="product_desc" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_desc' ? 'selected' : '' ?>>Description</option>
            <option value="product_price" <?= isset($_GET['search_field']) && $_GET['search_field'] == 'product_price' ? 'selected' : '' ?>>Price</option>
        </select>

        <!-- Dynamic input fields -->
        <div id="textInputGroup" style="display: none; flex: 1;">
            <input type="text" id="textInput" name="search" placeholder="Search" style="padding: 5px; width: 220px;" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>

        <div id="priceInputGroup" style="display: none; flex: 1;">
            <input type="number" step="0.01" id="minPriceInput" name="min_price" placeholder="Min Price" style="padding: 5px; width: 100px;" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">
            <input type="number" step="0.01" id="maxPriceInput" name="max_price" placeholder="Max Price" style="padding: 5px; width: 100px;" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>">
        </div>

        <button type="submit" style="padding: 5px 10px;">Search</button>
    </div>

</form>

<?php
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : 'product_name';
$search_value = isset($_GET['search']) ? $_GET['search'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

$query = "SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE p.product_status = 1";

if ($search_field == 'product_price') {
    if (!empty($min_price) && !empty($max_price)) {
        $query .= " AND p.$search_field BETWEEN :min_price AND :max_price";
        $stmt = $_db->prepare($query);
        $stmt->bindValue(':min_price', $min_price);
        $stmt->bindValue(':max_price', $max_price);
    } else {
        // echo "<p style='color:red;'>Please provide both Min and Max price!</p>";
        $stmt = $_db->prepare($query); // Prepare the base query
    }
} else {
    $query .= " AND p.$search_field LIKE :search_value"; // Default to the product table
    $stmt = $_db->prepare($query);
    $stmt->bindValue(':search_value', '%' . $search_value . '%');
}
?>

<?php

$stock_alert = [];
if(count($arr)) { ?>
    <p><?= count($arr) ?> record(s)</p>

    <div style="text-align: center; margin-top: 20px;">
        <table class="table" style="margin-left: auto; margin-right: auto;">
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Product Cover</th>
                <th>Product Resources</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock Left</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($arr as $s): ?>
            <tr>
                <td><?= $s->product_id ?></td>
                <td><?= $s->product_name ?></td>
                <td><img src="../../images/product_pic/<?= $s->product_cover ?>"></td>
                <td>
                    <?php if (!empty($s->product_resources)): ?>
                        <?php
                            $resources = json_decode($s->product_resources, true);
                            if ($resources): ?>
                                <div id="carousel-<?= $s->product_id ?>" class="custom-carousel">
                                    <div class="carousel-inner" data-current="0">
                                        <?php foreach ($resources as $index => $resource): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                <?php if (strpos(mime_content_type("../../uploads/$resource"), 'image/') !== false): ?>
                                                    <img class="d-block w-100 custom-carousel-item" src="/../../uploads/<?= $resource ?>" alt="Resource <?= $index + 1 ?>">
                                                <?php elseif (strpos(mime_content_type("../../uploads/$resource"), 'video/') !== false): ?>
                                                    <video class="d-block w-100 custom-carousel-item" controls>
                                                        <source src="/../../uploads/<?= $resource ?>" type="video/<?= pathinfo($resource, PATHINFO_EXTENSION) ?>">
                                                    </video>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" onclick="prevSlide('carousel-<?= $s->product_id ?>')">
                                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                                    </button>
                                    <button class="carousel-control-next" onclick="nextSlide('carousel-<?= $s->product_id ?>')">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <script>
                                    startAutoSlide('carousel-<?= $s->product_id ?>');
                                </script>
                            <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td><?= $s->product_desc ?></td>
                <td><?= $s->product_price ?></td>
                <td><?= $s->category_name?></td>
                <td>
                    <?= $s->product_stock ?>
                    <br>
                    <?php
                    if($s->product_stock <= 2){ 
                        echo "<span style='color:red;'> Low Stock!!</span>";
                        $stock_alert[] = $s->product_name;
                    } else {
                        echo "<span style='color:green;'> Good </span>";
                    }
                    ?>
                </td>
                <td>
                    <button data-get="/page/yongqiaorou/product_detail.php?id=<?= $s->product_id ?>">Detail</button>
                    <button data-get="/page/yongqiaorou/product_update.php?id=<?= $s->product_id ?>">Update</button>
                    <button data-post="/page/yongqiaorou/product_delete.php?id=<?= $s->product_id ?>" data-confirm>Delete</button>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    </div> 
<?php 
    if(!empty($stock_alert)){
        $names = '';
        foreach($stock_alert as $index => $alert) {
            $names .= $alert;
            if ($index < count($stock_alert) - 1) {
                $names .= ', ';
            }
        }
        $verb = count($stock_alert) == 1 ? 'is' : 'are';
        temp('info', "$names $verb low stock now!!");
    }
?>
<?php }else{?>
    <p class="err">No record found.</p>
<?php }?>
<a href="product_recover.php"><span id="dot" class="dot_left" style="color:rgb(245, 167, 167);"><i class="fa fa-trash" aria-hidden="true"></i></span></a>
<a href="product_add.php"><span id="dot" class="dot_right"><i class="fa fa-plus" aria-hidden="true"></i></span></a>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchField = document.getElementById('searchField');
    const textInputGroup = document.getElementById('textInputGroup');
    const priceInputGroup = document.getElementById('priceInputGroup');

    // Function to toggle input fields based on selected option
    const toggleInputFields = () => {
        const selectedValue = searchField.value;

        if (selectedValue === 'product_price') {
            priceInputGroup.style.display = 'block';
            textInputGroup.style.display = 'none';
        } else {
            priceInputGroup.style.display = 'none';
            textInputGroup.style.display = 'block';
        }
    };

    // Initialize on page load
    toggleInputFields();

    // Add event listener for dropdown changes
    searchField.addEventListener('change', toggleInputFields);
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.custom-carousel').forEach(carousel => {
        const carouselId = carousel.id;
        startAutoSlide(carouselId); // Initialize auto-slide for each carousel
    });
});

function startAutoSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const inner = carousel.querySelector('.carousel-inner');
    const items = carousel.querySelectorAll('.carousel-item');
    const totalItems = items.length;

    if (totalItems <= 1) return; // Do not initialize auto-slide for single-item carousels

    let currentIndex = 0;

    setInterval(() => {
        currentIndex = (currentIndex + 1) % totalItems; // Cycle through items
        updateSlide(inner, currentIndex);
    }, 3000); // Change slides every 3 seconds
}

function prevSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const inner = carousel.querySelector('.carousel-inner');
    const items = carousel.querySelectorAll('.carousel-item');
    const totalItems = items.length;

    let currentIndex = parseInt(inner.getAttribute('data-current')) || 0;
    currentIndex = (currentIndex - 1 + totalItems) % totalItems; // Move to previous item

    updateSlide(inner, currentIndex);
}

function nextSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    const inner = carousel.querySelector('.carousel-inner');
    const items = carousel.querySelectorAll('.carousel-item');
    const totalItems = items.length;

    let currentIndex = parseInt(inner.getAttribute('data-current')) || 0;
    currentIndex = (currentIndex + 1) % totalItems; // Move to next item

    updateSlide(inner, currentIndex);
}

function updateSlide(inner, index) {
    inner.style.transform = `translateX(-${index * 100}%)`; // Slide to the desired item
    inner.setAttribute('data-current', index); // Update current index
}

</script>

<?php
include '../../_foot.php';
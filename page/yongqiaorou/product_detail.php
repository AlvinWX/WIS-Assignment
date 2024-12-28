<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
$youtube_prefix = 'https://www.youtube.com/watch?v=';
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}
$id = req('id');

$stm = $_db->prepare('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE p.product_status = 1 AND product_id = ?');
$stm->execute([$id]);
$s = $stm->fetch();

if (!$s) {
    redirect('/');
}

if (isset($product_youtube_url) && !empty($product_youtube_url) && strpos($product_youtube_url, $youtube_prefix) !== 0) {
    $product_youtube_url = $youtube_prefix . $product_youtube_url;
}

// ----------------------------------------------------------------------------
$_title = 'Detail';
include '../../_admin_head.php';
?>

<table class="table detail" style="margin-top: 100px; margin-left:auto; margin-right:auto;">
    <tr>
        <th>Id</th>
        <td><?= $s->product_id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $s->product_name ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= $s->product_desc ?></td>
    </tr>
    <tr>
        <th>Price</th>
        <td><?= $s->product_price ?></td>
    </tr>
    <tr>
        <th>Stock Left</th>
        <td><?= $s->product_stock ?></td>
    </tr>
    <tr>
        <th>Category</th>
        <td><?= $s->category_name?></td>
    </tr>
    <tr>
        <th>Cover Picture</th>
        <td><img src="../../images/product_pic/<?= $s->product_cover ?>"/></td>
    </tr>
    <tr>
        <th>Extra Resources</th>
        <td>
            <?php if (!empty($s->product_resources)): ?>
                <?php
                    $resources = json_decode($s->product_resources, true);
                    if ($resources): ?>
                        <div id="carousel-<?= $s->product_id ?>" class="custom-carousel">
                            <div class="carousel-inner" data-current="0">
                                <?php foreach ($resources as $index => $resource): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <?php if (strpos(mime_content_type("../../images/uploads/products/$resource"), 'image/') !== false): ?>
                                            <img class="d-block w-100 custom-carousel-item" src="/../../images/uploads/products/<?= $resource ?>" alt="Resource <?= $index + 1 ?>">
                                        <?php elseif (strpos(mime_content_type("../../images/uploads/products/$resource"), 'video/') !== false): ?>
                                            <video class="d-block w-100 custom-carousel-item" controls>
                                                <source src="/../../images/uploads/products/<?= $resource ?>" type="video/<?= pathinfo($resource, PATHINFO_EXTENSION) ?>">
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
    </tr>
    
    <tr>
        <th>Youtube URL</th>
        <td><?= $s->product_youtube_url ?></td>
    </tr>
</table>

<br>

<button data-get="/page/yongqiaorou/product.php"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<script>
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
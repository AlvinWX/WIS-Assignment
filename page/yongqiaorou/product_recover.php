<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}
if (is_post()) {
    $id         = req('id');
    $stm = $_db->prepare('UPDATE product SET product_status=1, admin_id = ?, product_last_update = ? WHERE product_id = ? ');
    $stm->execute([$admin_id, date("Y-m-d H:i:s"), $id]);

    temp('info', 'Product recovered');
    redirect('product.php');
}
$arr = $_db->query('SELECT * FROM product p JOIN category c ON p.category_id = c.category_id WHERE p.product_status = 0')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Product Recover';
include '../../_admin_head.php';
?>

<?php if(count($arr)) {?>
<p><?= count($arr) ?> record(s)</p>
<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Product Cover</th>
        <th>Product Resources</th>
        <th>Description</th>
        <th>Price</th>
        <th>Category</th>
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
                <td><?= $s->product_desc ?></td>
                <td><?= $s->product_price ?></td>
                <td><?= $s->category_name?></td>
                <td>
                    <button  data-post="product_recover.php?id=<?= $s->product_id ?>" style="width:200px;">Recover Back</button>
                </td>
            </tr>
    <?php endforeach ?>
</table>
<?php }else{?>
    <p style="color:red;">No record deleted.</p>
<?php }?>

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
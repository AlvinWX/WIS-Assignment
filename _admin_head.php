<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?= $_title ?? 'Untitled' ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/qr_app.css">
    <script src="/js/qr_app.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <div class="home-logo">
            <a href="/">
                <img src="/images/favicon.png" alt="Clickable Image">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <!-- Dropdown Menu -->
                <li>
                    <a href="#">More</a>
                    <div class="dropdown-content">
                        <a href="#">XXXXX</a>
                        <a href="/page/chanyijing/admin/member_management/member_list.php">Member Management</a>
                        <a href="/page/chanyijing/admin/admin_management/admin_list.php">Admin Management</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="right-logo">
            
            <a href="/productsearch.php?product_name=&category_id=&minprice=&maxprice=&sort=product_name&dir=asc">
                <img class="search" src="/images/search.png" alt="Search Icon" id="search-icon">
            </a>

            <a href="/index.php">
                <img src="/images/shopping-cart.png" alt="Clickable Image">
            </a>

            <a href="/login.php">
                <img src="/images/user.png" alt="Clickable Image">
            </a>
        </div>
        <div class="search-container" id="search-container">
            <input type="text" class="search-bar" placeholder="Search...">
        </div>
    </header>

    <nav>
        <a href="/">Index</a>
        <a href="product.php">Product</a>
        <a href="category.php">Category</a>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
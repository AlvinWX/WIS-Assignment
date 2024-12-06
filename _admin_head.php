<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <h1><a href="/">Database SELECT</a></h1>
    </header>

    <nav>
        <a href="/">Index</a>
        <a href="product.php">Product</a>
        <!-- <a href="/demo2.php">Demo 2</a>
        <a href="/demo3.php">Demo 3</a>
        <a href="/demo4.php">Demo 4</a>
        <a href="/demo5.php">Demo 5</a>
        <a href="/demo6.php">Demo 6</a> -->
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
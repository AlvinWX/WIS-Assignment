<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="stylesheet" href="/css/yj_app.css">
    <link rel="shortcut icon" href="/images/tar_grocer_icon.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/yj_app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <h1><a href="/">TAR GROCER</a></h1>
    </header>

    <nav>
        <a href="/">Index</a>
        <a href="/page/chanyijing/admin/member_management/member_list.php">Member Management</a>
        <a href="/page/chanyijing/admin/admin_management/admin_list.php">Admin Management</a>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
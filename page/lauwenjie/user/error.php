<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="wj_css.css">
    <link rel="stylesheet" href="/css/wj_app.css">
</head>
<body>
    <div class="container">
        <h1>Error</h1>
        <p><?php echo $_SESSION['error'] ?? 'An unexpected error occurred.'; ?></p>
        <a href="/index.php">Return to Home</a>
    </div>
</body>
</html>

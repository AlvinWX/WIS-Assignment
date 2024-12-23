<link rel="stylesheet" href="/css/yj_app.css">

<?php

require '../../../../_base.php';

$user = $_SESSION['user'] ?? null;
$memberID = $user->memberID;

$_title = 'Thank You for Your Feedback';
include '../../../../_head.php';
?>

<div class="thank-you-container">
    <div class="thank-you-message">
        <h2>Thank you for your feedback!</h2>
        <p>Your input is greatly appreciated and helps us improve our services.</p>
        <br/><br/>
    </div>
    
    <button data-get="history_list.php?memberID=<?= $memberID ?>" class="pink-btn">Back</button>
</div>

<?php
include '../../../../_foot.php';

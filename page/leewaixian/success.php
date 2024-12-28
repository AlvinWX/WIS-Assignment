<?php
require '../../_base.php';
include '../../_head.php';

$user = $_SESSION['user'] ?? null;
$member_id = $user->member_id;
if(empty($member_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

$success_details = $_SESSION['success_details'];

$amount_paid = $success_details['amount_paid'];
$order_id = $success_details['order_id'];
$order_date = $success_details['order_date'];
$ship_date = date("Y-m-d", strtotime($success_details['order_date']));
$received_date = date("Y-m-d", strtotime($success_details['order_date']));
$points = $success_details['points'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/flash.css">
    <link rel="stylesheet" href="../../css/success.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="../../js/shoppingcart.js" defer></script>
    <title>Payment Success</title>
</head>
<body>
<div class="empty-box"></div>
    <div id="info"><?= temp('info')?></div>    
    <div class="container">
        <div class="success-container">
            <div class="success-header">
                <svg viewBox="0 0 24 24" width="100" height="100">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12ZM18.4158 9.70405C18.8055 9.31268 18.8041 8.67952 18.4127 8.28984L17.7041 7.58426C17.3127 7.19458 16.6796 7.19594 16.2899 7.58731L10.5183 13.3838L7.19723 10.1089C6.80398 9.72117 6.17083 9.7256 5.78305 10.1189L5.08092 10.8309C4.69314 11.2241 4.69758 11.8573 5.09083 12.2451L9.82912 16.9174C10.221 17.3039 10.8515 17.301 11.2399 16.911L18.4158 9.70405Z" fill="#008080"/>
                </svg>
                <h1>Order Success</h1>
            </div>
            <div class="order-details">
                <div class="amount"><h2>RM <?= sprintf('%.2f', $amount_paid) ?></h2></div>
                <h3>Paid</h3>
                <table>
                    <tr>
                        <td>Order ID</td>
                        <th><?= $order_id ?></th>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <th><?= $order_date ?></th>
                    </tr>
                    <tr>
                        <td>Estimated Ship Date</td>
                        <th><?= $ship_date ?></th>
                    </tr>
                    <tr>
                        <td>Estimated Received Date</td>
                        <th><?= $received_date ?></th>
                    </tr>
                    <tr>
                        <td>Points Earned</td>
                        <th><?= $points ?> points</th>
                    </tr>
                </table>
            </div>
            <div class="action-button">
                <button class="home" onclick="location.href='../../index.php'">Home Page</button>
                <button class="detail" onclick="location.href='../chanyijing/member/order_history/history_detail.php?order_id=<?= $order_id ?>'">View Details</button>
            </div>
        </div>
    </div>
</body>
</html>


<?php
include '../../_foot.php';
?>

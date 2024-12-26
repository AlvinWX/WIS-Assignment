<?php

// Include your base configuration or database connection file
include '../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users
auth('member', 'admin'); // Ensure the user is logged in as 'member' or 'admin'

// Retrieve current logged-in user's data from session
if (isset($_SESSION['user'])) {
    $currentUser = $_SESSION['user'];  // Get the logged-in user object

    // Check user type and extract corresponding user properties
    if ($currentUser->userType == 'member') {
        $userID = $currentUser->member_id;      // Example: member ID
        $userName = $currentUser->member_name;  // Example: member name
        $userEmail = $currentUser->member_email; // Example: member email
        $userPhone = $currentUser->member_phone; // Example: member phone
        $userGender = $currentUser->member_gender; // Example: member gender
        $userProfilePic = $currentUser->member_profile_pic; // Example: member profile picture
    } elseif ($currentUser->userType == 'admin') {
        $userID = $currentUser->admin_id;      // Example: admin ID
        $userName = $currentUser->admin_name;  // Example: admin name
        $userEmail = $currentUser->admin_email; // Example: admin email
        $userPhone = $currentUser->admin_phone; // Example: admin phone
        $userGender = $currentUser->admin_gender; // Example: admin gender
        $userProfilePic = $currentUser->admin_profile_pic; // Example: admin profile picture
    }} else {
        // Redirect to login page if the user is not logged in
        redirect('/login.php');
        exit();
    }
?>

<?php
// Include the head template
$_title = 'User Profile';
include '../_head.php';
?>
<style>
    form {
        margin-top: 200px;
    }
</style>
<form method="post" class="form" enctype="multipart/form-data">
    <label for="email">Email</label>
    <input type="text" name="email" maxlength="100" value="<?= htmlspecialchars($userEmail) ?>" />
    <?= err('email') ?>

    <label for="name">Name</label>
    <input type="text" name="name" maxlength="100" value="<?= htmlspecialchars($userName) ?>" />
    <?= err('name') ?>

    <label for="phone">Phone</label>
    <input type="text" name="phone" maxlength="15" value="<?= htmlspecialchars($userPhone) ?>" />
    <?= err('phone') ?>

    <label for="gender">Gender</label>
    <select name="gender" id="gender">
        <option value="Male" <?= $userGender == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $userGender == 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= $userGender == 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
    <?= err('gender') ?>

    <label for="photo">Profile Picture</label>
    <label class="upload" tabindex="0">
        <input type="file" name="photo" accept="image/*" hidden />
        <img src="/uploads/profiles/<?= htmlspecialchars($userProfilePic) ?>" alt="Profile Picture" />
    </label>
    <?= err('photo') ?>

    <section>
        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
// Include the footer template
include '../_foot.php';
?>

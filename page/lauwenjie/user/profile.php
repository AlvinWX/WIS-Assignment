<?php

// Include your base configuration or database connection file
include '../../../_base.php';

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
        // Fetch existing address for the member
        $stm = $_db->prepare('SELECT * FROM address WHERE member_id = ? LIMIT 1');
        $stm->execute([$userID]);
        $userAddress = $stm->fetch(PDO::FETCH_ASSOC);
    } elseif ($currentUser->userType == 'admin') {
        // Admin data retrieval (if necessary)
        // No address handling needed for admin
    }
} else {
    // Redirect to login page if the user is not logged in
    redirect('/login.php');
    exit();
}

// Handle form submission for profile and address update
if (is_post()) {
    // Get profile data
    $newName = req('name');
    $newPhone = req('phone');
    $newGender = req('gender');
    $newPhoto = $_FILES['photo'];

    // Get address data for members
    if ($currentUser->userType == 'member') {
        $newStreet = req('street');
        $newPostcode = req('postcode');
        $newCity = req('city');
        $newState = req('state');
    }

    // Validate profile inputs
    if ($newName == '') {
        $_err['name'] = 'Name is required';
    }
    if ($newPhone == '') {
        $_err['phone'] = 'Phone is required';
    }
    if ($newGender == '') {
        $_err['gender'] = 'Gender is required';
    }

    // Handle profile picture upload if exists
    if ($newPhoto['error'] == UPLOAD_ERR_OK) {
        // Define the upload directory
        $uploadDir = '../../../images/uploads/profiles/';
        $uploadFile = $uploadDir . basename($newPhoto['name']);
        
        // Check if file is a valid image
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $validExtensions)) {
            $_err['photo'] = 'Only JPG, JPEG, PNG, GIF files are allowed';
        } else {
            // Move uploaded file to the profile directory
            if (move_uploaded_file($newPhoto['tmp_name'], $uploadFile)) {
                // Update profile picture filename in the database
                $newProfilePic = basename($newPhoto['name']);
            } else {
                $_err['photo'] = 'Error uploading profile picture';
            }
        }
    }

    // Validate address inputs for members
    if ($currentUser->userType == 'member') {
        if ($newStreet == '') {
            $_err['street'] = 'Street address is required';
        }
        if ($newPostcode == '') {
            $_err['postcode'] = 'Postcode is required';
        }
        if ($newCity == '') {
            $_err['city'] = 'City is required';
        }
        if ($newState == '') {
            $_err['state'] = 'State is required';
        }
    }

    // If no errors, update user profile and address
    if (!$_err) {
        if ($currentUser->userType == 'member') {
            // Update member profile (do not change email)
            $stm = $_db->prepare('
                UPDATE member 
                SET member_name = ?, member_phone = ?, member_gender = ?, member_profile_pic = ? 
                WHERE member_id = ?
            ');
            $stm->execute([$newName, $newPhone, $newGender, $newProfilePic ?? $userProfilePic, $userID]);
            
            // Update address (if new address data is provided)
            if ($userAddress) {
                // Update existing address
                $stm = $_db->prepare('
                    UPDATE address
                    SET address_street = ?, address_postcode = ?, address_city = ?, address_state = ?
                    WHERE address_id = ?
                ');
                $stm->execute([$newStreet, $newPostcode, $newCity, $newState, $userAddress['address_id']]);
            } else {
                // Insert new address if it doesn't exist
                $addressID = 'AD' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // Generate unique address ID
                $stm = $_db->prepare('
                    INSERT INTO address (address_id, address_street, address_postcode, address_city, address_state, member_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ');
                $stm->execute([$addressID, $newStreet, $newPostcode, $newCity, $newState, $userID]);
            }

            // Update session with new data
            $_SESSION['user']->member_name = $newName;
            $_SESSION['user']->member_phone = $newPhone;
            $_SESSION['user']->member_gender = $newGender;
            $_SESSION['user']->member_profile_pic = $newProfilePic ?? $userProfilePic;
        } elseif ($currentUser->userType == 'admin') {
            // Admin profile update code (if necessary)
        }

        temp('info', 'Profile and address updated successfully');
        redirect('/page/lauwenjie/user/profile.php');
    }
}
?>

<?php
// Include the head template
$_title = 'User Profile';
include '../../../_head.php';
?>
<div id="info"><?= temp('info')?></div>
<div class="login-container">
    <form method="post" class="form" enctype="multipart/form-data">
        <h2>Profile</h2>

        <!-- Profile Picture -->
        <label for="photo">Profile Picture</label>
        <label class="upload" tabindex="0">
            <input type="file" name="photo" accept="image/*" hidden />
            <img src="../../../images/uploads/profiles/<?= htmlspecialchars($userProfilePic) ?>" alt="Profile Picture" />
        </label>
        <?= err('photo') ?>

        <!-- Email -->
        <label for="email">Email</label>
        <input type="text" name="email" maxlength="100" value="<?= htmlspecialchars($userEmail) ?>" readonly/>

        <!-- Name -->
        <label for="name">Name</label>
        <input type="text" name="name" maxlength="100" value="<?= htmlspecialchars($userName) ?>" />
        <?= err('name') ?>

        <!-- Phone -->
        <label for="phone">Phone</label>
        <input type="text" name="phone" maxlength="15" value="<?= htmlspecialchars($userPhone) ?>" />
        <?= err('phone') ?>

        <!-- Gender -->
        <label for="gender">Gender</label>
        <select name="gender" id="gender">
            <option value="Male" <?= $userGender == 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $userGender == 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $userGender == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
        <?= err('gender') ?>

        <?php if ($currentUser->userType == 'member'): ?>
        <!-- Address Section -->
        <h3>Address</h3>
        <label for="street">Street</label>
        <input type="text" name="street" maxlength="200" value="<?= htmlspecialchars($userAddress['address_street'] ?? '') ?>" />
        <?= err('street') ?>

        <label for="postcode">Postcode</label>
        <input type="text" name="postcode" maxlength="10" value="<?= htmlspecialchars($userAddress['address_postcode'] ?? '') ?>" />
        <?= err('postcode') ?>

        <label for="city">City</label>
        <input type="text" name="city" maxlength="100" value="<?= htmlspecialchars($userAddress['address_city'] ?? '') ?>" />
        <?= err('city') ?>

        <label for="state">State</label>
        <input type="text" name="state" maxlength="100" value="<?= htmlspecialchars($userAddress['address_state'] ?? '') ?>" />
        <?= err('state') ?>
        <?php endif; ?>

        <section>
            <button class="login-btn">Submit</button>
            <button type="reset" class="login-btn">Reset</button>
        </section>
    </form>
</div>

<?php
// Include the footer template
include '../../../_foot.php';
?>

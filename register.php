<?php
require '../_base.php';

session_start(); // Start the session
date_default_timezone_set('Asia/Kuala_Lumpur');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $memberDateJoined = date('Y-m-d'); // Get current date for the member's join date
    
    // Profile Picture Upload Handling
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $profilePic = $_FILES['profile_pic'];
        $profilePicName = time() . "_" . basename($profilePic['name']);
        $targetDir = "../uploads/profiles/";
        $targetFile = $targetDir . $profilePicName;

        // Allow certain file formats
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profilePic['type'], $allowedFileTypes)) {
            $error = "Only JPG, PNG, and GIF files are allowed.";
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            $error = "Sorry, file already exists.";
        }

        // Upload file if no errors
        if (empty($error) && move_uploaded_file($profilePic['tmp_name'], $targetFile)) {
            // Fetch the highest member ID (numeric part)
            $stmt = $_db->query("SELECT MAX(CAST(SUBSTRING(memberID, 3) AS UNSIGNED)) AS maxID FROM member");
            $row = $stmt->fetch(); // Fetch the result as an object

            // Check if the maxID is null, and if so, start with 'MB00001'
            if ($row->maxID === null) {
                $nextID = 'MB00001'; // Start with MB00001 if no members exist
            } else {
                // Increment the maxID value and format it as MB##### (e.g., MB00002)
                $nextID = 'MB' . str_pad($row->maxID + 1, 5, '0', STR_PAD_LEFT);
            }

            // Output the nextID for testing purposes (or use it in your insert statement)
            echo $nextID;
            
            // Insert the new member's data into the database, including the member's join date
            $stmt = $_db->prepare("INSERT INTO member (memberID, memberName, memberEmail, memberPassword, memberPhone, memberGender, memberProfilePic, memberDateJoined) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nextID,          // Member ID (e.g., MB00001)
                $name,            // Member Name
                $email,           // Member Email
                password_hash($password, PASSWORD_DEFAULT),  // Encrypted password
                $phone,           // Member Phone
                $gender,          // Member Gender
                $profilePicName,  // Profile Picture filename
                $memberDateJoined // Current date for Member Join Date
            ]);

            $_SESSION['user'] = $email;
            $success = "Registration successful! Welcome, $name.";
            header('Location: /page/login.php');
            exit;
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error = "Please upload a profile picture.";
    }
}

$_title = 'Register';
include '../_head.php';
?>

<body>
    <div class="register-container">
        <h2>Create Your Account</h2>

        <!-- Display success or error message -->
        <?php if (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" enctype="multipart/form-data" class="register-form">
            <div class="input-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="input-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" required>
            </div>

            <div class="input-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="input-group">
                <label for="profile_pic">Profile Picture:</label>
                <input type="file" name="profile_pic" id="profile_pic" accept="image/*" required>
            </div>

            <button type="submit" class="register-btn">Register</button>
        </form>
    </div>
</body>
</html>

<?php
include '../_foot.php';
?>
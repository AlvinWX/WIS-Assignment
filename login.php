<?php
require '_base.php';

session_start(); // Start the session

// Initialize success message variable
$success = '';
$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            // Check if the user exists
            $stmt = $_db->prepare("SELECT memberPassword FROM member WHERE memberEmail = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            if ($user) {
                if (password_verify($password, $user->memberPassword)) {
                    $_SESSION['user'] = $email;
                    $_SESSION['flash_success'] = "Login successful!";
                    header("Location: /index.php");
                    exit;
                } else {
                    $_SESSION['flash_error'] = "Invalid email or password.";
                }
            } else {
                $_SESSION['flash_error'] = "Email does not exist.";
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "An error occurred. Please try again.";
        }
    } else {
        $_SESSION['flash_error'] = "Please fill in both email and password.";
    }
    header("Location: login.php");
    exit;
}

$_title = 'Login';
include '_head.php';
?>
<br>
<body>
    <div class="login-container">
        <h2>Login to Your Account</h2>

        <!-- Display success message -->
        <?php if (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Display error message -->
        <?php if (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="input-group password-container">
                <label for="password">Password:</label>
                <div class="password-field-container">
                <input type="password" name="password" id="password" required>
                <!-- Eye icon inside the password field -->
                <img src="/images/closed-eyes.png" alt="Show Password" id="togglePassword" class="eye-icon">
            </div>
        </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="login-links">
            <p><a href="/page/forgot-password.php">Forgot Password?</a></p>
            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
        </div>
    </div>

    <script>
        // Toggle password visibility when eye icon is clicked
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('togglePassword');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text'; // Show password
                eyeIcon.src = '/images/opened-eye.png'; // Change icon to opened-eye
            } else {
                passwordInput.type = 'password'; // Hide password
                eyeIcon.src = '/images/closed-eyes.png'; // Change icon to closed-eye
            }
        });
    </script>
</body>
</html>

<?php
include '_foot.php';
?>
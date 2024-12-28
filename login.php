<?php
include '_base.php';

$max_attempts = 3;
$block_time = 60;
$_err = [];
$otp_verified = false;

if (is_post()) {
    $email = req('email');
    $password = req('password');
    $remember = req('remember') === 'on';

    // Email Login Validation
    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    if ($password == '') {
        $_err['password'] = 'Required';
    }

    // Check for login attempts
    if (isset($_SESSION['login_attempts'][$email]) && $_SESSION['login_attempts'][$email]['count'] >= $max_attempts) {
        $remaining_time = $_SESSION['login_attempts'][$email]['time'] + $block_time - time();
        if ($remaining_time > 0) {
            $_err['password'] = 'Too many attempts. Try again in ' . ceil($remaining_time / 60) . ' minutes';
        } else {
            unset($_SESSION['login_attempts'][$email]); // Reset after block time
        }
    }

    if (!$_err) {
        // Check if the user is a member
        $stm = $_db->prepare('
            SELECT *, "member" AS userType FROM member
            WHERE member_email = ? AND member_password = SHA1(?)
        ');
        $stm->execute([$email, $password]);
        $user = $stm->fetch();

        if (!$user) {
            // Check if the user is an admin if not found as a member
            $stm = $_db->prepare('
                SELECT *, "admin" AS userType FROM admin
                WHERE admin_email = ? AND admin_password = SHA1(?)
            ');
            $stm->execute([$email, $password]);
            $user = $stm->fetch();
        }

        if ($user) {
            unset($_SESSION['login_attempts'][$email]); // Reset attempts on success
            temp('info', 'Login successful as ' . $user->userType);
            $_SESSION['user'] = $user;
            $_SESSION['user_type'] = $user->userType;

            if ($user->userType === 'admin') {
                $_SESSION['admin_tier'] = $user->admin_tier;
                redirect('/page/chanyijing/admin/admin_management/admin_detail.php');
            } else {
                redirect('/index.php');
            }

            if ($remember) {
                setcookie('email', $email, time() + 86400 * 30, '/'); // 30 days
                setcookie('password', sha1($password), time() + 86400 * 30, '/');
            }
            exit();
        } else {
            $_err['password'] = 'Incorrect email or password';
            if (!isset($_SESSION['login_attempts'][$email])) {
                $_SESSION['login_attempts'][$email] = ['count' => 1, 'time' => time()];
            } else {
                $_SESSION['login_attempts'][$email]['count']++;
            }
        }
    }
}

// Auto-fill if "Remember Me" was set
$email = $_COOKIE['email'] ?? '';
$password = isset($_COOKIE['password']) ? '' : '';
$_title = 'Login';
include '_head.php';
?>

<div class="login-container">
    <h2>Login</h2>
    <form method="post" class="form">
        <div>
            <label>Email</label>
            <?= html_text('email', 'maxlength="100" class="input-field"') ?>
            <?= err('email') ?>

            <div style="position: relative;">
                <label for="password">Password</label>
                <?= html_password('password', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
                <img src="/images/closed-eyes.png" alt="Show Password" id="togglePassword" class="eye-icon">
                <?= err('password') ?>
            </div>
        </div>

        <button type="submit" class="login-btn">Login</button>
        <button type="reset" class="login-btn">Reset</button>
    </form>
    </br>
    <div class="remember-box">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="cursor: pointer;" class="remember-text">Remember Me</label>
        </div>
    </form>

    <a>Don't have an account?</a>
    <a href="/page/lauwenjie/user/registerMember.php">Register</a></br>
    <a href="/page/lauwenjie/user/reset.php">Forgot Password?</a>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('togglePassword');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = '/images/opened-eye.png';
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = '/images/closed-eyes.png';
        }
    });
</script>

<?php include '_foot.php'; ?>

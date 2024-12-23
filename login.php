<?php
include '_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');
    $password = req('password');

    $_err = [];

    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    if ($password == '') {
        $_err['password'] = 'Required';
    }

    if (!$_err) {
        $stm = $_db->prepare('
            SELECT *, "member" AS userType FROM member
            WHERE memberEmail = ? AND memberPassword = SHA1(?)
            UNION
            SELECT *, "admin" AS userType FROM admin
            WHERE adminEmail = ? AND adminPassword = SHA1(?)
        ');

        $stm->execute([$email, $password, $email, $password]);
        $user = $stm->fetch();

        if ($user) {
//             var_dump($user);
// exit;
            temp('info', 'Login successful as ' . $user->userType);
            $_SESSION['user'] = $user;
            $_SESSION['user_type'] = $user->userType;

            // Ensure Redirect
            if ($user->userType === 'admin') {
                
                redirect('/page/chanyijing/admin/admin_management/admin_detail.php');
            } else {
                redirect('/index.php');
            }
            exit();
        } else {
            $_err['password'] = 'Incorrect email or password';
        }
    }
}

// ----------------------------------------------------------------------------

$_title = 'Login';
include '_head.php';
?>

<div class="login-container">
    <h2>Login</h2>
    <form method="post" class="form">
        <div>
            <label for="email">Email</label>
            <?= html_text('email', 'maxlength="100" class="input-field"') ?>
            <?= err('email') ?>
        </div>

        <div style="position: relative;">
            <label for="password">Password</label>
            <?= html_password('password', 'maxlength="100" class="input-field" style="padding-right: 40px;"') ?>
            <img src="/images/closed-eyes.png" alt="Show Password" id="togglePassword" class="eye-icon">
            <?= err('password') ?>
        </div>

        <section>
            <button class="login-btn">Login</button>
            <button type="reset" class="login-btn">Reset</button>
        </section>
    </form>
    <a href="/user/registerMember.php">Register</a>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
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

</body>
</html>

<?php
include '_foot.php';
?>

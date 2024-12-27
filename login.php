<?php
include '_base.php';
require_once __DIR__ . '/lib/twilio-php-main/src/Twilio/autoload.php';
use Twilio\Rest\Client;

$max_attempts = 3;
$block_time = 60;
$accountSid = 'ACb8247836ce6a9fcf677a61907cc16503';
$authToken = 'd7b8e305f90b66e99048598f1ae6e132';
$twilioNumber = '+12185357906'; 
$message = 'Your OTP code is: ';
$client = new Client($accountSid, $authToken);

$_err = [];
$otp_verified = false;

if (is_post()) {
    $login_method = req('login_method');
    $email = req('email');
    $password = req('password');
    $phone = req('phone');
    $input_otp = req('input_otp');
    $remember = req('remember') === 'on';

    if ($login_method == 'otp' && $input_otp != '') {
        // Validate OTP and phone number
        if (isset($_SESSION['otp']) && $_SESSION['otp'] == $input_otp && $_SESSION['otp_phone'] == $phone) {
            unset($_SESSION['otp']);
            unset($_SESSION['otp_phone']);
            $otp_verified = true;
    
            // Check if the user exists in the database as a member
            $stm = $_db->prepare('SELECT *, "member" AS userType FROM member WHERE member_phone = ?');
            $stm->execute([$phone]);
            $user = $stm->fetch();
    
            if (!$user) {
                // If no member found, check if the user exists as an admin
                $stm = $_db->prepare('SELECT *, "admin" AS userType FROM admin WHERE admin_phone = ?');
                $stm->execute([$phone]);
                $user = $stm->fetch();
            }
    
            if ($user) {
                // User found, store session data and redirect based on user type
                $_SESSION['user'] = $user;
                $_SESSION['user_type'] = $user->userType;
                temp('info', 'Login successful as ' . $user->userType);
    
                // Redirect based on user type
                if ($user->userType === 'admin') {
                    $_SESSION['admin_tier'] = $user->admin_tier;
                    redirect('/page/chanyijing/admin/admin_management/admin_detail.php');
                } else {
                    redirect('/index.php');
                }
    
                exit(); // Ensure no further code is executed
            } else {
                $_err['otp'] = 'User not found. Please check your phone number.';
            }
        } else {
            $_err['otp'] = 'Invalid OTP. Please try again.';
        }
    }
    if ($login_method == 'email') {
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
}

if (isset($_GET['send_otp']) && $_GET['send_otp'] == '1') {
    $phone = $_GET['phone'];
    $normalizedPhone = (substr($phone, 0, 2) === '01') ? '+60' . substr($phone, 1) : $phone;
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_phone'] = $phone;

    $messageContent = $message . $otp;
    try {
        $client->messages->create(
            $normalizedPhone,
            [
                'from' => $twilioNumber,
                'body' => $messageContent,
            ]
        );
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully to $phoneNumber!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Error: ' . $e->getMessage()]);
    }
    exit;
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
            <label>
                <input type="radio" name="login_method" value="email" checked> Login with Email/Password
            </label>
            <label>
                <input type="radio" name="login_method" value="otp"> Login with OTP
            </label>
        </div>

        <div id="emailPasswordFields">
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

        <div id="otpFields" style="display: none;">
            <label>Phone Number</label>
            <div style="display: flex; align-items: center;">
                <?= html_text('phone', 'maxlength="15" class="input-field" id="phone"') ?>
                <button type="button" class="send-otp-btn" id="sendOtpBtn" style="margin-left: 10px;">Send OTP</button>
            </div>
            <?= err('phone') ?>
            
            <div id="otpInput">
                <label>Enter OTP</label>
                <?= html_text('input_otp', 'maxlength="6" class="input-field"') ?>
                <?= err('otp') ?>
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


</div>

<script>
    const loginMethodRadios = document.querySelectorAll('input[name="login_method"]');
    const emailPasswordFields = document.getElementById('emailPasswordFields');
    const otpFields = document.getElementById('otpFields');
    const sendOtpBtn = document.getElementById('sendOtpBtn');

    loginMethodRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === 'email') {
                emailPasswordFields.style.display = 'block';
                otpFields.style.display = 'none';
            } else {
                emailPasswordFields.style.display = 'none';
                otpFields.style.display = 'block';
            }
        });
    });

    sendOtpBtn.addEventListener('click', function () {
        const phone = document.getElementById('phone').value;
        if (phone) {
            fetch(`?send_otp=1&phone=${phone}`)
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                });
        } else {
            alert('Please enter your phone number.');
        }
    });

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

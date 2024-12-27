<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------


// TODO: (1) Delete expired tokens
$_db->query('DELETE FROM token WHERE expire < NOW()');

$id = req('id');

// TODO: (2) Is token id valid?
if (!is_exists($id, 'token', 'id')) {
    temp('info', 'Invalid token. Try again');
    redirect('/');
}

if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Send reset token (if valid)
    if (!$_err) {
        // (1) Check if the user is a member or admin
        $stm = $_db->prepare('
            UPDATE member
            SET member_password = SHA1(?)
            WHERE member_id = (SELECT member_id FROM token WHERE id = ?);

            DELETE FROM token WHERE id = ?;
        ');
        $stm->execute([$email, $email]);
        $user = $stm->fetch();

        // If a user was found (either member or admin)
        if ($user) {
            // (2) Generate token id
            $id = sha1(uniqid() . rand());

            // (3) Determine the appropriate IDs for member or admin
            if ($user->userType == 'member') {
                $memberId = $user->memberId;  // Assuming memberId is the ID for a member
                $adminId = null;
            } else {
                $adminId = $user->adminId;  // Assuming adminId is the ID for an admin
                $memberId = null;
            }

            // (4) Delete any existing token for the user
            $stm = $_db->prepare('DELETE FROM token WHERE member_id = ? OR admin_id = ?');
            $stm->execute([$memberId, $adminId]);

            // (5) Insert new token with the appropriate member_id or admin_id
            $stm = $_db->prepare('
                INSERT INTO token (id, expire, member_id, admin_id)
                VALUES (?, ADDTIME(NOW(), "00:05"), ?, ?);
            ');
            $stm->execute([$id, $memberId, $adminId]);

            // (6) Generate token URL
            $url = base("page/lauwenjie/user/token.php?id=$id");

            // (7) Prepare and send email content based on user role (member or admin)
            $m = get_mail();
            $m->addAddress($user->email, $user->name);
            $m->addEmbeddedImage(("../photos/$user->photo"), 'photo');
            $m->isHTML(true);
            $m->Subject = 'Reset Password';

            // Customize email based on user role (member or admin)
            $roleMessage = ($user->userType == 'admin') ?
                "As an admin, please use this link to reset your password." :
                "As a member, please use this link to reset your password.";

            $m->Body = "
                <img src='cid:photo'
                     style='width: 200px; height: 200px;
                            border: 1px solid #333'>
                <p>Dear $user->name,</p>
                <h1 style='color: red'>Reset Password</h1>
                <p>$roleMessage</p>
                <p>Please click <a href='$url'>here</a> to reset your password.</p>
                <p>From, 😺 Admin</p>
            ";

            // Send email and handle the result
            if ($m->send()) {
                echo 'Reset password link sent to your email.';
            } else {
                $_err['email'] = 'Failed to send email.';
            }
        } else {
            $_err['email'] = 'User not found';
        }
    }
}

// ----------------------------------------------------------------------------

$_title = 'Member | Reset Password';
include '../../../_head.php';
?>


<div class="login-container">
<form method="post" class="form">
    <h2>Reset Password</h2>
    <label for="password">New Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>

<form method="post" class="form">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>
</div>
<?php
include '../../../_foot.php';
?>

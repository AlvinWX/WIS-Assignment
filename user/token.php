<?php
include '../_base.php';

// ----------------------------------------------------------------------------

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
            SELECT *, "member" AS userType FROM member WHERE memberEmail = ? 
            UNION
            SELECT *, "admin" AS userType FROM admin WHERE adminEmail = ?
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
            $url = base("user/token.php?id=$id");

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

$_title = 'User | Reset Password';
include '../_head.php';
?>

<style>
    form {
        margin-top: 200px;
    }
</style>

<form method="post" class="form">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';
?>

<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } elseif (!is_exists($email, 'member', 'member_email')) {
        $_err['email'] = 'Email does not exist';
    }

    if (empty($_err)) {
        $stm = $_db->prepare('SELECT * FROM member WHERE member_email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();  // Fetch as object

        if ($u) {
            $id = sha1(uniqid() . rand());

            $stm = $_db->prepare('
                DELETE FROM token WHERE member_id = ?;

                INSERT INTO token (id, expire, member_id)
                VALUES (?, ADDTIME(NOW(), "00:05"), ?);
            ');
            $stm->execute([$u->member_id, $id, $u->member_id]);

            $url = base("page/lauwenjie/user/token.php?id=$id");

            $m = get_mail();
            $m->addAddress($u->member_email, $u->member_name);
            
            if (!empty($u->photo)) {
                $m->addEmbeddedImage("../../../images/uploads/profiles/$u->member_profile_pic", 'photo');
            }

            $m->isHTML(true);
            $m->Subject = 'Reset Password';
            $m->Body = "
                <img src='cid:photo'
                     style='width: 200px; height: 200px; border: 1px solid #333'>
                <p>Dear $u->member_name,</p>
                <h1 style='color: red'>Reset Password</h1>
                <p>
                    Please click <a href='$url'>here</a> to reset your password.
                </p>
                <p>From, 😺 Admin</p>
            ";

            $m->send();
        } else {
            $_err['email'] = 'User not found';
        }
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
include '../../../_head.php';
?>

<div class="login-container">
<form method="post" class="form">

        <h2>Reset Password</h2>
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <section>
        <button class="login-btn">Submit</button>
        <button type="reset" class="login-btn">Reset</button>
    </section>
    </div>
</form>

<?php
include '../../../_foot.php';
?>

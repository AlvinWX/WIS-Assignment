<?php
include '../../../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } else {
        $u = null;

        // Check if email exists in the member table
        $stm = $_db->prepare('SELECT * FROM member WHERE member_email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch(); // Fetch as object

        // Check if email exists in the admin table if not found in member
        $isAdmin = false;
        if (!$u) {
            $stm = $_db->prepare('SELECT * FROM admin WHERE admin_email = ?');
            $stm->execute([$email]);
            $u = $stm->fetch(); // Fetch as object
            $isAdmin = true;
        }

        if ($u) {
            $id = sha1(uniqid() . rand());

            if ($isAdmin) {
                // Handle admin token
                $stm = $_db->prepare('
                    DELETE FROM token_admin WHERE admin_id = ?;

                    INSERT INTO token_admin (id, expire, admin_id)
                    VALUES (?, ADDTIME(NOW(), "00:05"), ?);
                ');
                $stm->execute([$u->admin_id, $id, $u->admin_id]);
            } else {
                // Handle member token
                $stm = $_db->prepare('
                    DELETE FROM token WHERE member_id = ?;

                    INSERT INTO token (id, expire, member_id)
                    VALUES (?, ADDTIME(NOW(), "00:05"), ?);
                ');
                $stm->execute([$u->member_id, $id, $u->member_id]);
            }

            $url = base("page/lauwenjie/user/token.php?id=$id");

            $m = get_mail();
            $m->addAddress($u->member_email ?? $u->admin_email, $u->member_name ?? $u->admin_name);

            

            $m->isHTML(true);
            $m->Subject = 'Reset Password';
            $m->Body = "
                
                <p>Dear " . ($u->member_name ?? $u->admin_name) . ",</p>
                <h1 style='color: red'>Reset Password</h1>
                <p>
                    Please click <a href='$url'>here</a> to reset your password.
                </p>
                <p>From, 😺 Tar Grocer</p>
            ";

            $m->send();
            temp('info', 'Email sent!');
        } else {
            $_err['email'] = 'Email does not exist';
        }
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
include '../../../_head.php';
?>
<div id="info"><?= temp('info')?></div>
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
    </form>
</div>

<?php
include '../../../_foot.php';
?>
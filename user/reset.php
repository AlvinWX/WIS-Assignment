<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
<<<<<<< Updated upstream
    } elseif (!is_exists($email, 'member', 'member_email')) {
=======
    } elseif (!is_exists($email, 'member', 'memberEmail')) {
>>>>>>> Stashed changes
        $_err['email'] = 'Email does not exist';
    }

    if (empty($_err)) {
<<<<<<< Updated upstream
        $stm = $_db->prepare('SELECT * FROM member WHERE member_email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();  // Fetch as object
=======
        $stm = $_db->prepare('SELECT * FROM member WHERE memberEmail = ?');
        $stm->execute([$email]);
        $u = $stm->fetch(PDO::FETCH_OBJ);  // Fetch as object
>>>>>>> Stashed changes

        if ($u) {
            $id = sha1(uniqid() . rand());

            $stm = $_db->prepare('
                DELETE FROM token WHERE member_id = ?;
<<<<<<< Updated upstream

                INSERT INTO token (id, expire, member_id)
                VALUES (?, ADDTIME(NOW(), "00:05"), ?);
            ');
            $stm->execute([$u->member_id, $id, $u->member_id]);
=======
                INSERT INTO token (id, expire, member_id)
                VALUES (?, ADDTIME(NOW(), "00:05"), ?);
            ');

            $stm->execute([$u->memberID, $id, $u->memberID]);
>>>>>>> Stashed changes

            $url = base("user/token.php?id=$id");

            $m = get_mail();
<<<<<<< Updated upstream
            $m->addAddress($u->member_email, $u->member_name);
            
            if (!empty($u->photo)) {
                $m->addEmbeddedImage("../uploads/profiles/$u->member_profile_pic", 'photo');
=======
            $m->addAddress($u->memberEmail, $u->memberName);
            
            if (!empty($u->photo)) {
                $m->addEmbeddedImage("../photos/$u->photo", 'photo');
>>>>>>> Stashed changes
            }

            $m->isHTML(true);
            $m->Subject = 'Reset Password';
            $m->Body = "
                <img src='cid:photo'
                     style='width: 200px; height: 200px; border: 1px solid #333'>
<<<<<<< Updated upstream
                <p>Dear $u->member_name,</p>
=======
                <p>Dear $u->memberName,</p>
>>>>>>> Stashed changes
                <h1 style='color: red'>Reset Password</h1>
                <p>
                    Please click <a href='$url'>here</a> to reset your password.
                </p>
                <p>From, 😺 Admin</p>
            ";

            $m->send();
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
        } else {
            $_err['email'] = 'User not found';
        }
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Reset Password';
include '../_head.php';
?>

<<<<<<< Updated upstream
<div class="login-container">
=======
<style>
    form {
        margin-top: 200px;
    }
</style>

>>>>>>> Stashed changes
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
include '../_foot.php';
?>

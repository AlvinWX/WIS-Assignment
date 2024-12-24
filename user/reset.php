<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    if ($email == '') {
        $_err['email'] = 'Required';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } elseif (!is_exists($email, 'member', 'memberEmail')) {
        $_err['email'] = 'Email does not exist';
    }

    if (empty($_err)) {
        $stm = $_db->prepare('SELECT * FROM member WHERE memberEmail = ?');
        $stm->execute([$email]);
        $u = $stm->fetch(PDO::FETCH_OBJ);  // Fetch as object

        if ($u) {
            $id = sha1(uniqid() . rand());

            $stm = $_db->prepare('
                DELETE FROM token WHERE member_id = ?;
                INSERT INTO token (id, expire, member_id)
                VALUES (?, ADDTIME(NOW(), "00:05"), ?);
            ');

            $stm->execute([$u->memberID, $id, $u->memberID]);

            $url = base("user/token.php?id=$id");

            $m = get_mail();
            $m->addAddress($u->memberEmail, $u->memberName);
            
            if (!empty($u->photo)) {
                $m->addEmbeddedImage("../photos/$u->photo", 'photo');
            }

            $m->isHTML(true);
            $m->Subject = 'Reset Password';
            $m->Body = "
                <img src='cid:photo'
                     style='width: 200px; height: 200px; border: 1px solid #333'>
                <p>Dear $u->memberName,</p>
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

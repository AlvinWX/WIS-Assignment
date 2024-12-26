<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

$user = $_SESSION['user'] ?? null;
$admin_id = $user->admin_id;
if(empty($admin_id)){
    redirect('../../login.php');
    temp('info',"Unauthourized Access");
}

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM voucher WHERE voucher_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    extract((array)$s);
}

if (is_post()) {
    $id = req('id'); // <-- From URL
    $voucher_name = req('voucher_name');
    $voucher_desc = req('voucher_desc');

    // Validate name
    if ($voucher_name == '') {
        $_err['voucher_name'] = 'Required';
    } else if (strlen($voucher_name) > 100) {
        $_err['voucher_name'] = 'Maximum length 100';
    }

    // Validate desc
    if ($voucher_desc == '') {
        $_err['voucher_desc'] = 'Required';
    } else if (strlen($voucher_desc) > 1000) {
        $_err['voucher_desc'] = 'Maximum length 1000';
    }

    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE voucher
                            SET voucher_name = ?, voucher_desc = ?, voucher_last_update, admin_id
                            WHERE voucher_id = ?');
        $stm->execute([$voucher_name, $voucher_desc, , date("Y-m-d H:i:s"), $admin_id, $id]);

        temp('info', 'voucher updated');
        redirect('/page/yongqiaorou/voucher.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update';
include '../../_admin_head.php';
?>

<button data-get="/page/yongqiaorou/voucher.php"  class="back_button"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Back</button>

<form method="post" class="form">
    <label for="voucher_id">Id</label>
    <b><?= $id ?></b>
    <?= err('id') ?>

    <label for="voucher_name">Voucher Name</label>
    <?= html_text('voucher_name', 'maxlength="100"') ?>
    <?= err('voucher_name') ?>

    <label>Description</label>
    <?= html_text('voucher_desc', 'maxlength="1000"') ?>
    <?= err('voucher_desc') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_foot.php';
?>

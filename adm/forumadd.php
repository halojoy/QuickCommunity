<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['forum_name'])) {
    $forum_name = filter_var(trim($_POST['forum_name']), FILTER_SANITIZE_STRING);
    $forum_desc = filter_var(trim($_POST['forum_desc']), FILTER_SANITIZE_STRING);
    if (empty($forum_name)) {
        echo 'Forum name can not be empty<br><br>';
    } else {
        $sql = "INSERT INTO forums (f_name, f_desc) VALUES ('$forum_name', '$forum_desc')";
        $this->pdo->exec($sql);
        $last_id = $this->pdo->lastInsertId();
        $sql = "UPDATE forums SET f_order=$last_id WHERE fid=$last_id";
        $this->pdo->exec($sql);
        echo 'Forum was created<br><br>';
    }
}

//New forum form
?>
<span class="boldy">Add New Forum</span>
<br><br>
<form method="post" accept-charset="UTF-8">
    Forum Name:<br/>
    <input type="text" size="30" maxlength="25" required name="forum_name">
    <br/>
    Short Forum Description:<br/>
    <input type="text" size="40" maxlength="40" required name="forum_desc">
    <br/><br/>
    <input type="hidden" name="act" value="forumadd">
    <input type="submit" value="Submit">
</form>
<br>

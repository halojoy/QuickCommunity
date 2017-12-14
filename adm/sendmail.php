<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}
if (isset($_POST['usesmtp'])) {
    $usesmtp = $_POST['usesmtp'];
    $gmail = $_POST['googlemail'];
    $gpass = $_POST['googlepass'];
    $sql = "UPDATE settings SET setvalue='$usesmtp' WHERE setkey='usesmtp';";
    $this->pdo->exec($sql);
    $sql = "UPDATE settings SET setvalue='$gmail' WHERE setkey='googlemail';";
    $this->pdo->exec($sql);
    $sql = "UPDATE settings SET setvalue='$gpass' WHERE setkey='googlepass';";
    $this->pdo->exec($sql);
    header('location:./admin.php');
    exit();
}

$sql = "SELECT setvalue FROM settings WHERE setkey='usesmtp';";
$usmtp = $this->pdo->querySQL($sql)->fetchColumn();
$sql = "SELECT setvalue FROM settings WHERE setkey='googlemail';";
$gmail = $this->pdo->querySQL($sql)->fetchColumn();
$sql = "SELECT setvalue FROM settings WHERE setkey='googlepass';";
$gpass = $this->pdo->querySQL($sql)->fetchColumn();
?>
<form method="post">
    You want to have Register with SMTP Email Activation?
    <select name="usesmtp">
<?php
        if ($usmtp) {
?>
        <option value="1" selected>Yes</option>
        <option value="0">No</option>
<?php
        } else {
?>
        <option value="1">Yes</option>
        <option value="0" selected>No</option>
<?php
}
?>
    </select><br><br>
    Your google email account, needed for SMTP:<br>
    <input type="text" name="googlemail" size="40" value="<?php echo $gmail ?>"><br><br>
    Your gmail password:<br>
    <input type="text" name="googlepass" value="<?php echo $gpass ?>"><br><br>
    <input type="submit" value="SUBMIT">
    <input type="hidden" name="act" value="sendmail">
    <br><br>
</form>
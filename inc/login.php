<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

if (isset($_POST['filled'])) {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);
    $passhash = sha1($password);
    if (empty($username) || empty($password)) {
        header('location:./');
        exit();
    }
    $user = $this->pdo->nameCheck($username);
    $this->pdo = null;
    if (!$user || $passhash != $user->u_pass) {
        echo BADUSERPASS;
        exit();
    }

    $this->sess->userid   = $user->uid;
    $this->sess->username = $user->u_name;
    $this->sess->usertype = $user->u_type;
    $this->sess->reLogin();

    header('location:./');
    exit();
}
$this->pdo = null;
?>
<br>
<form method="post" accept-charset="UTF-8">
    <input type="text" name="username" size="32" maxlength="25" required>
    <label for="username"><?php echo USERNAME ?></label>
    <br>
    <input type="password" name="password" maxlength="16" required>
    <label for="password"><?php echo PASSWORD ?></label>
    <br><br>
    <input type="submit" value="<?php echo SUBMIT ?>">
    <input type="hidden" name="act" value="login">
    <input type="hidden" name="filled">
</form>
<br>


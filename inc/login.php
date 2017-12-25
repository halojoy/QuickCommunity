<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

require 'core/classVundoCSRF.php';

if (isset($_POST['filled'])) {
    if(!CSRF::check($_POST['_token'])){
        exit('Wrong Token!');
    }
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);
    if (empty($username) || empty($password)) {
        header('location:./');
        exit();
    }
    $user = $this->pdo->nameCheck($username);
    $this->pdo = null;
    if (!$user || !password_verify($password, $user->u_pass)) {
        echo BADUSERPASS;
        exit();
    }
    if ($user->u_type == 'activate') {
        echo 'You should Activate your account.<br>
        Look in your email inbox.';
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
    <input type="hidden" name="_token" value="<?php echo CSRF::generate() ?>">
</form>
<br>


<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

if (isset($_GET['ucode'])) {
    $ucode = $_GET['ucode'];
    $name = $this->pdo->nameActivate($ucode);
    if ($name) {
        $this->pdo->doActivate($ucode);
        exit('You are activated, '.$name);
    } else
        exit('Activation error');
}

require 'core/classVundoCSRF.php';

$error = $username = $email = '';
if (isset($_POST['filled'])) {
    if(!CSRF::check($_POST['_token'])){
        exit('Wrong Token!');
    }
    if ($_POST['capcode'] != $_SESSION['capcode']) {
        echo 'Wrong captcha code. Try again!';
        exit();
    }
    $username  = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password  = trim($_POST['password']);
    $password2 = trim($_POST['password2']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $email = strtolower($email);

    $error = $this->sess->validReg($username, $password, $password2, $email);
    if (!$error) {
        $this->sess->doRegister($username, $password, $email);
    }
}
$this->pdo = null;

require 'core/classCaptcha.php';
$cap = new Captcha();
$capcode = $cap->generatecode();
$_SESSION['capcode'] = $capcode;
?>
        <br>
        <span class="error"><?php echo $error; ?></span>
        <form method="post" accept-charset="UTF-8">
            <table style="border-collapse: collapse;">              
                <tr>
                    <td><label for="username"><?php echo USERNAME ?></label></td>
                    <td><input type="text" name="username" value="<?php echo $username ?>"
                               size="32" maxlength="25" required/>
                        <?php echo USER3TO25 ?></td>
                </tr>
                <tr>
                    <td><label for="password"><?php echo PASSWORD ?></label></td>
                    <td><input type="password" name="password" maxlength="16" required/>
                        <?php echo PASS6TO16 ?></td>
                </tr>
                <tr>
                    <td><label for="password2"><?php echo PASSWORD ?></label></td>
                    <td><input type="password" name="password2" maxlength="16" required/>
                        <?php echo CONFIRM ?></td>
                </tr>
                <tr>
                    <td><label for="email"><?php echo EMAIL ?></label></td>
                    <td><input type="text" name="email" value="<?php echo $email ?>"
                                size="45" maxlength="40" required/>
                        <?php echo NOTBESHOWN ?></td>
                </tr>
                <tr><td colspan="2">&nbsp;</tr>
                <tr><td colspan="2"><img src="<?php echo $cap->generateImage($capcode) ?>" title="Verification capcode"><br>
                <input type="text" size="16" name="capcode"> Fill in the characters</td></tr>
                <tr><td colspan="2">&nbsp;</tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="<?php echo SUBMIT ?>">
                    <input type="hidden" name="act" value="register">
                    <input type="hidden" name="filled"></td>
                    <input type="hidden" name="_token" value="<?php echo CSRF::generate() ?>">
                </tr>
            </table>
        </form>
        <br>

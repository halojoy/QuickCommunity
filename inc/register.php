<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

if (isset($_GET['ucode'])) {
    $ucode = $_GET['ucode'];

    $sql = "SELECT u_name FROM users WHERE u_code='$ucode';";
    $name = $this->pdo->query($sql)->fetchColumn();
    if ($name) {
        $sql = "UPDATE users SET u_type='member', u_code='0' WHERE u_code='$ucode';";
        $this->pdo->exec($sql);
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
    $passhash = password_hash($password, PASSWORD_BCRYPT);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $email = strtolower($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = ERROR1;
    } elseif (strlen($username) < 3) {
            $error = ERROR2;
    } elseif (strlen($password) < 6 || $password2 != $password) {
            $error = ERROR3;
    } else {
        $unameexists = $this->pdo->nameCheck($username);
        if ($unameexists) {
            $error = ERROR4;
        } else {
            $mailexists = $this->pdo->emailCheck($email);
            if ($mailexists) {
                $error = ERROR5;
            } else {
                // register
                $posts = 0;
                $ip = $_SERVER['REMOTE_ADDR'];
                $joined = $active = time();
                $sql = "SELECT setvalue FROM settings WHERE setkey='usesmtp';";
                $usesmtp = $this->pdo->query($sql)->fetchColumn();
                if (!$usesmtp)
                    $this->pdo->insertUser($username, $passhash, $email, 'member', '0', $posts, $ip, $joined, $active);
                // send smtp mail
                if ($usesmtp) {
                    $ucode = sha1($username);
                    $this->pdo->insertUser($username, $passhash, $email, 'activate', $ucode, $posts, $ip, $joined, $active);
                    echo 'You will get an Activation Email to activate your account.<br>';
                    require 'core/classSimpleMail.php';
                    $mail = new SimpleMail('smtp.gmail.com', 587, 'tls');
                    $sql = "SELECT setvalue FROM settings WHERE setkey='googlemail';";
                    $gmail = $this->pdo->query($sql)->fetchColumn();
                    $sql = "SELECT setvalue FROM settings WHERE setkey='googlepass';";
                    $gpass = $this->pdo->query($sql)->fetchColumn();
                    $mail->user = $gmail;
                    $mail->pass = $gpass;
                    $mail->from('noreply@hotmail.com', 'admin');
                    $mail->to($email, $username);
                    
                    $mail->subject = 'Your Activation';
                    $link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?act=register&ucode='.$ucode;
                    $mail->message = 'Click this link to activate your account:<br>
                    <a href="'.$link.'">Activate</a>';

                    if ($mail->send())
                        exit('Activation Mail was successfully sent.');
                    else
                        exit('Error: ' . $mail->error);
                }
                
                echo REGISTERDONE.' <span class="boldy">'.$username.'</span>';
                exit();
                }
            }
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

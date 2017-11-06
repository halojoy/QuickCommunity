<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

$error = $username = $email = '';
if (isset($_POST['filled'])) {

    $username  = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password  = trim($_POST['password']);
    $password2 = trim($_POST['password2']);
    $passhash = sha1($password);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
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
                $usertype = 'member';
                $posts = 0;
                $ip = $_SERVER['REMOTE_ADDR'];
                $joined = $active = time();
                $user_id = $this->pdo->insertUser($username, $passhash, $email, $usertype, $posts, $ip, $joined, $active);
                echo REGISTERDONE.' <span class="boldy">'.$username.'</span>';
                exit();
                }
            }
        }
}
$this->pdo = null;
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
                    <td><input type="text" name="email" value="<?php echo $email ?>
                                size="45" maxlength="40" required/>
                        <?php echo NOTBESHOWN ?></td>
                </tr>
                <tr><td colspan="2">&nbsp;</tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="<?php echo SUBMIT ?>">
                    <input type="hidden" name="act" value="register">
                    <input type="hidden" name="filled"></td>
                </tr>
            </table>
        </form>
        <br>


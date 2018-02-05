<?php if(!defined('QCOM1'))exit();

class Session
{
    public $pdo;
    public $userid   = '';
    public $username = '';
    public $usertype = ''; // 'admin', 'member', 'activate' or 'banned'
    public $logged = false;
    public $lifetime = 30*24*3600; // Login Cookie for one month
    public $usesmtp;    // If to use SMTP activation mail
    public $googlemail; // For SMTP Mail
    public $googlepass; // For SMTP Mail
    private $key;       // For Crypto
    private $iv;        // For Crypto

    public function __construct($pdo, $settings)
    {
        $this->pdo = $pdo;
        $this->usesmtp    = $settings->usesmtp;
        $this->googlemail = $settings->googlemail;
        $this->googlepass = $settings->googlepass;
        $this->key        = hex2bin($settings->cryptokey);
        $this->iv         = hex2bin($settings->cryptoiv);

        if (!isset($_COOKIE['myuserid']) && !isset($_SESSION['myuserid']))
            return;

        if (isset($_COOKIE['myuserid'])) {
            $this->userid = openssl_decrypt($_COOKIE['myuserid'], 'AES-256-CTR',
                    $this->key, 0, $this->iv);
        } else {
            $this->userid = $_SESSION['myuserid'];
        }

        if (!is_numeric($this->userid))
            $this->Logout();

        $user = $this->pdo->getUser($this->userid);
        if (!$user)
            $this->Logout();

        $this->username = $user->u_name;
        $this->usertype = $user->u_type;
        $this->reLogin();

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();
        $this->pdo->setLastTime($ip, $time, $this->userid);

    }

    public function submitLogin()
    {
        if ($this->isLogged()) {
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
            $this->loginControl($username, $password);
        }
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
<?php
    }

    public function loginControl($username, $password)
    {
        $user = $this->pdo->nameCheck($username);
        if (!$user || !password_verify($password, $user->u_pass)) {
            echo BADUSERPASS;
            exit();
        }
        if ($user->u_type == 'activate') {
            echo 'You should Activate your account.<br>
                Look in your email inbox.';
            exit();
        }

        // Login the user
        $this->userid   = $user->uid;
        $this->username = $user->u_name;
        $this->usertype = $user->u_type;
        $this->reLogin();

        header('location:./');
        exit();
    }

    public function reLogin()
    {
        $encoded = openssl_encrypt($this->userid, 'AES-256-CTR',
                $this->key, 0, $this->iv);
        setcookie('myuserid', $encoded, time()+$this->lifetime);
        $_SESSION['myuserid'] = $this->userid;
        $this->logged = true;
    }

    public function Logout()
    {
        setcookie('myuserid', '', time()-3600);
        $_SESSION['myuserid'] = null;
        $this->logged = false;
        header('location:./');
        exit();
    }

    public function isLogged()
    {
        return $this->logged;
    }

    public function isAdmin()
    {
        return $this->usertype == 'admin';
    }

    public function isBanned()
    {
        return $this->usertype == 'banned';
    }

    public function submitRegister()
    {
        if ($this->isLogged()) {
            header('location:./');
            exit();
        }
        if (isset($_GET['ucode'])) {
            $this->activate($_GET['ucode']);
        }
        $error = $username = $email = '';
        require 'core/classVundoCSRF.php';
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

            $error = $this->validReg($username, $password, $password2, $email);
            if (!$error) {
                $this->doRegister($username, $password, $email);
            }
        }

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
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
            <td colspan="2"><img src="<?php echo $cap->generateImage($capcode) ?>"
                title="Verification capcode"><br>
            <input type="text" size="16" name="capcode"> Fill in the characters</td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
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
<?php
    }

    public function validReg($uname, $pass1, $pass2, $email)
    {
        $error = false;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = ERROR1;
        } elseif (strlen($uname) < 3) {
            $error = ERROR2;
        } elseif (strlen($pass1) < 6 || $pass1 != $pass2) {
            $error = ERROR3;
        } else {
            $unameexists = $this->pdo->nameCheck($uname);
            if ($unameexists) {
                $error = ERROR4;
            } else {
                $mailexists = $this->pdo->emailCheck($email);
                if ($mailexists) {
                    $error = ERROR5;
                }
            }
        }
        return $error;
    }

    public function doRegister($uname, $pass, $email)
    {
        // Register
        $passhash = password_hash($pass, PASSWORD_BCRYPT);
        $posts = 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        $joined = $active = time();

        // Do we Send SMTP Activation Mail?
        $usesmtp = $this->usesmtp;
        if (!$usesmtp) {
            $this->pdo->insertUser($uname, $passhash, $email, 'member', '0',
                    $posts, $ip, $joined, $active);
            echo REGISTERDONE.' <span class="boldy">'.$uname.'</span>';
            exit();
        } else {
            // Send SMTP Activation Mail
            $ucode = uniqid();
            $this->pdo->insertUser($uname, $passhash, $email, 'activate', $ucode,
                    $posts, $ip, $joined, $active);
            echo 'You will get Activation Email to activate your account.<br>';
            require 'core/classSimpleMail.php';
            $mail = new SimpleMail('smtp.gmail.com', 587, 'tls');
            $mail->user = $this->googlemail;
            $mail->pass = $this->googlepass;
            $mail->from('noreply@hotmail.com', 'admin');
            $mail->to($email, $uname);

            $mail->subject = 'Your Activation';
            $link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].
                    '?act=register&ucode='.$ucode;
            $mail->message = 'Click this link to activate your account:<br>
            <a href="'.$link.'">Activate</a>';

            if ($mail->send())
                exit('Activation Mail was successfully sent.');
            else
                exit('Mail Error: ' . $mail->error);
        }
    }

    public function activate($ucode)
    {
        $name = $this->pdo->nameActivate($ucode);
        if ($name) {
            $this->pdo->doActivate($ucode);
            exit('You are activated, '.$name);
        } else
            exit('Activation error');
    }

}

<?php if(!defined('QCOM1'))exit() ?>
<?php

class Session
{
    public $db;
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

    public function __construct($db, $settings)
    {
        $this->db = $db;
        $this->usesmtp    = $settings->usesmtp;
        $this->googlemail = $settings->googlemail;
        $this->googlepass = $settings->googlepass;
        $this->key        = $settings->cryptokey;
        $this->iv         = $settings->cryptoiv;

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

        $user = $this->db->getUser($this->userid);
        if (!$user)
            $this->Logout();

        $this->username = $user->u_name;
        $this->usertype = $user->u_type;
        $this->reLogin();

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();
        $this->db->setLastTime($ip, $time, $this->userid);

    }

    public function loginControl($username, $password)
    {
        $user = $this->db->nameCheck($username);
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
            $unameexists = $this->db->nameCheck($uname);
            if ($unameexists) {
                $error = ERROR4;
            } else {
                $mailexists = $this->db->emailCheck($email);
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
            $this->db->insertUser($uname, $passhash, $email, 'member', '0',
                    $posts, $ip, $joined, $active);
            echo REGISTERDONE.' <span class="boldy">'.$uname.'</span>';
            exit();
        } else {
            // Send SMTP Activation Mail
            $ucode = uniqid();
            $this->db->insertUser($uname, $passhash, $email, 'activate', $ucode,
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
                exit('Error: ' . $mail->error);
        }

    }

    public function activate($ucode)
    {
        $name = $this->db->nameActivate($ucode);
        if ($name) {
            $this->db->doActivate($ucode);
            exit('You are activated, '.$name);
        } else
            exit('Activation error');
    }    

}

<?php if(!defined('QCOM1'))exit() ?>
<?php

class Session
{
    public $db;
    public $userid   = '';
    public $username = '';
    public $usertype = '';
    public $logged = false;
    public $lifetime = 3600*24*30; // Login Cookie for one month
    public $bantime  = 3600*24*365;// Ban for 1 year
    public $usesmtp;
    public $googlemail;
    public $googlepass;
    private $key;
    private $iv;

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

}

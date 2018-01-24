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
        $sql = "SELECT * FROM users WHERE uid=$this->userid";
        $stmt = $this->db->querySQL($sql);
        if (!$row = $stmt->fetch())
            $this->Logout();
        $this->username = $row->u_name;
        $this->usertype = $row->u_type;
        $this->reLogin();

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();
        $sql = "UPDATE users SET u_ip='$ip', u_active=$time WHERE uid=$this->userid;";
        $ret = $this->db->querySQL($sql);
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
        if ($this->logged)
            return true;
        else
            return false;
    }

    public function isAdmin()
    {
        if ($this->usertype == 'admin')
            return true;
        else
            return false;
    }
    
    public function isBanned()
    {
        if ($this->usertype == 'banned')
            return true;
        else
            return false;
    }

}

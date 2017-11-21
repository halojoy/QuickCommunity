<?php if(!defined('QCOM1'))exit() ?>
<?php

class Session
{
    public $db;
    public $logged;
    public $userid = '';
    public $username = '';
    public $usertype = '';
    public $banned = false;
    public $lifetime = 3600*24*30; // Login Cookie for one month
    public $bantime  = 3600*24*365;// Ban for 1 year
    private $secret = '1B852E5F70414C66';
    private $iv     = '9D252E50';

    public function __construct($db)
    {
        $this->db = $db;
        if (isset($_COOKIE['userdata'])) {
            if (empty($_COOKIE['userdata'])) {
                $this->Logout();
                return;
            }
            $cookiedata = openssl_decrypt($_COOKIE['userdata'], 'blowfish', $this->secret, 0, $this->iv);
            $userdata = explode('~', $cookiedata);
            if (count($userdata) != 3) {
                $this->Logout();
                return;
            }

            $this->userid   = $userdata[0];
            $this->username = $userdata[1];
            $this->usertype = $this->reCheck();
            $this->reLogin();

            $ip = $_SERVER['REMOTE_ADDR'];
            $time = time();
            $sql = "UPDATE users SET u_ip='$ip', u_active=$time WHERE uid=$this->userid;";
            $ret = $this->db->querySQL($sql);

        } else {
            $this->logged = false;
        }
    }
    
    public function reCheck()
    {
        $sql = "SELECT u_type FROM users WHERE uid=$this->userid LIMIT 1;";
        $u_type = $this->db->querySQL($sql)->fetchColumn();
        if ($u_type == 'banned')
            $this->banned = true;
        else
            $this->banned = false;

        return $u_type;
    }       
        
    public function reLogin()
    {
        $ustring = implode('~', array($this->userid, $this->username, $this->usertype));
        $encoded = openssl_encrypt($ustring, 'blowfish', $this->secret, 0, $this->iv);
        setcookie('userdata', $encoded, time()+$this->lifetime);
        $this->logged = true;
    }

    public function Logout()
    {
        setcookie('userdata', '', time()-10);
        $this->logged = false;
    }
    
    public function isBanned()
    {
        if ($this->banned)
            return true;
        else
            return false;
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
}

?>

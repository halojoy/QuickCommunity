<?php if(!defined('QCOM1'))exit() ?>
<?php

class Database extends PDO
{
    public $dbdriver;
    public $lastInsertId;
    public $rowCount;

    public function __construct($dsn, $dbuser = '', $dbpass = '')
    {
        try{
            parent::__construct($dsn, $dbuser, $dbpass);
        } catch (PDOException $e) {
            echo 'Database Connection Error: '.$e->getMessage().'<br>';
        }
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
        $this->dbdriver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
    
    public function querySQL($sql)
    {
        $this->lastInsertId = 0;
        $sth = $this->prepare($sql);
        $sth->execute();
        if($sth === false) {
            echo print_r($this->errorInfo()).'<br>';
            echo $sql.'<br><br>';
            exit();
        }
        $this->lastInsertId = $this->lastInsertId();
        
        if(strpos(strtoupper($sql), 'SELECT') !== false) {
            $this->rowCount = 0;
            $res = $this->query($sql);       
            $i=0; foreach($res as $dum) $i++;
            $this->rowCount = $i;
        }
        
        return $sth;
    }

    public function insertUser($uname, $upass, $umail, $utype, $ucode, $posts, $ip, $join, $active)
    {
        $sql = "INSERT INTO users VALUES (null, '$uname', '$upass', '$umail', '$utype', '$ucode', $posts, '$ip', $join, $active)";
        $ret = $this->querySQL($sql);
        return $this->lastInsertId;
    }
        
    public function ipCheck($ip)
    {
        $sql = "SELECT u_type FROM users WHERE u_ip='$ip' LIMIT 1";
        $ret = $this->querySQL($sql);
        
        return $ret->fetchColumn();
    }

    public function nameCheck($uname)
    {
        $uname = strtolower($uname);
        $sql = "SELECT * FROM users WHERE lower(u_name)='$uname' LIMIT 1";
        $ret = $this->querySQL($sql);
        
        return $ret->fetch();   
    }

    public function emailCheck($email)
    {
        $sql = "SELECT u_mail FROM users WHERE u_mail='$email' LIMIT 1";
        $ret = $this->querySQL($sql);
        
        return $ret->fetchColumn();
    }

    
}
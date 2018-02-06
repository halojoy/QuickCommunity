<?php if(!defined('QCOM1'))exit();

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
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    public function insertUser($uname, $upass, $umail,
                $utype, $ucode, $posts, $ip, $join, $active)
    {
        $sql = "INSERT INTO users
        (u_name, u_pass, u_mail, u_type, u_code, u_posts, u_ip, u_joined, u_active)
        VALUES
        ('$uname', '$upass', '$umail', '$utype', '$ucode', $posts, '$ip', $join, $active)";
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
        $sql = "SELECT * FROM users WHERE u_name='$uname' LIMIT 1";
        $ret = $this->querySQL($sql);

        return $ret->fetch();   
    }

    public function emailCheck($email)
    {
        $sql = "SELECT u_mail FROM users WHERE u_mail='$email' LIMIT 1";
        $ret = $this->querySQL($sql);

        return $ret->fetchColumn();
    }

    public function getForums()
    {
        $sql = "SELECT * FROM forums ORDER BY f_order";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function lastPosting($forumid)
    {
        $sql = "SELECT t_lastpuname, t_lastptime FROM topics
                WHERE t_fid=$forumid ORDER BY t_lastptime DESC LIMIT 1";
        $ret = $this->querySQL($sql);

        return $ret->fetch();
    }

    public function getMembers()
    {
        $sql = "SELECT * FROM users ORDER BY u_type, lower(u_name)";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function getPosts($topicid)
    {
        $sql = "SELECT * FROM posts WHERE p_tid=$topicid";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function getTopics($forumid)
    {
        $sql = "SELECT * FROM topics WHERE t_fid=$forumid
                ORDER BY t_lastptime DESC LIMIT 20";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function getSticky($forumid)
    {
        $sql = "SELECT * FROM topics WHERE t_fid=$forumid
                AND t_sticky='1'";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function notSticky($forumid)
    {
        $sql = "SELECT * FROM topics WHERE t_fid=$forumid AND t_sticky='0'
                ORDER BY t_lastptime DESC LIMIT 20";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function nameActivate($ucode)
    {
        $sql = "SELECT u_name FROM users WHERE u_code='$ucode'";
        $ret = $this->query($sql);

        return $ret->fetchColumn();
    }

    public function doActivate($ucode)
    {
        $sql = "UPDATE users SET u_type='member', u_code='0'
                WHERE u_code='$ucode'";
        $ret = $this->querySQL($sql);

        return;
    }

    public function getUser($userid)
    {
        $sql = "SELECT * FROM users WHERE uid=$userid";
        $ret = $this->querySQL($sql);

        return $ret->fetch();
    }

    public function setLastTime($ip, $time, $userid)
    {
        $sql = "UPDATE users SET u_ip='$ip', u_active=$time
                WHERE uid=$userid";
        $ret = $this->querySQL($sql);

        return;
    }

    public function forumName($forumid)
    {
        $sql = "SELECT f_name FROM forums WHERE fid=$forumid";
        $ret = $this->querySQL($sql);

        return $ret->fetchColumn();
    }

    public function getTopic($topicid)
    {
        $sql = "SELECT * FROM topics WHERE tid=$topicid";
        $ret = $this->querySQL($sql);

        return $ret->fetch();
    }

    public function getPost($postid)
    {
        $sql = "SELECT * FROM posts WHERE pid=$postid";
        $ret = $this->querySQL($sql);

        return $ret->fetch();
    }

    public function addPost($fid, $fname, $tid, $tsubj, $mess,
                            $uid, $uname, $time, $ip)
    {
        $sql = "INSERT INTO posts (p_fid, p_fname, p_tid, p_tsubj, p_message,
                p_uid, p_uname, p_time, p_ip)
            VALUES ($fid, '$fname', $tid, '$tsubj', '$mess',
                $uid, '$uname', $time, '$ip')";
        $ret = $this->querySQL($sql);

        return $this->lastInsertId;
    }

    public function addPostUpTopic($pid, $uid, $uname, $time, $tid)
    {
        $sql = "UPDATE topics SET t_lastpid=$pid, t_lastpuid=$uid,
                t_lastpuname='$uname', t_lastptime=$time WHERE tid=$tid";
        $ret = $this->querySQL($sql);

        return;
    }

    public function addPostUpUser($uid)
    {
        $sql = "UPDATE users SET u_posts=u_posts+1 WHERE uid=$uid;";
        $ret = $this->querySQL($sql);

        return;
    }

    public function addPostFile($newname, $filecat, $pid)
    {
        $sql = "UPDATE posts SET p_file='$newname', p_cat='$filecat'
                WHERE pid=$pid";
        $ret = $this->querySQL($sql);

        return;
    }

    public function editPostUp($message, $pid)
    {
        $sql = "UPDATE posts SET p_message='$message' WHERE pid=$pid";
        $ret = $this->querySQL($sql);

        return;
    }

    public function addTopic($fid, $fname, $subj, $uid, $uname, $time)
    {
        $sql = "INSERT INTO topics (t_fid, t_fname, t_subject, t_uid, t_uname,
                t_time, t_lastpuid, t_lastpuname, t_lastptime, t_sticky)
            VALUES ($fid, '$fname', '$subj', $uid, '$uname',
                $time, $uid, '$uname', $time, '0')";
        $ret = $this->querySQL($sql);

        return $this->lastInsertId;
    }

    public function addTopicPost($fid, $fname, $tid, $subj, $message,
                                $uid, $uname, $time, $ip)
    {
        $sql = "INSERT INTO posts (p_fid, p_fname, p_tid, p_tsubj, p_message,
                p_uid, p_uname, p_time, p_ip)
            VALUES ($fid, '$fname', $tid, '$subj', '$message',
                $uid, '$uname', $time, '$ip')";
        $ret = $this->querySQL($sql);

        return $this->lastInsertId;
    }

    public function addTopicUpTopic($pid, $tid)
    {
        $sql = "UPDATE topics SET t_lastpid=$pid WHERE tid=$tid";
        $ret = $this->querySQL($sql);

        return;
    }

    public function getTopicsNew($timelimit)
    {
        $sql = "SELECT tid, t_fid, t_subject, t_lastpuname, t_lastptime
        FROM topics 
        WHERE t_lastptime>$timelimit ORDER BY t_lastptime DESC LIMIT 12";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }

    public function getSettings()
    {
        $sql = "SELECT setkey, setvalue FROM settings";
        $ret = $this->querySQL($sql);

        return $ret->fetchAll();
    }    

}

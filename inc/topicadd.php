<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

if(isset($_POST['filled'])) {

    $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
    $message = str_replace(' ', '&nbsp;', $message);
    $time = time();
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if(!empty($subject) && !empty($message)) {
        $sql = "INSERT INTO topics (t_fid, t_fname, t_subject, t_uid, t_uname, t_time, t_lastpuid, t_lastpuname, t_lastptime)
            VALUES ($this->fid, '$this->fname', '$subject', {$this->sess->userid}, '{$this->sess->username}', $time,
                {$this->sess->userid}, '{$this->sess->username}', $time);";
        $ret = $this->pdo->querySQL($sql);
        $tid = $this->pdo->lastInsertId;
        
        $sql = "INSERT INTO posts (p_fid, p_fname, p_tid, p_tsubj, p_message, p_uid, p_uname, p_time, p_ip)
            VALUES ($this->fid, '$this->fname', $tid, '$subject', '$message', {$this->sess->userid}, '{$this->sess->username}', $time, '$ip');";
        $ret = $this->pdo->querySQL($sql);;
        $pid = $this->pdo->lastInsertId;

        $sql = "UPDATE topics SET t_lastpid=$pid WHERE tid=$tid";
        $ret = $this->pdo->querySQL($sql);
        
        $sql = "UPDATE users SET u_posts=u_posts+1 WHERE uid={$this->sess->userid};";
        $ret = $this->pdo->querySQL($sql);

        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {

            require 'core/classUploadFile.php';
            $file = $_FILES['image'];
            $image = new UploadFile($file); 

            preg_match("@(\..+)$@", $file['name'], $match);
            $ext = strtolower($match[1]);
            $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
            $genname=''; for ($i=0;$i<8;$i++) $genname[$i]=$chars[mt_rand(0,35)];
            $imagename = $pid.'_'.$genname.$ext;
            
            $image->setName($imagename);
            $image->upload();
            $image->setMaxSize(840, 680);
            $image->resize();

            $sql = "UPDATE posts SET p_image='$imagename' WHERE pid=$pid;";
            $ret = $this->pdo->querySQL($sql);
        }

        $this->pdo = null;
        header('location:./?act=posts&tid='.$tid);
        exit();
    }
}
$this->pdo = null;

?>
<br>
<span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $this->fname ?></span>
<br>
<form enctype="multipart/form-data" method="post" accept-charset="UTF-8">
    <?php echo SUBJECT ?>:<br/>
    <input type="text" size="70" maxlength="60" required name="subject"><br/>
    <?php echo MESSAGE ?>:<br/>
    <textarea rows="10" cols="100" maxlength="2048" required name="message"></textarea>
    <br>
    <label for="profile_pic"><?php echo CHOOSEIMAGE ?></label>
    <input type="file" size="32" name="image">
    <br><br>
    <input type="submit" value="<?php echo SUBMIT ?>">
    <input type="hidden" name="act" value="topicadd">
    <input type="hidden" name="fid" value="<?php echo $this->fid ?>">
    <input type="hidden" name="filled">
</form>
<br>


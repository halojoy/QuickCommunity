<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

if(isset($_POST['filled'])) {

    $message = nl2br(filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING));
    $time = time();
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if(!empty($message)) {
        $sql = "INSERT INTO posts (p_fid, p_fname, p_tid, p_tsubj, p_message, p_uid, p_uname, p_time, p_ip)
            VALUES ($this->fid, '$this->fname', $this->tid, '$this->tsubj', '$message', {$this->sess->userid}, '{$this->sess->username}', $time, '$ip');";
        $ret = $this->pdo->querySQL($sql);
        $pid = $this->pdo->lastInsertId;
        
        $sql = "UPDATE topics SET t_lastpid=$pid, t_lastpuid={$this->sess->userid}, t_lastpuname='{$this->sess->username}', t_lastptime=$time
            WHERE tid=$this->tid;";
        $ret = $this->pdo->querySQL($sql);
        
        $sql = "UPDATE users SET u_posts=u_posts+1 WHERE uid={$this->sess->userid};";
        $ret = $this->pdo->querySQL($sql);
        
        $this->pdo = null;

        header('location:./?act=posts&fid='.$this->fid.'&tid='.$this->tid);
        exit();
    }
}
$this->pdo = null;

?>
<br>
<span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $this->tsubj ?></span>
<br>
<form method="post" accept-charset="UTF-8">
    <?php echo MESSAGE ?>:<br/>
    <textarea rows="10" cols="100" maxlength="2048" required name="message"></textarea>
    <br><br>
    <input type="submit" value="<?php echo SUBMIT ?>">
    <input type="hidden" name="act" value="postadd">
    <input type="hidden" name="tid" value="<?php echo $this->tid ?>">
    <input type="hidden" name="filled">
</form>
<br>


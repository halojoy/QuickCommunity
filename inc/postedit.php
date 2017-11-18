<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

if(isset($_POST['filled'])) {

    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
    $time = time();

    if(!empty($message)) {

        $sql = "UPDATE posts SET p_message='$message' WHERE pid=$this->pid;";
        $ret = $this->pdo->querySQL($sql);
        $this->pdo = null;

        header('location:./?act=post&pid='.$this->pid);
        exit();
    }
}

$sql = "SELECT * FROM posts WHERE pid=$this->pid;";
$post = $this->pdo->querySQL($sql)->fetch();
$this->pdo = null;

if ($this->sess->userid != $post->p_uid) {
    header('location:./');
    exit();
}

$message = $post->p_message;

?>
<br>
<span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $this->tsubj ?></span>
<br>
<form method="post" accept-charset="UTF-8">
    <?php echo MESSAGE ?>:<br/>
    <textarea rows="10" cols="100" maxlength="2048" required name="message"><?php echo $message ?></textarea>
    <br><br>
    <input type="submit" value="<?php echo SUBMIT ?>">
    <input type="hidden" name="act" value="postedit">
    <input type="hidden" name="pid" value="<?php echo $this->pid ?>">
    <input type="hidden" name="filled">
</form>
<br>


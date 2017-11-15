<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

if(isset($_POST['filled'])) {

    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
    $message = str_replace(' ', '&nbsp;', $message);
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

        if ($_FILES['upfile']['error'] == UPLOAD_ERR_OK) {
  
            require 'core/classUploadFile.php';
            $upload = new UploadFile();
            $upload->registerFile($_FILES['upfile']);
            $filecat = $upload->fileCategory;

            if ($filecat == 'other') {
                $filename = $pid.'_'.$upload->name;
                $upload->setName($filename);
                $upload->upload();
            } else {
                $genname=''; for ($i=0;$i<8;$i++) $genname[$i]=chr(mt_rand(97,122));
                $ext = $upload->extension;
                $filename  = $pid.'_'.$genname.'.'.$ext;
                $upload->setName($filename);
                $upload->upload();
                if ($filecat == 'image1') {
                    $upload->setName('tmb_'.$filename);
                    $upload->setMaxSize(175, 175);
                    $upload->resize();
                }
            }
            $sql = "UPDATE posts SET p_file='$filename', p_cat='$filecat' WHERE pid=$pid;";
            $ret = $this->pdo->querySQL($sql);
        } elseif ($_FILES['upfile']['error'] != 4) {
            exit('File Upload Error: '.$_FILES['upfile']['error'].
            '&nbsp;&nbsp;<a href="http://php.net/manual/en/features.file-upload.errors.php" target="_blank">Information</a>');
        }

        $this->pdo = null;       
        header('location:./?act=post&pid='.$pid);
        exit();
    }
}
$this->pdo = null;

require 'core/classUploadFile.php';
$upload = new UploadFile();
$maxsize = $upload->maxFileSize * 1048576;
?>
<br>
<span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $this->tsubj ?></span>
<br>
<form enctype="multipart/form-data" method="post" accept-charset="UTF-8">
    <?php echo MESSAGE ?>:<br/>
    <textarea rows="10" cols="100" maxlength="2048" required name="message"></textarea>
    <br>
    <?php echo CHOOSEFILE ?>

    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxsize ?>">
    <input type="file" size="32" name="upfile">
    <br><br>
    <input type="submit" value="<?php echo SUBMIT ?>">
    <input type="hidden" name="act" value="postadd">
    <input type="hidden" name="tid" value="<?php echo $this->tid ?>">
    <input type="hidden" name="filled">
</form>
<br>


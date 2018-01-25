<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

require 'core/classVundoCSRF.php';

if(isset($_POST['filled'])) {
    if(!CSRF::check($_POST['_token'])){
        exit('Wrong Token!');
    }
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
    $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    $message = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $message);

    $fid   = $this->fid;
    $fname = $this->fname;
    $tid   = $this->tid;
    $tsubj = $this->tsubj;
    $mess  = $message;
    $uid   = $this->sess->userid;
    $uname = $this->sess->username;
    $time  = time();
    $ip    = $_SERVER['REMOTE_ADDR'];

    if(!empty($message)) {

        $pid = $this->pdo->addPost($fid, $fname, $tid, $tsubj, $mess,
                                    $uid, $uname, $time, $ip);

        $this->pdo->addPostUpTopic($pid, $uid, $uname, $time, $tid);

        $this->pdo->addPostUpUser($uid);

        if ($_FILES['upfile']['error'] == UPLOAD_ERR_OK) {

            require 'core/classUploadFile.php';
            $upload = new UploadFile();
            $upload->registerFile($_FILES['upfile']);
            $filecat = $upload->fileCategory;

            if ($filecat == 'file') {
                $newname = $pid.'_'.$upload->name;
                $upload->setName($newname);
                $upload->upload();
            }
            if ($filecat == 'image') {
                $genname=''; for ($i=0;$i<8;$i++) $genname[$i]=chr(mt_rand(97,122));
                $ext = $upload->extension;
                $newname  = $pid.'_'.$genname.'.'.$ext;
                $upload->setName($newname);
                $upload->upload();
                $upload->setName('tmb_'.$newname);
                $upload->resize();
            }
            $this->pdo->addPostFile($newname, $filecat, $pid);

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
    <input type="hidden" name="_token" value="<?php echo CSRF::generate() ?>">
</form>
<br>


<?php if(!defined('QCOM1'))exit();

class Forum
{
    public $pdo;
    public $sess;
    public $dateform;
    public $datetime;

    public function __construct($pdo, $sess, $settings)
    {
        $this->pdo  = $pdo;
        $this->sess = $sess;
        $this->dateform = $settings->dateform;
        $this->datetime = $settings->datetime;
    }

    public function forums()
    {
?>
        <br>
        <div id="bodycore">
        <table id="forums">
<?php

        $forums = $this->pdo->getForums();
        foreach($forums as $row) {
            $row2 = $this->pdo->lastPosting($row->fid);
?>
            <tr><td class="forumtop" colspan="4"></td></tr>
            <tr class="frame">
            <td class="forumleft">          
                <form class="link" method="post">
                <input class="link left" type="submit"
                    value="<?php echo $row->f_name ?>">
                <input type="hidden" name="act" value="topics">
                <input type="hidden" name="fid" value="<?php echo $row->fid ?>">
                </form>
            </td>
            <td class="forummiddle"><?php echo $row->f_desc ?></td>
            <td class="forumright">
            <?php if ($row2 !== false)
                echo LASTPOSTBY.' <span class="boldy">'.
                    $row2->t_lastpuname.'</span>' ?>
            </td>
            <td class="forumfarright">
            <?php if ($row2 !== false)
                echo utf8_encode(strftime($this->datetime,
                    $row2->t_lastptime)) ?>
            </td>
            </tr>
            <tr><td class="spacer" colspan="4"></td></tr>
<?php
}
?>
        </table>
        </div>
<?php
    }

    public function topics($forumid)
    {

        if($this->sess->isLogged() && !$this->sess->isBanned()) {
?>
        <br>
        <div class="clearleft"></div>
        <div id="postlink">
        <form class="link" method="post">
            <input class="link" type="submit" value="<?php echo POSTTOPIC ?>">
            <input type="hidden" name="act" value="topicadd">
            <input type="hidden" name="fid" value="<?php echo $forumid ?>">
        </form>
        </div>
        <br>
<?php
}
?>
        <br>
        <table id="topics"> 
            <tr><td class="topicstop" colspan="4"></td></tr>
<?php

        $topics = $this->pdo->getTopics($forumid);

        foreach($topics as $row) {
?>
            <tr>
            <td class="topicleft">
            <form class="link" method="post">
                    <input class="link left" type="submit" value="<?php echo $row->t_subject ?>">
                    <input type="hidden" name="act" value="posts">
                    <input type="hidden" name="tid" value="<?php echo $row->tid ?>">
            </form>
            </td>
            <td class="topicmiddle1"><?php echo STARTEDBY ?> <span class="boldy"><?php echo $row->t_uname ?></span></td>
            <td class="topicmiddle2"><?php echo LASTPOSTBY ?> <span class="boldy"><?php echo $row->t_lastpuname ?></span></td>
            <td class="topicright"><?php echo utf8_encode(strftime($this->datetime, $row->t_lastptime)) ?></td>
            </tr>
<?php
}
?>
        </table>
        <div id="topicsspacer"></div>

<?php
    }

    public function posts($tid, $tsubj)
    {

        if ($this->sess->isLogged() && !$this->sess->isBanned()) {
?>
        <br>
        <div id="postlink">
        <form class="link" method="post">
            <input class="link" type="submit" value="<?php echo POSTREPLY ?>">
            <input type="hidden" name="act" value="postadd">
            <input type="hidden" name="tid" value="<?php echo $tid ?>">
        </form>
        </div>
        <br>

<?php
}
?>
        <span class="leftspace">&nbsp;</span>
        <span class="boldy"><?php echo $tsubj ?></span>
        <div class="postupright"><a href="?act=posts&tid=<?php echo $tid ?>"><?php echo TOPICLINK ?></a>&nbsp;</div>

        <table id="posts">
<?php

        $posts = $this->pdo->getPosts($tid);

        foreach($posts as $row) {
?>
            <tr class="frame"><td class="posttop" colspan="2"></td></tr>
            <tr class="frame">
                <td class="postleft">
                    <?php echo utf8_encode(strftime($this->datetime, $row->p_time)) ?><br>
                    <?php echo $row->p_uname ?>
                </td>
                <td style="" class="postright">
                    <div class="postupright"><a href="?act=post&pid=<?php echo $row->pid ?>"><?php echo LINK ?></a></div>
<?php
            if ($this->sess->userid == $row->p_uid && !$this->sess->isBanned()) {
?>
                    <div class="postupright">
                        <form class="link" method="post">
                            <input class="link" type="submit" value="<?php echo EDIT ?>">
                            <input type="hidden" name="act" value="postedit">
                            <input type="hidden" name="pid" value="<?php echo $row->pid ?>">
                        </form>
                    </div>
<?php
            }
                    if ($row->p_cat == 'image') {
?>
                        <div class="thumb">
                        <a href="upload/<?php echo $row->p_file ?>" target="_blank">
                        <img src="upload/<?php echo 'tmb_'.$row->p_file ?>">
                        </a></div>
<?php
                    }
?>
                    <div class="message"><?php echo nl2br($row->p_message) ?></div>
<?php
                    if ($row->p_cat == 'file') {
                        echo '<br>'.ATTACHMENT.
                        ' <a href="upload/'.$row->p_file.'" target="_blank">'.$row->p_file.'</a>';
                        echo '&nbsp;&nbsp;'.RIGHTCLICK;
                    }
?>
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>
<?php
}
?>
        </table>
<?php

if ($this->sess->isLogged() && !$this->sess->isBanned()) {
?>
        <br>
        <div id="postlink">
        <form class="link" method="post">
            <input class="link" type="submit" value="<?php echo POSTREPLY ?>">
            <input type="hidden" name="act" value="postadd">
            <input type="hidden" name="tid" value="<?php echo $tid ?>">
        </form>
        </div>
        <br>
<?php
}

    }

    public function post($pid, $tsubj)
    {
?>
        <br>
        <span class="leftspace">&nbsp;</span>
        <span class="boldy"><?php echo $tsubj ?></span>

        <table id="posts">
<?php
        $row = $this->pdo->getPost($pid);
?>
            <tr class="frame"><td class="posttop" colspan="2"></td></tr>
            <tr class="frame">
                <td class="postleft">
                    <?php echo utf8_encode(strftime($this->datetime, $row->p_time)) ?><br>
                    <?php echo $row->p_uname ?>
                </td>
                <td class="postright">
                    <div class="postupright"><a href="?act=post&pid=<?php echo $row->pid ?>"><?php echo LINK ?></a></div>
<?php
            if ($this->sess->userid == $row->p_uid && !$this->sess->isBanned()) {
?>
                    <div class="postupright">
                        <form class="link" method="post">
                            <input class="link" type="submit" value="<?php echo EDIT ?>">
                            <input type="hidden" name="act" value="postedit">
                            <input type="hidden" name="pid" value="<?php echo $row->pid ?>">
                        </form>
                    </div>
<?php
            }
                    if ($row->p_cat == 'image') {
?>
                        <div class="thumb">
                        <a href="upload/<?php echo $row->p_file ?>" target="_blank">
                        <img src="upload/<?php echo 'tmb_'.$row->p_file ?>">
                        </a></div>
<?php
                    }
?>
                    <div class="message"><?php echo nl2br($row->p_message) ?></div>
<?php
                    if ($row->p_cat == 'file') {
                        echo '<br>'.ATTACHMENT.
                        ' <a href="upload/'.$row->p_file.'" target="_blank">'.$row->p_file.'</a>';
                        echo '&nbsp;&nbsp;'.RIGHTCLICK;
                    }
?>
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>
        </table>
<?php
    }

    public function topicadd($fid, $fname)
    {
        if (!$this->sess->isLogged() || $this->sess->isBanned()) {
            header('location:./');
            exit();
        }
        require 'core/classVundoCSRF.php';
        if(isset($_POST['filled'])) {
            if(!CSRF::check($_POST['_token'])){
                exit('Wrong Token!');
            }
            $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
            $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
            $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
            $message = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $message);
            $time = time();
            $ip   = $_SERVER['REMOTE_ADDR'];
            $subj  = $subject;
            $uid   = $this->sess->userid;
            $uname = $this->sess->username;

            if(!empty($subject) && !empty($message)) {

                $tid = $this->pdo->addTopic($fid, $fname, $subj, $uid, $uname, $time);

                $pid = $this->pdo->addTopicPost($fid, $fname, $tid, $subj, $message,
                                                $uid, $uname, $time, $ip);

                $this->pdo->addTopicUpTopic($pid, $tid);

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
                    exit('File Upload Error: '.$_FILES['upfile']['error'].'&nbsp;&nbsp;
                    <a href="http://php.net/manual/en/features.file-upload.errors.php"
                    target="_blank">Information</a>');
                }

                header('location:./?act=posts&tid='.$tid);
                exit();
            }
        }

        require 'core/classUploadFile.php';
        $upload = new UploadFile();
        $maxsize = $upload->maxFileSize * 1048576;
        ?>
        <br>
        <span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $fname ?></span>
        <br>
        <form enctype="multipart/form-data" method="post" accept-charset="UTF-8">
            <?php echo SUBJECT ?>:<br/>
            <input type="text" size="60" maxlength="56" required name="subject"><br/>
            <?php echo MESSAGE ?>:<br/>
            <textarea rows="10" cols="100" maxlength="2048" required name="message"></textarea>
            <br>
            <?php echo CHOOSEFILE ?>

            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxsize ?>">
            <input type="file" size="32" name="upfile">
            <br><br>
            <input type="submit" value="<?php echo SUBMIT ?>">
            <input type="hidden" name="act" value="topicadd">
            <input type="hidden" name="fid" value="<?php echo $fid ?>">
            <input type="hidden" name="filled">
            <input type="hidden" name="_token" value="<?php echo CSRF::generate() ?>">
        </form>
        <br>
<?php
    }

    public function postadd($fid, $fname, $tid, $tsubj)
    {
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
                '&nbsp;&nbsp;
                <a href="http://php.net/manual/en/features.file-upload.errors.php"
                target="_blank">Information</a>');
                }
     
                header('location:./?act=post&pid='.$pid);
                exit();
            }
        }

        require 'core/classUploadFile.php';
        $upload = new UploadFile();
        $maxsize = $upload->maxFileSize * 1048576;
        ?>
        <br>
        <span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $tsubj ?></span>
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
            <input type="hidden" name="tid" value="<?php echo $tid ?>">
            <input type="hidden" name="filled">
            <input type="hidden" name="_token" value="<?php echo CSRF::generate() ?>">
        </form>
        <br>
<?php
    }

    public function postedit($pid, $tsubj)
    {
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
            $time = time();
            if(!empty($message)) {
                $this->pdo->editPostUp($message, $pid);
                header('location:./?act=post&pid='.$pid);
                exit();
            }
        }
        $post = $this->pdo->getPost($pid);
        if ($this->sess->userid != $post->p_uid) {
            header('location:./');
            exit();
        }
        $message = $post->p_message;
        $message = strip_tags($message);
?>
        <br>
        <span class="leftspace">&nbsp;</span><span class="boldy"><?php echo $tsubj ?></span>
        <br>
        <form method="post" accept-charset="UTF-8">
            <?php echo MESSAGE ?>:<br/>
            <textarea rows="10" cols="100" maxlength="2048" required
                name="message"><?php echo $message ?></textarea>
            <br><br>
            <input type="submit" value="<?php echo SUBMIT ?>">
            <input type="hidden" name="act" value="postedit">
            <input type="hidden" name="pid" value="<?php echo $pid ?>">
            <input type="hidden" name="filled">
            <input type="hidden" name="_token" value="<?php echo CSRF::generate() ?>">
        </form>
        <br>
<?php
    }

    public function topicsnew()
    {
        if (!$this->sess->isLogged() || $this->sess->isBanned()) {
            header('location:./');
            exit();
        }
?>
        <br>
        <table id="topicsnew">
            <tr><td class="tnewtop" colspan="4"></td></tr>
<?php
        $timelimit = time()-8*24*3600;// Last 8 days
        $topics = $this->pdo->getTopicsNew($timelimit);
        foreach($topics as $row) {
            $forumname = $this->pdo->forumName($row->t_fid);
?>
            <tr>
            <td class="tnewleft">
            <?php echo utf8_encode(strftime($this->datetime,
                $row->t_lastptime)) ?></td>
            <td class="tnewbody">
            <form class="link" method="post">
                <input class="link left" type="submit"
                    value="<?php echo $row->t_subject ?>">
                <input type="hidden" name="act" value="posts">
                <input type="hidden" name="tid" value="<?php echo $row->tid ?>">
            </form>
            </td>
            <td class="tnewright1"><span class="boldy">
                <?php echo $forumname ?></span></td>
            <td class="tnewright2"><?php echo LASTPOSTBY ?> <span class="boldy">
                <?php echo $row->t_lastpuname ?></span></td>        
            </tr>
<?php
}
?>
        </table>
        <div id="topicsspacer"></div>
<?php
    }

    public function showMembers()
    {
        if (!$this->sess->isLogged() || $this->sess->isBanned()) {
            header('location:./');
            exit();
        }
?>
        <br>
        <div id="memberstop">
            <span class="boldy"><?php echo MEMBERS ?></span>
        </div>
        <table id="members">
            <tr id="memtop"><th><?php echo JOINED ?></th>
            <th><?php echo NAME ?></th>
            <th><?php echo USERTYPE ?></th><th><?php echo POSTS ?></th>
            <th><?php echo LASTACTIVE ?></th></tr>
<?php
        $members = $this->pdo->getMembers();
        foreach($members as $row) {
?>
            <tr><td><?php echo utf8_encode(strftime($this->dateform,
                $row->u_joined)) ?></td>
            <td><span class="boldy"><?php echo $row->u_name ?></span></td>
            <td><?php echo $row->u_type ?></td>
            <td><?php echo $row->u_posts ?></td><td>
            <?php echo utf8_encode(strftime($this->dateform,
                $row->u_active)) ?></td></tr>
<?php
        }
?>
        </table>
<?php
    }

}

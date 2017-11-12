<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['delpid'])) {

    $pid = $_POST['delpid'];
    $tid = $_POST['tid'];
    $_POST = array();

    $this->pdo->exec("DELETE FROM posts WHERE pid=".$pid);
    
    $sql = "SELECT pid, p_uid, p_uname, p_time FROM posts WHERE p_tid=$tid ORDER BY p_time DESC LIMIT 1;";
    $last = $this->pdo->querySQL($sql)->fetch();
    $sql = "UPDATE topics SET t_lastpid=$last->pid, t_lastpuid=$last->p_uid, t_lastpuname='$last->p_uname', t_lastptime=$last->p_time WHERE tid=$tid;";
    $this->pdo->exec($sql);

}

if (isset($_POST['tid'])) {
    
    $tid = $_POST['tid'];
    $sql = "SELECT t_subject FROM topics WHERE tid=$tid;";
    $subject = $this->pdo->querySQL($sql)->fetchColumn();
    
?>
    <span class="boldy">Delete Post</span>
    <br>
    <span class="leftspace">&nbsp;</span>
    <span class="boldy"><?php echo $subject ?></span>

    <table id="posts">
<?php
    $sql = "SELECT * FROM posts WHERE p_tid=$tid;";
    $ret = $this->pdo->querySQL($sql);
    $this->pdo = null;
    
    $delflag = false;
foreach($ret as $row) {
?>
        <tr class="frame"><td class="posttop" colspan="2"></td></tr>
        <tr class="frame"><td class="postleft"><?php echo utf8_encode(strftime($this->view->datetime, $row->p_time)) ?><br><?php echo $row->p_uname ?><br>
        <?php
        if ($delflag) {
        ?>
        <form class="link" method="post">
            <input class="link left" type="submit" value="Delete This Post">
            <input type="hidden" name="act" value="postdelete">
            <input type="hidden" name="delpid" value="<?php echo $row->pid ?>">
            <input type="hidden" name="tid" value="<?php echo $tid ?>">
        </form>
        <?php
        }
        ?>
        </td>
        <td class="postright"><?php echo nl2br($row->p_message) ?></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>
<?php
        $delflag = true;
}
?>
    </table>
<?php   
    exit(); 
}

?>
<span class="boldy">Delete Post</span>
<br>
Select topic where is the post:
<table id="topicsnew">  
    <tr><td class="tnewtop" colspan="3"></td></tr>
<?php

$sql = "SELECT tid, t_fid, t_subject, t_lastptime FROM topics 
        ORDER BY t_lastptime DESC LIMIT 30;";
$ret = $this->pdo->querySQL($sql);
foreach($ret as $row) {
    $sql = "SELECT f_name FROM forums WHERE fid=$row->t_fid;";
    $fname = $this->pdo->querySQL($sql)->fetchColumn();
?>
    <tr>
    <td class="tnewleft"><?php echo utf8_encode(strftime($this->view->datetime, $row->t_lastptime)) ?></td>
    <td class="tnewbody">
    <form class="link" method="post">
            <input class="link left" type="submit" value="<?php echo $row->t_subject ?>">
            <input type="hidden" name="act" value="postdelete">
            <input type="hidden" name="tid" value="<?php echo $row->tid ?>">
    </form>
    </td>
    <td class="tnewright2"><span class="boldy"><?php echo $fname ?></span></td>     
    </tr>
<?php
}
$this->pdo = null;
?>
</table>
<div id="topicsspacer"></div>


<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['deltid'])) {

    $tid = $_POST['deltid'];

    // delete all posts where is p_tid=$tid
    $sql = "DELETE FROM posts WHERE p_tid=$tid;";
    $this->pdo->exec($sql);
    // delete topic $tid
    $sql = "DELETE FROM topics WHERE tid=$tid;";
    $this->pdo->exec($sql);
}
?>
<span class="boldy">Delete Topic</span><br>
<span class="boldy">WARNING!</span><br>
Select topic to delete:
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
            <input type="hidden" name="act" value="topicdelete">
            <input type="hidden" name="deltid" value="<?php echo $row->tid ?>">
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


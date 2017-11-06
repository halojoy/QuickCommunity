<?php if(!defined('QCOM1'))exit() ?>
<?php

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
$sql = "SELECT tid, t_fid, t_subject, t_lastpuname, t_lastptime FROM topics 
    WHERE t_lastptime>$timelimit ORDER BY t_lastptime DESC LIMIT 12;";
$ret = $this->pdo->querySQL($sql);
foreach($ret as $row) {
    $sql = "SELECT f_name FROM forums WHERE fid=$row->t_fid;";
    $forumname = $this->pdo->querySQL($sql)->fetchColumn();
?>
            <tr>
            <td class="tnewleft"><?php echo utf8_encode(strftime($this->view->datetime, $row->t_lastptime)) ?></td>
            <td class="tnewbody">
            <form class="link" method="post">
                    <input class="link left" type="submit" value="<?php echo $row->t_subject ?>">
                    <input type="hidden" name="act" value="posts">
                    <input type="hidden" name="tid" value="<?php echo $row->tid ?>">
            </form>
            </td>
            <td class="tnewright1"><span class="boldy"><?php echo $forumname ?></span></td>
            <td class="tnewright2"><?php echo LASTPOSTBY ?> <span class="boldy"><?php echo $row->t_lastpuname ?></span></td>        
            </tr>
<?php
}
$this->pdo = null;
?>
        </table>
        <div id="topicsspacer"></div>

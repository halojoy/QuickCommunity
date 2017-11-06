<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['delfid'])) {
    
    $fid = $_POST['delfid'];

    // 1. delete all posts in $fid
    $sql = "DELETE FROM posts WHERE p_fid=$fid;";
    $this->pdo->exec($sql);
    // 2. delete all topics in forum $fid
    $sql = "DELETE FROM topics WHERE t_fid=$fid;";
    $this->pdo->exec($sql); 
    // 3. delete the forum $fid
    $sql = "DELETE FROM forums WHERE fid=$fid;";
    $this->pdo->exec($sql);

}
?>
<span class="boldy">Delete Forum</span><br>
<span class="boldy">WARNING!</span><br>
Select forum to delete:
<div id="bodycore">
    <table id="forums">
<?php 
$sql = "SELECT * FROM forums ORDER BY f_order";
$res = $this->pdo->querySQL($sql);
foreach($res as $row) {
?>
    <tr><td class="forumtop" colspan="3"></td></tr>
    <tr class="frame">
        <td class="forumleft">          
            <form class="link" method="post">
                <input class="link left" type="submit" value="<?php echo $row->f_name ?>">
                <input type="hidden" name="act" value="forumdelete">
                <input type="hidden" name="delfid" value="<?php echo $row->fid ?>">
            </form>
        </td>
        <td class="forummiddle">
            <?php echo $row->f_desc ?>
        </td>
        <td class="forumright"></td>
    </tr>
    <tr><td class="spacer" colspan="3"></td></tr>
<?php
}
$this->pdo = null;
?>
    </table>
</div>

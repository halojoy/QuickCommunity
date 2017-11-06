<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged() && !$this->sess->isBanned()) {
?>
        <br>
        <div id="postlink">
        <form class="link" method="post">
            <input class="link" type="submit" value="<?php echo POSTREPLY ?>">
            <input type="hidden" name="act" value="postadd">
            <input type="hidden" name="tid" value="<?php echo $this->tid ?>">
        </form>
        </div>
        <br>

<?php
}
?>
        <span class="leftspace">&nbsp;</span>
        <span class="boldy"><?php echo $this->tsubj ?></span>

        <table id="posts">
<?php
$sql = "SELECT * FROM posts WHERE p_tid=$this->tid;";
$ret = $this->pdo->querySQL($sql);
$this->pdo = null;

foreach($ret as $row) {
?>
            <tr class="frame"><td class="posttop" colspan="2"></td></tr>
            <tr class="frame"><td class="postleft"><?php echo utf8_encode(strftime($this->view->datetime, $row->p_time)) ?><br><?php echo $row->p_uname ?></td>
            <td class="postright"><?php echo $row->p_message ?></td></tr>
            <tr><td class="spacer" colspan="2"></td></tr>
<?php
}
?>
        </table>

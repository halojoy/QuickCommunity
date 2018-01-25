<?php if(!defined('QCOM1'))exit() ?>
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
                <input class="link left" type="submit" value="<?php echo $row->f_name ?>">
                <input type="hidden" name="act" value="topics">
                <input type="hidden" name="fid" value="<?php echo $row->fid ?>">
                </form>
            </td>
            <td class="forummiddle"><?php echo $row->f_desc ?></td>
            <td class="forumright">
            <?php if ($row2 !== false)
                echo LASTPOSTBY.' <span class="boldy">'.$row2->t_lastpuname.'</span>' ?>
            </td>
            <td class="forumfarright">
            <?php if ($row2 !== false)
                echo utf8_encode(strftime($this->view->datetime, $row2->t_lastptime)) ?>
            </td>
            </tr>
            <tr><td class="spacer" colspan="4"></td></tr>
<?php
}
$this->pdo = null;
?>
        </table>
        </div>


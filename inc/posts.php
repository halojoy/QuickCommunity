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
        <div class="postupright"><a href="?act=posts&tid=<?php echo $this->tid ?>"><?php echo TOPICLINK ?></a>&nbsp;</div>

        <table id="posts">
<?php

$posts = $this->pdo->getPosts($this->tid);
$this->pdo = null;

foreach($posts as $row) {
?>
            <tr class="frame"><td class="posttop" colspan="2"></td></tr>
            <tr class="frame">
                <td class="postleft">
                    <?php echo utf8_encode(strftime($this->view->datetime, $row->p_time)) ?><br>
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
            <input type="hidden" name="tid" value="<?php echo $this->tid ?>">
        </form>
        </div>
        <br>

<?php
}

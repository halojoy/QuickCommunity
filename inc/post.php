<?php if(!defined('QCOM1'))exit() ?>
        <br>
        <span class="leftspace">&nbsp;</span>
        <span class="boldy"><?php echo $this->tsubj ?></span>

        <table id="posts">
<?php
$sql = "SELECT * FROM posts WHERE pid=$this->pid LIMIT 1;";
$row = $this->pdo->querySQL($sql)->fetch();
$this->pdo = null;
?>
            <tr class="frame"><td class="posttop" colspan="2"></td></tr>
            <tr class="frame">
                <td class="postleft">
                    <?php echo utf8_encode(strftime($this->view->datetime, $row->p_time)) ?><br>
                    <?php echo $row->p_uname ?>
                </td>
                <td class="postright">
                    <div class="postupright"><a href="?act=post&pid=<?php echo $row->pid ?>"><?php echo LINK ?></a></div>
<?php
            if ($this->sess->userid == $row->p_uid) {
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
 ?>
                    <?php echo nl2br($row->p_message) ?> 
<?php
                    if ($row->p_cat == 'image') {
?>
                        <div class="thumb">
                        <a href="upload/<?php echo $row->p_file ?>" target="_blank">
                        <img src="upload/<?php echo 'tmb_'.$row->p_file ?>">
                        </a></div>
<?php
                    }
                    if ($row->p_cat == 'file') {
                        echo '<br><br>'.ATTACHMENT.
                        ' <a href="upload/'.$row->p_file.'" target="_blank">'.$row->p_file.'</a>';
                        echo '&nbsp;&nbsp;'.RIGHTCLICK;
                    }
?>                    
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>
        </table>

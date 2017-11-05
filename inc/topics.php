<?php if(!defined('QCOM1'))exit() ?>
<?php

if($this->sess->isLogged() && !$this->sess->isBanned()) {
?>
		<br>
		<div class="clearleft"></div>
		<div id="postlink">
		<form class="link" method="post">
			<input class="link" type="submit" value="<?php echo POSTTOPIC ?>">
			<input type="hidden" name="act" value="topicadd">
			<input type="hidden" name="fid" value="<?php echo $this->fid ?>">
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
$sql = "SELECT tid, t_subject, t_uname, t_lastpuname, t_lastptime FROM topics 
	WHERE t_fid=$this->fid ORDER BY t_lastptime DESC LIMIT 20;";
$ret = $this->pdo->querySQL($sql);
$this->pdo = null;
foreach($ret as $row) {
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
			<td class="topicright"><?php echo utf8_encode(strftime($this->view->datetime, $row->t_lastptime)) ?></td>
			</tr>
<?php
}
?>
		</table>
		<div id="topicsspacer"></div>

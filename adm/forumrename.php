<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
	header('location:./');
	exit();
}

if (isset($_POST['newfid'])) {
	$fid = $_POST['newfid'];
	$fname = filter_var(trim($_POST['newfname']), FILTER_SANITIZE_STRING);
	$fdesc = filter_var(trim($_POST['newfdesc']), FILTER_SANITIZE_STRING);
	$sql = "UPDATE forums SET f_name='$fname', f_desc='$fdesc' WHERE fid=$fid;";
	$this->pdo->exec($sql);
	$sql = "UPDATE topics SET t_fname='$fname' WHERE t_fid=$fid;";
	$this->pdo->exec($sql);
	$sql = "UPDATE posts SET p_fname='$fname' WHERE p_fid=$fid;";
	$this->pdo->exec($sql);
}

if (isset($_POST['renfid'])) {
	$fid = $_POST['renfid'];
	$fname = $_POST['fname'];
	$fdesc = $_POST['fdesc'];
?>	
	<span class="boldy">Rename Forum</span>
	<br><br>
	<form method="post" accept-charset="UTF-8">
		<span class="boldy">New Forum Name:</span><br/>
		<input type="text" size="30" maxlength="25" required name="newfname" value="<?php echo $fname ?>">
		<br/><br>
		<span class="boldy">New Short Forum Description:</span><br/>
		<input type="text" size="40" maxlength="40" required name="newfdesc" value="<?php echo $fdesc ?>">
		<br/><br/>
		<input type="hidden" name="act" value="forumrename">
		<input type="hidden" name="newfid" value="<?php echo $fid ?>">
		<input type="submit" value="Submit">
	</form>
	<br>
<?php
	exit();
}
?>
<span class="boldy">Rename Forum</span><br>
Select forum to rename:
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
				<input type="hidden" name="act" value="forumrename">
				<input type="hidden" name="renfid" value="<?php echo $row->fid ?>">
				<input type="hidden" name="fname" value="<?php echo $row->f_name ?>">
				<input type="hidden" name="fdesc" value="<?php echo $row->f_desc ?>">
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

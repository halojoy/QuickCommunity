<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
	header('location:./');
	exit();
}

if (isset($_POST['adm_id'])) {
	$this->pdo->exec("UPDATE users SET u_type = 'admin' WHERE uid = " . $_POST['adm_id']);
}
if (isset($_POST['mem_id'])) {
	$this->pdo->exec("UPDATE users SET u_type = 'member' WHERE uid = " . $_POST['mem_id']);
}
if (isset($_POST['ban_id'])) {
	$this->pdo->exec("UPDATE users SET u_type = 'banned' WHERE uid = " . $_POST['ban_id']);
}



$sql = "SELECT * FROM users ORDER BY u_type, lower(u_name);";
$users = $this->pdo->querySQL($sql);
?>
<span class="boldy">Manage Members</span>
<br><br>
	<table border="1">
		<tr>
		<th>Name</th>
		<th>Posts</th>
		<th>Last Active</th>
		<th>Email</th>
		<th>IP</th>
		<th>User Type</th>
		<th>Time Join</th>
		<th></th>
		<th colspan="3">User Type Change!</th>
		</tr>
		<?php
		foreach ($users as $user) {
			echo "<tr><td>" . $user->u_name . "</td>\n";
			echo '<td align="center">' . $user->u_posts . "</td>\n";
			echo '<td>' . date('Y-m-d', $user->u_active) . "</td>\n";
			echo '<td><a href="mailto:' . $user->u_mail . '">' . $user->u_mail . '</a></td>' . "\n";
			echo '<td>' . $user->u_ip . '</td>' . "\n";
			echo '<td align="center">' . $user->u_type . '</td>' . "\n";
			echo '<td>' . date('Y-m-d', $user->u_joined) . '</td>' . "\n";
			echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
			echo '<td>' . "\n";
			?>
			
			<form action="admin.php?act=members" method="post">
				<input type="hidden" name="adm_id" value="<?php echo $user->uid ?>"/>
				<input type="submit" value="Admin"></form>
			<?php
			echo '</td><td>' . "\n";
			?>
			<form action="admin.php?act=members" method="post">
				<input type="hidden" name="mem_id" value="<?php echo $user->uid ?>"/>
				<input type="submit" value="Member"></form>
			<?php
			echo '</td><td>' . "\n";
			?>
			<form action="admin.php?act=members" method="post">
				<input type="hidden" name="ban_id" value="<?php echo $user->uid ?>"/>
				<input type="submit" value="Banned"></form>
			<?php
			echo '</td></tr>' . "\n";
		}
		?>
	</table>
	<br>
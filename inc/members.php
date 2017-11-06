<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}
?>
    <br>
    <div id="memberstop">
        <span class="boldy"><?php echo MEMBERS ?></span>
    </div>
    <table id="members">
        <tr id="memtop"><th><?php echo JOINED ?></th><th><?php echo NAME ?></th>
        <th><?php echo USERTYPE ?></th><th><?php echo POSTS ?></th><th><?php echo LASTACTIVE ?></th></tr>
<?php
$sql = "SELECT * FROM users ORDER BY u_type, lower(u_name);";
$ret = $this->pdo->querySQL($sql);
foreach($ret as $row) {
?>  
        <tr><td><?php echo utf8_encode(strftime($this->view->dateform, $row->u_joined)) ?></td>
        <td><span class="boldy"><?php echo $row->u_name ?></span></td><td><?php echo $row->u_type ?></td>
        <td><?php echo $row->u_posts ?></td><td><?php echo utf8_encode(strftime($this->view->dateform, $row->u_active)) ?></td></tr>
<?php
}
?>
    </table>


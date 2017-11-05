<?php if(!defined('QCOM1'))exit() ?>
<?php

$this->pdo = null;
$this->sess->Logout();
header('location:./');
exit();

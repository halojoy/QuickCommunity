<?php

define('QCOM0', true);
$scope = 'admin';
require 'core/init.php';

if (!$sess->isAdmin()) {
	header('location:./');
	exit();
}

$view->doHeader();
$view->doAdminMenu();
$act->executeAction();
$view->doFooter();

exit();

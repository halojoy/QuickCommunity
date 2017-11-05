<?php

define('QCOM0', true);
$scope = 'index';
require 'core/init.php';

$view->doHeader();
$view->doMenu();
$act->breadCrumb();
$act->executeAction();
$view->doFooter();

exit();

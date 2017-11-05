<?php if(!defined('QCOM0'))exit() ?>
<?php

define('QCOM1', true);
$scriptstart = microtime(true);
error_reporting(-1);// Debug setting

if (!file_exists('conf/config.php')) {
	header('location:setup.php'); exit();}
require 'conf/config.php';
require 'core/classDatabase.php';
$pdo  = new Database($dsn, $dbuser, $dbpass);

require 'core/classSession.php';
$sess = new Session($pdo);

$settings = new stdClass();
$ret = $pdo->querySQL("SELECT setkey, setvalue FROM settings;");
foreach ($ret as $row)
	$settings->{$row->setkey} = $row->setvalue;

require 'core/classViewPage.php';
$view = new ViewPage($sess, $settings, $scriptstart);

require 'core/classAction.php';
$act  = new Action($pdo, $sess, $view, $scope);

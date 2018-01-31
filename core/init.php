<?php if(!defined('QCOM0'))exit();

$scriptstart = microtime(true);

define('QCOM1', true);

error_reporting(32767);// Debug setting

if (!file_exists('conf/config.php')) {
    header('location:setup.php');
    exit();
}
session_start();

require 'conf/config.php';
require 'core/classDatabase.php';
$pdo  = new Database($dsn, $dbuser, $dbpass);

$settings = new stdClass();
$rows = $pdo->getSettings();
foreach ($rows as $row)
    $settings->{$row->setkey} = $row->setvalue;

date_default_timezone_set($settings->timezone);

require 'core/classSession.php';
$sess = new Session($pdo, $settings);

require 'core/classViewPage.php';
$view = new ViewPage($sess, $settings, $scriptstart);

require 'core/classForum.php';
$fora = new Forum($pdo, $sess, $view);

require 'core/classAction.php';
$act  = new Action($pdo, $sess, $view, $fora, $scope);

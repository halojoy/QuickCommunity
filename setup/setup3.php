<?php if(!defined('QCOM1'))exit() ?>
<?php
if (!isset($_POST['db_name'])) exit();
?>
<h2>INSTALL step 3</h2>
<?php

extract($_POST);
$title = filter_var($title, FILTER_SANITIZE_STRING);
$subtitle = filter_var($subtitle, FILTER_SANITIZE_STRING);
$forumname = filter_var($def_forum_name, FILTER_SANITIZE_STRING);
$forumdesc = filter_var($def_forum_desc, FILTER_SANITIZE_STRING);
$admin = filter_var($admin_user, FILTER_SANITIZE_STRING);
$passw = trim($admin_pass);
$email = filter_var($admin_mail, FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	exit('Not a valid email.');

if ($dbdriver == 'mysql') {
	$db_host = filter_var($db_host, FILTER_SANITIZE_STRING);
	$db_user = filter_var($db_user, FILTER_SANITIZE_STRING);
	$db_pass = filter_var($db_pass, FILTER_SANITIZE_STRING);
	$db_name = filter_var($db_name, FILTER_SANITIZE_STRING);
	$handle = <<<BEGIN
<?php

\$dbdriver = 'mysql';
\$dsn = 'mysql:host=$db_host;dbname=$db_name';
\$dbuser = '$db_user';
\$dbpass = '$db_pass';

?>

BEGIN;
}

if ($dbdriver == 'sqlite') {
	$db_name = filter_var($db_name, FILTER_SANITIZE_STRING);
	$handle = <<<BEGIN
<?php

\$dbdriver = 'sqlite';
\$dsn = 'sqlite:data/$db_name';
\$dbuser = '';
\$dbpass = '';

?>

BEGIN;
	$htaccess = <<<HTCODE
<FilesMatch "$db_name">
	Require all denied
</FilesMatch>

HTCODE;
	file_put_contents('data/.htaccess', $htaccess);
}

file_put_contents('conf/config.php', $handle, FILE_APPEND);

$htaccess = <<<HTCODE
<FilesMatch "config.php">
	Require all denied
</FilesMatch>

HTCODE;
file_put_contents('conf/.htaccess', $htaccess);

// generate crypto hexa keys for cookie session
$chars = "0123456789ABCDEF";
$hexkey=''; for ($i=0;$i<16;$i++) $hexkey[$i]=$chars[mt_rand(0,15)];
$hexiv=''; for ($i=0;$i<8;$i++) $hexiv[$i]=$chars[mt_rand(0,15)];
$sessfile = file_get_contents('core/classSession.php');
$sessfile = str_replace(['hexakey', 'hexaiv'], [$hexkey, $hexiv], $sessfile);
file_put_contents('core/classSession.php', $sessfile);

include('conf/config.php');             //Connect to database
$pdo = new PDO($dsn, $dbuser, $dbpass);
include('data/tables_' . $dbdriver . '.php'); //CREATE TABLES

// Insert configured default values.
$pdo->exec("INSERT INTO settings VALUES ('title', '$title');");
$pdo->exec("INSERT INTO settings VALUES ('subtitle', '$subtitle');");
$pdo->exec("INSERT INTO settings VALUES ('style', '$style');");
$pdo->exec("INSERT INTO settings VALUES ('language', '$language');");
$pdo->exec("INSERT INTO settings VALUES ('timezone', '$timezone');");

$pdo->exec("INSERT INTO forums VALUES (null, '$forumname', '$forumdesc', 1);");

//$admin = name
$passh = sha1($passw);
//$email = admin email
$usertype = 'admin';
//num posts = 0
$ip = $_SERVER['REMOTE_ADDR'];
$joined = $active = time();
$pdo->exec("INSERT INTO users VALUES (null,'$admin','$passh','$email','$usertype',0,'$ip',$joined,$active);");

exit('Install is finished. <i>conf/config.php</i> is written.<br />
<b>IMPORTANT: </b>Delete file "setup.php" and folder "setup" now!<br /><br />
Then you can visit forum startpage and Log in: <b>'.$admin.'</b><br />
<a href="./"><b>To Startpage</b></a>');

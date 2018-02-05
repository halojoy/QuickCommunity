<?php if(!defined('QCOM1'))exit();

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

\$dsn = 'mysql:host=$db_host;dbname=$db_name;charset=utf8mb4';
\$dbuser = '$db_user';
\$dbpass = '$db_pass';

BEGIN;
}

if ($dbdriver == 'sqlite') {
    $db_name = filter_var($db_name, FILTER_SANITIZE_STRING);
    $handle = <<<BEGIN
<?php

\$dsn = 'sqlite:data/$db_name';
\$dbuser = '';
\$dbpass = '';

BEGIN;
    $htaccess = <<<HTCODE
<FilesMatch "$db_name">
    Require all denied
</FilesMatch>

HTCODE;
    file_put_contents('data/.htaccess', $htaccess);
}

file_put_contents('conf/config.php', $handle);

$htaccess = <<<HTCODE
<FilesMatch "config.php">
    Require all denied
</FilesMatch>

HTCODE;
file_put_contents('conf/.htaccess', $htaccess);

// Generate crypto keys for cookie session
$cryptokey = bin2hex(openssl_random_pseudo_bytes(16));
$cryptoiv  = bin2hex(openssl_random_pseudo_bytes(16));

include('conf/config.php');             //Connect to database
$pdo = new PDO($dsn, $dbuser, $dbpass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
include('data/tables_' . $dbdriver . '.php'); //CREATE TABLES

// Insert configured default values.
$pdo->exec("INSERT INTO settings VALUES ('title', '$title')");
$pdo->exec("INSERT INTO settings VALUES ('subtitle', '$subtitle')");
$pdo->exec("INSERT INTO settings VALUES ('style', '$style')");
$pdo->exec("INSERT INTO settings VALUES ('language', '$language')");
$pdo->exec("INSERT INTO settings VALUES ('timezone', '$timezone')");
$pdo->exec("INSERT INTO settings VALUES ('usesmtp', '$usesmtp')");
$pdo->exec("INSERT INTO settings VALUES ('googlemail', '$googlemail')");
$pdo->exec("INSERT INTO settings VALUES ('googlepass', '$googlepass')");
$pdo->exec("INSERT INTO settings VALUES ('cryptokey', '$cryptokey')");
$pdo->exec("INSERT INTO settings VALUES ('cryptoiv', '$cryptoiv')");

$pdo->exec("INSERT INTO forums VALUES (null, '$forumname', '$forumdesc', 1);");

//$admin = name
$passh = password_hash($passw, PASSWORD_BCRYPT);
//$email = admin email
//num posts = 0
$ip = $_SERVER['REMOTE_ADDR'];
$joined = $active = time();
$pdo->exec("INSERT INTO users VALUES (null,'$admin','$passh','$email','admin','0',0,'$ip',$joined,$active);");

exit('Install is finished. <i>conf/config.php</i> is written.<br />
<b>IMPORTANT: </b>Delete file "setup.php" and folder "setup" now!<br /><br />
Then you can visit forum startpage and Log in: <b>'.$admin.'</b><br />
<a href="./"><b>To Startpage</b></a>');

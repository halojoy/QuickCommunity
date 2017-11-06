<?php define('QCOM1', true) ?>
<?php

if (file_exists('conf/config.php'))
    exit('Script has already been setup.<br /><b>config.php</b> exists.');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
</head>
<body style="background:#99aabb;">
<div style="background:#cceeff;width:626px;margin:auto;padding:10px 10px">
<?php
if (isset($_POST['step'])) {
    require 'setup/setup'.$_POST['step'].'.php';
    exit();
}
?>
    <h2>Welcome! Setup step 1</h2>

    <form method="post">
        <fieldset>
            <b>Database:</b><br>
            <select name="dbdriver">
                <option value="sqlite" selected>SQLite</option>
                <option value="mysql">MySQL</option>
            </select> Database you want to use<br><br>
            <input type="submit" value="SUBMIT">
            <input type="hidden" name="step" value="2">
            <br>
        </fieldset>
    </form>
    
<?php

exit();

<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['newtimezone'])) {

    $sql = "UPDATE settings SET setvalue='".$_POST['newtimezone']."' WHERE setkey='timezone';";
    $ret = $this->pdo->querySQL($sql);
    header('location:./admin.php');
    exit();
}
 
function get_timezones()
{
    $out = array();
    $timezones = DateTimeZone::listIdentifiers();
    foreach($timezones as $tzone)
    {
        $label = '';
        $dtzone = new DateTimeZone($tzone); //Throws exception for 'US/Pacific-New'
        $seconds = $dtzone->getOffset(new DateTime("now", $dtzone));
        $hours   = sprintf( "%+02d" , intval($seconds/3600));
        $minutes = sprintf( "%02d" , ($seconds%3600)/60 );
        $label = $tzone."  [ $hours:$minutes ]" ;
        $out[$tzone] = $label;
    }
    ksort($out);
    return $out;
}

$output = get_timezones();
$current = $this->view->timezone;
?>
<form method="post">
    <select name="newtimezone">
<?php
        foreach($output as $tz => $label)
        {
            if ($tz == $current)
                echo "<option value=".$tz." selected>$label</option>";
            else
                echo "<option value=".$tz.">$label</option>";
        }
?>
    </select>
    <br><br>
    <input type="submit" value="Submit">
    <input type="hidden" name="act" value="timezones">
</form>
<br>

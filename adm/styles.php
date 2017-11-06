<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['newstyle'])) {

    $sql = "UPDATE settings SET setvalue='".$_POST['newstyle']."' WHERE setkey='style';";
    $ret = $this->pdo->querySQL($sql);
    header('location:./admin.php');
    exit();
}
?>
    <span class="boldy">Change Style</span>
    <br><br>
    <form method="post">
        <select name="newstyle">    
<?php
        $current = $this->view->style;
        foreach (scandir('css') as $string) {
            if (preg_match("@^(.+)\.css$@", $string, $match))
                $styles[] = $match[1];
        }
        foreach ($styles as $style) {
            if ($style == $current)
                echo '<option value="' . $style . '" selected>' . $style . '</option>' . "\n";
            else
                echo '<option value="' . $style . '">' . $style . '</option>' . "\n";
        }
?>
        </select>
        <br><br>
        <input type="submit" value="Submit">
        <input type="hidden" name="act" value="styles">
    </form>
    <br>

<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

if (isset($_POST['newlang'])) {

    $sql = "UPDATE settings SET setvalue='".$_POST['newlang']."' WHERE setkey='language';";
    $ret = $this->pdo->querySQL($sql);
    header('location:./admin.php');
    exit();
}
?>
    <span class="boldy">Change Language</span>
    <br><br>
    <form method="post">
        <select name="newlang"> 
<?php
        $current = $this->view->language;
        foreach (scandir('lang') as $string) {
            if (preg_match("@^(.+)\.php$@", $string, $match))
                $languages[] = $match[1];
        }
        foreach ($languages as $language) {
            if ($language == $current)
                echo '<option value="' . $language . '" selected>' . $language . '</option>' . "\n";
            else
                echo '<option value="' . $language . '">' . $language . '</option>' . "\n";
        }
?>
        </select>
        <br><br>
        <input type="submit" value="Submit">
        <input type="hidden" name="act" value="languages">
    </form>
    <br>

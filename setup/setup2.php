<?php if(!defined('QCOM1'))exit() ?>
<?php
if (!isset($_POST['dbdriver'])) exit();
?>
    <h2>INSTALL step 2</h2>
<?php
    $dbdriver = $_POST['dbdriver'];
?>
    <form method="post">
        <fieldset>
<?php
        if ($dbdriver == 'mysql') {
?>
            <b>MySQL:</b><br/>
            <input type="text" name="db_host" required> Database server, often 'localhost'<br/>
            <input type="text" name="db_user" required> Database username<br/>
            <input type="text" name="db_pass" required> Database password<br/>
            <input type="text" name="db_name" required> Database name, must have been created<br/><br>
<?php
        }
        if ($dbdriver == 'sqlite') {
?>
            <b>SQLite:</b><br/>
            <input type="text" name="db_name" required> SQLite Database file, for example: myforum.db3<br/><br>
<?php
        }
?>
            <input type="hidden" name="dbdriver" value="<?php echo $dbdriver ?>">
        
            <b>Website:</b><br>
            <input type="text" name="title" size="40" required> Website Name = TITLE<br>
            <input type="text" name="subtitle" size="40" required> Website Short Description<br/><br>
            <b>Forum Style:</b><br/>
            <select name="style">
<?php
            foreach (scandir('css') as $string) {
                if (preg_match("@^(.+)\.css$@", $string, $match))
                    $styles[] = $match[1];
            }
            foreach ($styles as $style) {
                if ($style == 'default')
                    echo '<option value="' . $style . '" selected>' . $style . '</option>' . "\n";
                else
                    echo '<option value="' . $style . '">' . $style . '</option>' . "\n";
            }
?>
            </select><br><br>
            <b>Forum Language:</b><br>
            <select name="language">
<?php
            foreach (scandir('lang') as $string) {
                if (preg_match("@^(.+)\.php$@", $string, $match))
                    $languages[] = $match[1];
            }
            foreach ($languages as $language) {
                if ($language == 'english')
                    echo '<option value="' . $language . '" selected>' . $language . '</option>' . "\n";
                else
                    echo '<option value="' . $language . '">' . $language . '</option>' . "\n";
            }
?>
            </select><br><br>
            <b>Timezone:</b><br>
            <input type="text" name="timezone" value="Europe/London" required> Your Timezone<br><br>
            <b>Default Forum.</b> Will be added to begin with:<br/>
            <input type="text" name="def_forum_name" required> Default forum name.<br/>
            <input type="text" name="def_forum_desc" required> Default short forum description<br/><br>
            <b>Forum Admin:</b><br/>
            <input type="text" name="admin_user" required> Admin username, often 'Admin'<br/>
            <input type="text" name="admin_pass" required> Admin password<br/>
            <input type="text" name="admin_mail" required> Admin Email address<br/><br>

            <input type="submit" value="SUBMIT">
            <input type="hidden" name="step" value="3">
            <br/>
        </fieldset>
    </form>

<?php

exit();

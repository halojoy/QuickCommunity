<?php if(!defined('QCOM1'))exit() ?>
<?php

class ViewPage
{
    public $sess;
    public $title;
    public $subtitle;
    public $style;
    public $language;
    public $timezone;
    public $scriptstart;
    public $lang;
    public $dateform;
    public $datetime;
    
    public function __construct($sess, $settings, $scriptstart)
    {
        $this->sess = $sess;
        $this->title = $settings->title;
        $this->subtitle = $settings->subtitle;
        $this->style = $settings->style;
        $this->language = $settings->language;
        $this->timezone = $settings->timezone;
        $this->scriptstart = $scriptstart;
        
        date_default_timezone_set($this->timezone);
        require 'lang/'.$this->language.'.php';
        setlocale(LC_ALL, $locale);
        $this->lang = $lang;
        $this->dateform = $dateform;
        $this->datetime = $datetime;

        return; 
    }

    public function doHeader()
    {
?>
<!DOCTYPE html>
<html lang="<?php echo $this->lang ?>">
<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $this->title."\n" ?>
    </title>
    <link rel="stylesheet" type="text/css" href="css/<?php echo $this->style ?>.css">
</head>
<body>
    <div id="wrapper">

        <span id="title"><?php echo $this->title ?></span><span id="subtitle"><?php echo $this->subtitle ?></span>
        <br>

<?php
        return;
    }
    
    public function doFooter()
    {
?>
        <div id="footer">
            <div id="copyright">
                Copyright &copy; 2017 halojoy applying MIT License
            </div>
            <div id="scripttime">
                <?php echo 'Powered by QuickCommunity - Script Time: ' . ceil(1000*(microtime(true)-$this->scriptstart))/1000 . ' seconds'."\n" ?>
            </div>
        </div>

    </div>
</body>
</html>
<?php
        return;
    }
    
    public function doMenu()
    {
?>
        <div id="topmenu">
            <a class="inverse" href="./"><?php echo HOME ?></a>
<?php
if (!$this->sess->isLogged()) {
?>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="<?php echo LOGIN ?>">
                <input type="hidden" name="act" value="login">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="<?php echo REGISTER ?>">
                <input type="hidden" name="act" value="register">
            </form>
<?php
} else {
?>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="<?php echo LOGOUT ?>">
                <input type="hidden" name="act" value="logout">
            </form>
<?php
if (!$this->sess->isBanned()) {
?>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="<?php echo MEMBERS ?>">
                <input type="hidden" name="act" value="members">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="<?php echo NEWPOSTS ?>">
                <input type="hidden" name="act" value="topicsnew">
            </form>
<?php
}
if ($this->sess->isAdmin()) {
?>
            <a class="inverse" href="./admin.php"><?php echo ADMIN ?></a>
<?php
}
?>
            <br>
            <div id="loggedas" style="clear: left; margin-left: 8px;">
                <?php echo LOGGEDAS ?> <span class="boldy"><?php echo $this->sess->username ?></span>
            </div>
<?php
}
?>
        </div>
        <br>

<?php
        return;
    }

    public function doAdminMenu()
    {
?>
        <div id="adminmenu">

            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="START">
                <input type="hidden" name="act" value="home">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value=" Members">
                <input type="hidden" name="act" value="members">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="Add New Forum">
                <input type="hidden" name="act" value="forumadd">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="Set Forum Order">
                <input type="hidden" name="act" value="forumorder">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="Change Style">
                <input type="hidden" name="act" value="styles">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="Change Language">
                <input type="hidden" name="act" value="languages">
            </form>
            <form class="inverse" method="post">
                <input class="inverse" type="submit" value="Change Timezone">
                <input type="hidden" name="act" value="timezones">
            </form>

        </div>
        <br>

<?php
        return;
    }

    
}

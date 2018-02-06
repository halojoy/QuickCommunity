<?php if(!defined('QCOM1'))exit();

class Admin
{
    public $pdo;
    public $style;
    public $language;
    public $timezone;
    public $usesmtp;
    public $googlemail;
    public $googlepass;
    public $datetime;

    public function __construct($pdo, $settings)
    {
        $this->pdo        = $pdo;
        $this->style      = $settings->style;
        $this->language   = $settings->language;
        $this->timezone   = $settings->timezone;
        $this->usesmtp    = $settings->usesmtp;
        $this->googlemail = $settings->googlemail;
        $this->googlepass = $settings->googlepass;
        $this->datetime   = $settings->datetime;
    }

    public function adminhome()
    {
?>
        <a href="./"><b>Back To Website >></b></a>
        <br><br>
        Welcome to your Admin interface.<br>
        Here you can make changes.<br>
        Enjoy!
        <br>
        You Can:
        <br><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Manage Members">
            <input type="hidden" name="act" value="members">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Send SMTP Mail">
            <input type="hidden" name="act" value="sendmail">
        </form><br><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Sticky Topics">
            <input type="hidden" name="act" value="topicsticky">
        </form><br><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Delete Post">
            <input type="hidden" name="act" value="postdelete">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Delete Topic">
            <input type="hidden" name="act" value="topicdelete">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Delete Forum">
            <input type="hidden" name="act" value="forumdelete">
        </form><br><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Add New Forum">
            <input type="hidden" name="act" value="forumadd">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Set Forum Order">
            <input type="hidden" name="act" value="forumorder">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Rename Forum">
            <input type="hidden" name="act" value="forumrename">
        </form><br><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Change Style">
            <input type="hidden" name="act" value="styles">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Change Language">
            <input type="hidden" name="act" value="languages">
        </form><br>
        <form class="link" method="post">
            <input class="link" type="submit" value="Change Timezone">
            <input type="hidden" name="act" value="timezones">
        </form><br>
        <br>
<?php
    }

    public function members()
    {

        if (isset($_POST['adm_id'])) {
            $this->pdo->exec("UPDATE users SET u_type = 'admin' WHERE uid = " . $_POST['adm_id']);
        }
        if (isset($_POST['mem_id'])) {
            $this->pdo->exec("UPDATE users SET u_type = 'member' WHERE uid = " . $_POST['mem_id']);
        }
        if (isset($_POST['ban_id'])) {
            $this->pdo->exec("UPDATE users SET u_type = 'banned' WHERE uid = " . $_POST['ban_id']);
        }

        $users = $this->pdo->getMembers();
?>
        <span class="boldy">Manage Members</span>
        <br><br>
            <table border="1">
                <tr>
                <th>Name</th>
                <th>Posts</th>
                <th>Last Active</th>
                <th>Email</th>
                <th>IP</th>
                <th>User Type</th>
                <th>Time Join</th>
                <th></th>
                <th colspan="3">User Type Change!</th>
                </tr>
<?php
                foreach ($users as $user) {
                    echo "<tr><td>" . $user->u_name . "</td>\n";
                    echo '<td align="center">' . $user->u_posts . "</td>\n";
                    echo '<td>' . date('Y-m-d', $user->u_active) . "</td>\n";
                    echo '<td><a href="mailto:' . $user->u_mail . '">' . $user->u_mail . '</a></td>' . "\n";
                    echo '<td>' . $user->u_ip . '</td>' . "\n";
                    echo '<td align="center">' . $user->u_type . '</td>' . "\n";
                    echo '<td>' . date('Y-m-d', $user->u_joined) . '</td>' . "\n";
                    echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                    echo '<td>' . "\n";
?>

                    <form action="admin.php?act=members" method="post">
                        <input type="hidden" name="adm_id" value="<?php echo $user->uid ?>"/>
                        <input type="submit" value="Admin"></form>
<?php
                    echo '</td><td>' . "\n";
?>
                    <form action="admin.php?act=members" method="post">
                        <input type="hidden" name="mem_id" value="<?php echo $user->uid ?>"/>
                        <input type="submit" value="Member"></form>
<?php
                    echo '</td><td>' . "\n";
?>
                    <form action="admin.php?act=members" method="post">
                        <input type="hidden" name="ban_id" value="<?php echo $user->uid ?>"/>
                        <input type="submit" value="Banned"></form>
<?php
                    echo '</td></tr>' . "\n";
                }
?>
            </table>
        <br>
<?php
    }

    public function sendmail()
    {
        if (isset($_POST['usesmtp'])) {
            $usesmtp = $_POST['usesmtp'];
            $gmail   = $_POST['googlemail'];
            $gpass   = $_POST['googlepass'];
            $sql = "UPDATE settings SET setvalue='$usesmtp' WHERE setkey='usesmtp';";
            $this->pdo->exec($sql);
            $sql = "UPDATE settings SET setvalue='$gmail' WHERE setkey='googlemail';";
            $this->pdo->exec($sql);
            $sql = "UPDATE settings SET setvalue='$gpass' WHERE setkey='googlepass';";
            $this->pdo->exec($sql);
            header('location:./admin.php');
            exit();
       }
?>
        <form method="post">
            You want to have Register with SMTP Email Activation?
            <select name="usesmtp">
<?php
                if ($this->usesmtp) {
?>
                <option value="1" selected>Yes</option>
                <option value="0">No</option>
<?php
                } else {
?>
                <option value="1">Yes</option>
                <option value="0" selected>No</option>
<?php
        }
?>
            </select><br><br>
            Your google email account, needed for SMTP:<br>
            <input type="text" name="googlemail" size="40" value="<?php echo $this->googlemail ?>"><br><br>
            Your gmail password:<br>
            <input type="text" name="googlepass" value="<?php echo $this->googlepass ?>"><br><br>
            <input type="submit" value="SUBMIT">
            <input type="hidden" name="act" value="sendmail">
            <br><br>
        </form>
<?php
    }

    public function postdelete()
    {
        if (isset($_POST['delpid'])) {

            $pid = $_POST['delpid'];
            $tid = $_POST['tid'];
            $_POST = array();

            $this->pdo->exec("DELETE FROM posts WHERE pid=".$pid);
            
            $sql = "SELECT pid, p_uid, p_uname, p_time FROM posts WHERE p_tid=$tid ORDER BY p_time DESC LIMIT 1;";
            $last = $this->pdo->querySQL($sql)->fetch();
            $sql = "UPDATE topics SET t_lastpid=$last->pid, t_lastpuid=$last->p_uid, t_lastpuname='$last->p_uname', t_lastptime=$last->p_time WHERE tid=$tid;";
            $this->pdo->exec($sql);
        }

        if (isset($_POST['tid'])) {
            
            $tid = $_POST['tid'];
            $sql = "SELECT t_subject FROM topics WHERE tid=$tid;";
            $subject = $this->pdo->querySQL($sql)->fetchColumn();
            
?>
            <span class="boldy">Delete Post</span>
            <br>
            <span class="leftspace">&nbsp;</span>
            <span class="boldy"><?php echo $subject ?></span>

            <table id="posts">
<?php
            $sql = "SELECT * FROM posts WHERE p_tid=$tid;";
            $ret = $this->pdo->querySQL($sql);
            
            $delflag = false;
            foreach($ret as $row) {
?>
                <tr class="frame"><td class="posttop" colspan="2"></td></tr>
                <tr class="frame"><td class="postleft"><?php echo utf8_encode(strftime($this->datetime, $row->p_time)) ?><br><?php echo $row->p_uname ?><br>
<?php
                if ($delflag) {
?>
                <form class="link" method="post">
                    <input class="link left" type="submit" value="Delete This Post">
                    <input type="hidden" name="act" value="postdelete">
                    <input type="hidden" name="delpid" value="<?php echo $row->pid ?>">
                    <input type="hidden" name="tid" value="<?php echo $tid ?>">
                </form>
<?php
                }
?>
                </td>
                <td class="postright"><?php echo nl2br($row->p_message) ?></td></tr>
                <tr><td class="spacer" colspan="2"></td></tr>
<?php
                $delflag = true;
            }
?>
            </table>
<?php   
            exit(); 
        }
?>
        <span class="boldy">Delete Post</span>
        <br>
        Select topic where is the post:
        <table id="topicsnew">  
            <tr><td class="tnewtop" colspan="3"></td></tr>
<?php

        $sql = "SELECT tid, t_fid, t_subject, t_lastptime FROM topics 
                ORDER BY t_lastptime DESC LIMIT 30;";
        $ret = $this->pdo->querySQL($sql);
        foreach($ret as $row) {
            $sql = "SELECT f_name FROM forums WHERE fid=$row->t_fid;";
            $fname = $this->pdo->querySQL($sql)->fetchColumn();
?>
            <tr>
            <td class="tnewleft"><?php echo utf8_encode(strftime($this->datetime, $row->t_lastptime)) ?></td>
            <td class="tnewbody">
            <form class="link" method="post">
                    <input class="link left" type="submit" value="<?php echo $row->t_subject ?>">
                    <input type="hidden" name="act" value="postdelete">
                    <input type="hidden" name="tid" value="<?php echo $row->tid ?>">
            </form>
            </td>
            <td class="tnewright2"><span class="boldy"><?php echo $fname ?></span></td>     
            </tr>
<?php
        }
?>
        </table>
        <div id="topicsspacer"></div>
<?php
    }

    public function topicdelete()
    {
        if (isset($_POST['deltid'])) {

            $tid = $_POST['deltid'];

            // delete all posts where is p_tid=$tid
            $sql = "DELETE FROM posts WHERE p_tid=$tid;";
            $this->pdo->exec($sql);
            // delete topic $tid
            $sql = "DELETE FROM topics WHERE tid=$tid;";
            $this->pdo->exec($sql);
        }
?>
        <span class="boldy">Delete Topic</span><br>
        <span class="boldy">WARNING!</span><br>
        Select topic to delete:
        <table id="topicsnew">  
            <tr><td class="tnewtop" colspan="3"></td></tr>
<?php
        $sql = "SELECT tid, t_fid, t_subject, t_lastptime FROM topics 
                ORDER BY t_lastptime DESC LIMIT 30;";
        $ret = $this->pdo->querySQL($sql);
        foreach($ret as $row) {
            $sql = "SELECT f_name FROM forums WHERE fid=$row->t_fid;";
            $fname = $this->pdo->querySQL($sql)->fetchColumn();
?>
            <tr>
            <td class="tnewleft"><?php echo utf8_encode(strftime($this->datetime, $row->t_lastptime)) ?></td>
            <td class="tnewbody">
            <form class="link" method="post">
                    <input class="link left" type="submit" value="<?php echo $row->t_subject ?>">
                    <input type="hidden" name="act" value="topicdelete">
                    <input type="hidden" name="deltid" value="<?php echo $row->tid ?>">
            </form>
            </td>
            <td class="tnewright2"><span class="boldy"><?php echo $fname ?></span></td>     
            </tr>
<?php
        }
?>
        </table>
        <div id="topicsspacer"></div>
<?php
    }

    public function topicsticky()
    {
        if (isset($_POST['stickytid'])) {

            $tid = $_POST['stickytid'];
            $sql = "SELECT t_sticky FROM topics WHERE tid=$tid";
            $sticky = $this->pdo->querySQL($sql)->fetchColumn();
            if ($sticky) $new = '0';
            else         $new = '1';
            $this->pdo->exec("UPDATE topics SET t_sticky=$new WHERE tid=$tid");
        }
?>
        <span class="boldy">Sticky Topics</span><br>
        Click topic to change <b>Sticky</b>:
        <table id="topicsnew">  
            <tr><td class="tnewtop" colspan="3"></td></tr>
<?php
        $sql = "SELECT tid, t_fid, t_subject, t_lastptime, t_sticky FROM topics 
                ORDER BY t_lastptime DESC LIMIT 30;";
        $ret = $this->pdo->querySQL($sql);
        foreach($ret as $row) {
            $sql = "SELECT f_name FROM forums WHERE fid=$row->t_fid;";
            $fname = $this->pdo->querySQL($sql)->fetchColumn();
?>
            <tr>
            <td class="tnewleft"><?php echo utf8_encode(strftime($this->datetime, $row->t_lastptime)) ?></td>
            <td class="tnewbody">
<?php
            if ($row->t_sticky) {
                echo '<span class="sticky">Sticky</span>';
            }
?>
            <form class="link" method="post">
                    <input class="link left" type="submit" value="<?php echo $row->t_subject ?>">
                    <input type="hidden" name="act" value="topicsticky">
                    <input type="hidden" name="stickytid" value="<?php echo $row->tid ?>">
            </form>
            </td>
            <td class="tnewright2"><span class="boldy"><?php echo $fname ?></span></td>     
            </tr>
<?php
        }
?>
        </table>
        <div id="topicsspacer"></div>
<?php
    }

    public function forumdelete()
    {
        if (isset($_POST['delfid'])) {
            
            $fid = $_POST['delfid'];

            // 1. delete all posts in $fid
            $sql = "DELETE FROM posts WHERE p_fid=$fid;";
            $this->pdo->exec($sql);
            // 2. delete all topics in forum $fid
            $sql = "DELETE FROM topics WHERE t_fid=$fid;";
            $this->pdo->exec($sql); 
            // 3. delete the forum $fid
            $sql = "DELETE FROM forums WHERE fid=$fid;";
            $this->pdo->exec($sql);
        }
?>
        <span class="boldy">Delete Forum</span><br>
        <span class="boldy">WARNING!</span><br>
        Select forum to delete:
        <div id="bodycore">
            <table id="forums">
<?php 
        $sql = "SELECT * FROM forums ORDER BY f_order";
        $res = $this->pdo->querySQL($sql);
        foreach($res as $row) {
?>
            <tr><td class="forumtop" colspan="3"></td></tr>
            <tr class="frame">
                <td class="forumleft">          
                    <form class="link" method="post">
                        <input class="link left" type="submit" value="<?php echo $row->f_name ?>">
                        <input type="hidden" name="act" value="forumdelete">
                        <input type="hidden" name="delfid" value="<?php echo $row->fid ?>">
                    </form>
                </td>
                <td class="forummiddle">
                    <?php echo $row->f_desc ?>
                </td>
                <td class="forumright"></td>
            </tr>
            <tr><td class="spacer" colspan="3"></td></tr>
<?php
        }
?>
            </table>
        </div>
<?php
    }

    public function forumadd()
    {
        if (isset($_POST['forum_name'])) {
            $forum_name = filter_var(trim($_POST['forum_name']), FILTER_SANITIZE_STRING);
            $forum_desc = filter_var(trim($_POST['forum_desc']), FILTER_SANITIZE_STRING);
            if (empty($forum_name)) {
                echo 'Forum name can not be empty<br><br>';
            } else {
                $sql = "INSERT INTO forums (f_name, f_desc) VALUES ('$forum_name', '$forum_desc')";
                $this->pdo->exec($sql);
                $last_id = $this->pdo->lastInsertId();
                $sql = "UPDATE forums SET f_order=$last_id WHERE fid=$last_id";
                $this->pdo->exec($sql);
                echo 'Forum was created<br><br>';
            }
        }

        //New forum form
?>
        <span class="boldy">Add New Forum</span>
        <br><br>
        <form method="post" accept-charset="UTF-8">
            Forum Name:<br/>
            <input type="text" size="30" maxlength="25" required name="forum_name">
            <br/>
            Short Forum Description:<br/>
            <input type="text" size="40" maxlength="40" name="forum_desc">
            <br/><br/>
            <input type="hidden" name="act" value="forumadd">
            <input type="submit" value="Submit">
        </form>
        <br>
<?php        
    }

    public function forumorder()
    {
        if (isset($_POST['disp_ord'])) {
            $neworder = $_POST['in'];
            foreach ($neworder as $fid => $disp_order) {
                $neworder[$fid] = trim($disp_order);
                if (!is_numeric($neworder[$fid]) || empty($neworder[$fid])) {
                    echo 'Forum order value should be a number!';
                    exit();
                }
            }
            foreach ($neworder as $fid => $disp_ord) {
                $sql = "UPDATE forums SET f_order=$disp_ord WHERE fid=$fid";
                $ret = $this->pdo->querySQL($sql);
            }
        }

        $sql = "SELECT * FROM forums ORDER BY f_order";
        $forums = $this->pdo->querySQL($sql)->fetchAll();
?>
        <span class="boldy">Forum Display Order</span>
        <br><br>
        <form method="post">
            <table>
<?php
                foreach ($forums as $forum) {
                    echo "<tr>\n";
                    echo "<td>" . $forum->f_name . "</td>";
                    echo '<td>
                            <input type="text" size="2" maxlength="2" name="in['.$forum->fid.']"
                            value="'.$forum->f_order.'" required />';
                    echo '</td>'."\n";
                    echo "</tr>\n";
                }
?>
            </table>
            <br>
            <input type="submit" value="Submit">
            <input type="hidden" name="act" value="forumorder">
            <input type="hidden" name="disp_ord" value="1">
        </form>
        <br>        
<?php
    }

    public function forumrename()
    {
        if (isset($_POST['newfid'])) {
            $fid = $_POST['newfid'];
            $fname = filter_var(trim($_POST['newfname']), FILTER_SANITIZE_STRING);
            $fdesc = filter_var(trim($_POST['newfdesc']), FILTER_SANITIZE_STRING);
            $sql = "UPDATE forums SET f_name='$fname', f_desc='$fdesc' WHERE fid=$fid;";
            $this->pdo->exec($sql);
            $sql = "UPDATE topics SET t_fname='$fname' WHERE t_fid=$fid;";
            $this->pdo->exec($sql);
            $sql = "UPDATE posts SET p_fname='$fname' WHERE p_fid=$fid;";
            $this->pdo->exec($sql);
        }

        if (isset($_POST['renfid'])) {
            $fid = $_POST['renfid'];
            $fname = $_POST['fname'];
            $fdesc = $_POST['fdesc'];
?>
            <span class="boldy">Rename Forum</span>
            <br><br>
            <form method="post" accept-charset="UTF-8">
                <span class="boldy">New Forum Name:</span><br/>
                <input type="text" size="30" maxlength="25" required name="newfname" value="<?php echo $fname ?>">
                <br/><br>
                <span class="boldy">New Short Forum Description:</span><br/>
                <input type="text" size="40" maxlength="40" required name="newfdesc" value="<?php echo $fdesc ?>">
                <br/><br/>
                <input type="hidden" name="act" value="forumrename">
                <input type="hidden" name="newfid" value="<?php echo $fid ?>">
                <input type="submit" value="Submit">
            </form>
            <br>
<?php
            exit();
        }
?>
        <span class="boldy">Rename Forum</span><br>
        Select forum to rename:
        <div id="bodycore">
            <table id="forums">
<?php
        $sql = "SELECT * FROM forums ORDER BY f_order";
        $res = $this->pdo->querySQL($sql);
        foreach($res as $row) {
?>
            <tr><td class="forumtop" colspan="3"></td></tr>
            <tr class="frame">
                <td class="forumleft">          
                    <form class="link" method="post">
                        <input class="link left" type="submit" value="<?php echo $row->f_name ?>">
                        <input type="hidden" name="act" value="forumrename">
                        <input type="hidden" name="renfid" value="<?php echo $row->fid ?>">
                        <input type="hidden" name="fname" value="<?php echo $row->f_name ?>">
                        <input type="hidden" name="fdesc" value="<?php echo $row->f_desc ?>">
                    </form>
                </td>
                <td class="forummiddle">
                    <?php echo $row->f_desc ?>
                </td>
                <td class="forumright"></td>
            </tr>
            <tr><td class="spacer" colspan="3"></td></tr>
<?php
        }
?>
            </table>
        </div>
<?php
    }

    public function styles()
    {
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
                $current = $this->style;
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
<?php
    }

    public function languages()
    {
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
                $current = $this->language;
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
<?php
    }

    public function timezones()
    {
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
        $current = $this->timezone;
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
<?php
    }    
    
}
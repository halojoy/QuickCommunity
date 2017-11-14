<?php if(!defined('QCOM1'))exit() ?>
<?php

class Action
{
    public $pdo;
    public $sess;
    public $view;
    public $scope;
    public $act   = 'home';
    public $fid   = false;
    public $tid   = false;
    public $pid   = false;
    public $fname = false;
    public $tsubj = false;

    public function __construct($pdo, $sess, $view, $scope)
    {
        $this->pdo  = $pdo;
        $this->sess = $sess;
        $this->view = $view;
        $this->scope = $scope;
        
        if (isset($_POST['act']))
            $this->act = $_POST['act'];
        elseif (isset($_GET['act']))
            $this->act = $_GET['act'];

        if ($this->scope == 'admin') {
            $this->act = $this->validAdmin($this->act);
            return;
        }

        $this->act = $this->validAction($this->act);
        if ($this->act == 'home')
            return;

        $this->act = $this->prepArguments();

        return;
    }

    public function prepArguments()
    {
        switch($this->act):
            case 'topics':
            case 'topicadd':
                if (isset($_POST['fid'])) $this->fid = $_POST['fid'];
                elseif (isset($_GET['fid'])) $this->fid = $_GET['fid'];
                else return 'home';
                $sql = "SELECT f_name FROM forums WHERE fid=$this->fid;";
                $this->fname = $this->pdo->querySQL($sql)->fetchColumn();
                break;
            case 'posts':
            case 'postadd':
                if (isset($_POST['tid'])) $this->tid = $_POST['tid'];
                elseif (isset($_GET['tid'])) $this->tid = $_GET['tid'];
                else return 'home';
                $sql = "SELECT * FROM topics WHERE tid=$this->tid;";
                $row = $this->pdo->querySQL($sql)->fetch();
                $this->fid = $row->t_fid; $this->fname = $row->t_fname;
                $this->tsubj = $row->t_subject;
                break;
            case 'post':
            case 'postedit':
                if (isset($_POST['pid'])) $this->pid = $_POST['pid'];
                elseif (isset($_GET['pid'])) $this->pid = $_GET['pid'];
                else return 'home';
                $sql = "SELECT * FROM posts WHERE pid=$this->pid;";
                $row = $this->pdo->querySQL($sql)->fetch();
                $this->fid = $row->p_fid; $this->fname = $row->p_fname; 
                $this->tid = $row->p_tid; $this->tsubj = $row->p_tsubj;
                break;          
            default:
                return $this->act;
                break;
        endswitch;

        return $this->act;
    }
    
    public function executeAction()
    {
        if ($this->scope == 'index')
            require 'inc/'.$this->act.'.php';
        if ($this->scope == 'admin')
            require 'adm/'.$this->act.'.php';
        return;     
    }
    
    public function validAction($act)
    {
        switch ($act):
            case 'home':
            case 'topics':
            case 'posts':
            case 'post':
            case 'topicadd':
            case 'postadd':
            case 'topicsnew':
            case 'login':
            case 'register':
            case 'logout':
            case 'members':
            case 'postedit':
                return $act;
                break;
            default:
                return 'home';
                break;
        endswitch;
    }

    public function validAdmin($act)
    {
        switch ($act):
            case 'home':
            case 'forumadd':
            case 'forumorder':
            case 'styles':
            case 'languages':
            case 'timezones':
            case 'members':
            case 'postdelete':
            case 'topicdelete':
            case 'forumdelete':
            case 'forumrename':
                return $act;
                break;
            default:
                return 'home';
                break;
        endswitch;      
    }
    
        
    public function breadCrumb()
    {
?>
        <div id="breadcrumb">
            <a class="fleft" href="./"><?php echo HOME ?></a>
<?php

if (in_array($this->act, array('home', 'login', 'logout', 'register', 'members', 'topicsnew'))) {

    switch ($this->act):

        case 'login':
?>
                <span class="link">&nbsp;&#187;&nbsp;</span>
                <form class="link" method="post">
                    <input class="link" type="submit" value="<?php echo LOGIN ?>">
                    <input type="hidden" name="act" value="login">
                </form>
<?php
            break;
        case 'register':
?>
                <span class="link">&nbsp;&#187;&nbsp;</span>
                <form class="link" method="post">
                    <input class="link" type="submit" value="<?php echo REGISTER ?>">
                    <input type="hidden" name="act" value="register">
                </form>
<?php
            break;
        case 'members':
?>
                <span class="link">&nbsp;&#187;&nbsp;</span>
                <form class="link" method="post">
                    <input class="link" type="submit" value="<?php echo MEMBERS ?>">
                    <input type="hidden" name="act" value="members">
                </form>
<?php
            break;
        case 'topicsnew':
?>
                <span class="link">&nbsp;&#187;&nbsp;</span>
                <form class="link" method="post">
                    <input class="link" type="submit" value="<?php echo NEWPOSTS ?>">
                    <input type="hidden" name="act" value="topicsnew">
                </form>
<?php
            break;

    endswitch;

}

if (in_array($this->act, array('topics', 'topicadd', 'posts', 'postadd', 'postedit', 'post'))) {

?>
    <span class="link">&nbsp;&#187;&nbsp;</span>
    <form class="link" method="post">
        <input class="link" type="submit" value="<?php echo $this->fname ?>">
        <input type="hidden" name="act" value="topics">
        <input type="hidden" name="fid" value="<?php echo $this->fid ?>">
    </form>
<?php   
}
    
if (in_array($this->act, array('posts', 'postadd', 'postedit', 'post'))) {
?>
        <span class="link">&nbsp;&#187;&nbsp;</span>
        <form class="link" method="post">
            <input class="link" type="submit" value="<?php echo $this->tsubj ?>">
            <input type="hidden" name="act" value="posts">
            <input type="hidden" name="tid" value="<?php echo $this->tid ?>">
        </form>
<?php
}   

if ($this->act == 'topicadd') {
?>
    <span class="link">&nbsp;&#187;&nbsp;</span>
    <form class="link" method="post">
        <input class="link" type="submit" value="<?php echo POSTTOPIC ?>">
        <input type="hidden" name="act" value="topicadd">
        <input type="hidden" name="fid" value="<?php echo $this->fid ?>">
    </form>
<?php
}

if ($this->act == 'postadd') {
?>
    <span class="link">&nbsp;&#187;&nbsp;</span>
    <form class="link" method="post">
        <input class="link" type="submit" value="<?php echo POSTREPLY ?>">
        <input type="hidden" name="act" value="postadd">
        <input type="hidden" name="tid" value="<?php echo $this->tid ?>">
    </form>
<?php
}

if ($this->act == 'postedit') {
?>
    <span class="link">&nbsp;&#187;&nbsp;</span>
    <form class="link" method="post">
        <input class="link" type="submit" value="<?php echo POSTEDIT ?>">
        <input type="hidden" name="act" value="postedit">
        <input type="hidden" name="pid" value="<?php echo $this->pid ?>">
    </form>
<?php
}

if ($this->act == 'post') {
?>
    <span class="link">&nbsp;&#187;&nbsp;</span>
    <form class="link" method="post">
        <input class="link" type="submit" value="<?php echo POST ?>">
        <input type="hidden" name="act" value="post">
        <input type="hidden" name="pid" value="<?php echo $this->pid ?>">
    </form>
<?php
}

?>
        </div>
        <br>

<?php
        return;
    }
    
}

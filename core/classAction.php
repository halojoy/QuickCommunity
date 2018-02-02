<?php if(!defined('QCOM1'))exit();

class Action
{
    public $pdo;
    public $sess;
    public $fora;
    public $adm;
    public $scope;
    public $act;
    public $fid   = false;
    public $tid   = false;
    public $pid   = false;
    public $fname = false;
    public $tsubj = false;

    public function __construct($pdo, $sess, $fora, $adm, $scope)
    {
        $this->pdo   = $pdo;
        $this->sess  = $sess;
        $this->fora  = $fora;
        $this->adm   = $adm;
        $this->scope = $scope;

        if (isset($_POST['act']))
            $this->act = $_POST['act'];
        elseif (isset($_GET['act']))
            $this->act = $_GET['act'];
        else
            $this->act = 'home';

        $this->prepArguments();

        return;
    }

    public function executeAction()
    {
        if ($this->scope == 'index')
            $this->validAction();
        if ($this->scope == 'admin')
            $this->validAdmin();
        return;     
    }

    public function validAction()
    {
        switch ($this->act):
            case 'home':
                $this->fora->forums();
                break;
            case 'members':
                $this->fora->showMembers();
                break;
            case 'post':
                $this->fora->post($this->pid, $this->tsubj);
                break;
            case 'postadd':
                $this->fora->postadd($this->fid, $this->fname, $this->tid,
                        $this->tsubj);
                break;
            case 'postedit':
                $this->fora->postedit($this->pid, $this->tsubj);
                break;
            case 'posts':
                $this->fora->posts($this->tid, $this->tsubj);
                break;
            case 'topicadd':
                $this->fora->topicadd($this->fid, $this->fname);
                break;
            case 'topics':
                $this->fora->topics($this->fid);
                break;
            case 'topicsnew':
                $this->fora->topicsnew();
                break;
            case 'login':
                $this->sess->submitLogin();
                break;
            case 'logout':
                $this->sess->Logout();
                break;
            case 'register':
                $this->sess->submitRegister();
                break;
            default:
                $this->fora->forums();
                break;
        endswitch;
    }

    public function validAdmin()
    {
        switch ($this->act):
            case 'home':
                $this->adm->adminhome();
                break;
            case 'forumadd':
                $this->adm->forumadd();
                break;
            case 'forumorder':
                $this->adm->forumorder();
                break;
            case 'styles':
                $this->adm->styles();
                break;
            case 'languages':
                $this->adm->languages();
                break;
            case 'timezones':
                $this->adm->timezones();
                break;
            case 'members':
                $this->adm->members();
                break;
            case 'postdelete':
                $this->adm->postdelete();
                break;
            case 'topicdelete':
                $this->adm->topicdelete();
                break;
            case 'forumdelete':
                $this->adm->forumdelete();
                break;
            case 'forumrename':
                $this->adm->forumrename();
                break;
            case 'sendmail':
                $this->adm->sendmail();
                break;
            default:
                $this->adm->adminhome();
                break;
        endswitch;      
    }

    public function prepArguments()
    {
        switch($this->act):
            case 'topics':
            case 'topicadd':
                if (isset($_POST['fid'])) $this->fid = $_POST['fid'];
                elseif (isset($_GET['fid'])) $this->fid = $_GET['fid'];
                else {
                    $this->act = 'home';
                    return;
                }
                $this->fname = $this->pdo->forumName($this->fid);
                if (!$this->fname) {
                    $this->act = 'home';
                    return;
                }
                break;
            case 'posts':
            case 'postadd':
                if (isset($_POST['tid'])) $this->tid = $_POST['tid'];
                elseif (isset($_GET['tid'])) $this->tid = $_GET['tid'];
                else {
                    $this->act = 'home';
                    return;
                }
                $row = $this->pdo->getTopic($this->tid);
                if (!$row) {
                    $this->act = 'home';
                    return;
                }
                $this->fid = $row->t_fid; $this->fname = $row->t_fname;
                $this->tsubj = $row->t_subject;
                break;
            case 'post':
            case 'postedit':
                if (isset($_POST['pid'])) $this->pid = $_POST['pid'];
                elseif (isset($_GET['pid'])) $this->pid = $_GET['pid'];
                else {
                    $this->act = 'home';
                    return;
                }
                $row = $this->pdo->getPost($this->pid);
                if (!$row) {
                    $this->act = 'home';
                    return;
                }
                $this->fid = $row->p_fid; $this->fname = $row->p_fname; 
                $this->tid = $row->p_tid; $this->tsubj = $row->p_tsubj;
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

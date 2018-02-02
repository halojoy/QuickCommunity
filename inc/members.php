<?php if(!defined('QCOM1'))exit();

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

$this->fora->showMembers();

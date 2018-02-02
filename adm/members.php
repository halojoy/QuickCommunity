<?php if(!defined('QCOM1'))exit();

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

$this->adm->members();

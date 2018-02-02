<?php if(!defined('QCOM1'))exit();

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

$this->sess->submitLogin();

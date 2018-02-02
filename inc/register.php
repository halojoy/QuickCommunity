<?php if(!defined('QCOM1'))exit();

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

if (isset($_GET['ucode'])) {
    $this->sess->activate($_GET['ucode']);
}

$this->sess->submitRegister();

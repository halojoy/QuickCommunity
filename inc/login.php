<?php if(!defined('QCOM1'))exit() ?>
<?php

if ($this->sess->isLogged()) {
    header('location:./');
    exit();
}

$this->sess->submitLogin();

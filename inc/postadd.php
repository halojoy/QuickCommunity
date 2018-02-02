<?php if(!defined('QCOM1'))exit();

if (!$this->sess->isLogged() || $this->sess->isBanned()) {
    header('location:./');
    exit();
}

$this->fora->postadd($this->fid, $this->fname, $this->tid, $this->tsubj);

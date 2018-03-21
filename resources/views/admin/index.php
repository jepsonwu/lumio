<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/main-temp');
?>

    我的邀请码：<?= $params['user']->inviteCode ?>
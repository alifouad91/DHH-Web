<?php
defined('C5_EXECUTE') or die('Access Denied.');

$subject = t('Forgot Password');
ob_start();
    Loader::element('mail/wrapper', array('mh' => $this, 'template' => 'forgot_password','uName' => $uName,
        'changePassURL' => $changePassURL));
$bodyHTML = ob_get_clean();

<?php
defined('C5_EXECUTE') or die('Access Denied.');

ob_start();
    Loader::element('mail/wrapper', array('mh' => $this, 'template' => 'print_booking_confirmed','booking' => $booking));
$bodyHTML = ob_get_clean();

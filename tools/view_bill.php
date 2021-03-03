<?php

$uh     = Loader::helper('util');
$billID = $_GET['id'];
if (!$billID) {
    exit();
}
$billID = (int)$uh->decrypt($_GET['id'], BILL_KEY);
$bill   = Bill::getByID($billID);
if ($bill) {
    header('Location: ' . $bill->getPDFPath());
}
exit();
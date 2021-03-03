<?php
/* @var string $uEmail */
/* @var string $uHash */
defined('C5_EXECUTE') or die(_('Access Denied.'));
$uh = Loader::helper('util');

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>

<p>Bill No <?php echo $billID; ?>,</p>
<p>Please find the bill for <?php echo e(SITE); ?>: <a href="<?= BASE_URL.View::url('/tools/view_bill?id=' . $uh->encrypt($billID, BILL_KEY)); ?>">Here</a></p>
<div class="bill"></div>
<p>Thank you!</p>
<p>
    Regards,<br>
    <?php echo e(SITE); ?>
</p>

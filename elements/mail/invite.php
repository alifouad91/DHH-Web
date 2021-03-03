<?php
/* @var string $uEmail */
/* @var string $uHash */
defined('C5_EXECUTE') or die(_('Access Denied.'));
?>
<div style="width: 592px;margin: auto;">
    <p>You have been referred by <?php echo $userName; ?></p>
    <p>Please click the link below and register now to avail a referral credit of <?php echo Config::get('REFERRAL_CREDIT'); ?> AED.
        Referral credits will be calculated at booking confirmation for your first booking.</p>
    <p><a href="<?php echo $registerLink; ?>" target="_blank"></a><?php echo $registerLink; ?></p>
    <p>Thank you!</p>
    <p>
        Regards,<br>
        <?php echo e(SITE); ?>
    </p>
</div>
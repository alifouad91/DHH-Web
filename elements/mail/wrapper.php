<?php
/* @var MailHelper $mh */
/* @var string $template */
/* @var string $pkgHandle */
defined('C5_EXECUTE') or die(_('Access Denied.'));

if (!isset($pkgHandle)) {
    $pkgHandle = null;
}
$parameters = $mh->getParameters();
$uName = $parameters['uName'];
$theme = PageTheme::getSiteTheme();
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo e(SITE); ?></title>
    <link rel="stylesheet" href="https://use.typekit.net/qip4cyd.css">
</head>
<body style="background-color: #f8f8f9; -webkit-font-smoothing: antialiased;font-family:'Open Sans', sans-serif; font-size:12px; line-height:22px; margin:0; padding:0; color:#333;">
<table width="640" border="0" cellspacing="0" style="border: 0 none; background-color: #fff; margin: auto;">
    <tr style="border: 0 none;">
        <td height="67">
            <a href="<?php echo BASE_URL; ?>" target="_blank">
                <!-- <img style="display:block;margin-left:24px; margin-top: 25px; height: 42px;" src="<?php //echo BASE_URL . $theme->getThemeURL() . '/src/images/logo-email.png'; ?>" alt="<?php //echo e(SITE); ?>"/> -->
                <img style="display:block;margin-left:24px; margin-top: 25px; height: 42px;" src="https://driven-holiday-homes.1020dev.com/themes/dhh/src/images/logo-email.png" alt="<?php echo e(SITE); ?>"/>
                
            </a>
        </td>
    </tr>
    <?php if($template != 'validate_user_email' && $template != 'print_booking_confirmed') {?>
        <tr style="border: 0 none;">
            <td>
                <p style=" color: #000000; font-family: Roboto; font-size: 15px; line-height: 24px; text-align: left; margin: 15px 15px 15px;">
                    Hi <?php echo $uName; ?>,
                </p>
            </td>
        </tr>
    <?php } ?>
    <tr style="border: 0 none;">
        <td>
            <div style="padding: 37px 0 27px;">
                <?php Loader::element('mail/' . $template, $mh->getParameters(), $pkgHandle); ?>
            </div>
        </td>
    </tr>

    
    <tr style="border: 0 none;">
        <td>
            <div>
                <?php Loader::element('mail/footer', ['template' => $template]);?>
            </div>
        </td>
    </tr>
</table>
</body>
</html>

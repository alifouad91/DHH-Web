<?php
/* @var View $this */
/* @var Page $c */
/* @var string $bodyClass */
defined('C5_EXECUTE') or die(_('Access Denied.'));

global $u;

$page = $c;

$pageType = (string) $page->getAttribute('page_type');
if (!$pageType) {
	$pageType = 'default';
}

if (!$bodyClass) {
	$bodyClass = '';
}
$bodyClass .= ' ' . $pageType . '-page';
if (User::isLoggedIn()) {
	$bodyClass .= ' logged-in';
}
if ($page->isEditMode()) {
	$bodyClass .= ' edit-mode';
}
if ($u->isAdmin()) {
	$bodyClass .= ' admin-user';
}

$token    = new Concrete5_Helper_Validation_Token();
?>
<!DOCTYPE html>
<!--[if lte IE 8]> <html lang="en-us" class="ie10 ie9 ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en-us" class="ie10 ie9"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en-us"> <!--<![endif]-->
<head>
    <?php Loader::element('header_required', array('noOutput' => true, 'property' => $property));?>
    <?php $this->inc('elements/head.php');?>
</head>
<body class="<?php echo $bodyClass; ?>">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MXBH59S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="site-loader">
    <div class="logo-middle">
        <img src="<?php echo $this->getThemePath(); ?>/dist/images/dhh-logo.svg" alt="<?php echo e(SITE); ?>"/>
    </div>
</div>
<script>
    if (document.cookie.indexOf("visited=") == -1) {
        setCookie("visited", "1");
        $('.site-loader').show();
    }
</script>
<div class="wrapper">
    <?php $this->inc('elements/header.php');?>
    <?php $this->inc('elements/main.php');?>
    <!-- This div is important to have in order to push the footer at the bottom -->
    <!-- <div class="push"></div> -->
    <?php $this->inc('elements/footer.php');?>
</div>
<?php $this->inc('elements/scripts.php');?>
<?php Loader::element('footer_required');?>

<?php echo '<input type="hidden" id="api-token-csrf" data-type="token" name="token" value="' . $token->generate('api') . '" />'; ?>
</body>
</html>
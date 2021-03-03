<?php
/* @var View $this */
/* @var Page $c */
/* @var string $bodyClass */
defined('C5_EXECUTE') or die(_('Access Denied.'));

global $u;

$page = $c;

$pageClass = $page->getCollectionTypeHandle();
if (!$pageClass) {
	$pageClass = $page->getCollectionHandle();
}
$pageClass .= '-view-page';

if (!$bodyClass) {
	$bodyClass = '';
}
$bodyClass .= ' ' . $pageClass;
if (User::isLoggedIn()) {
	$bodyClass .= ' logged-in';
}
if ($page->isEditMode()) {
	$bodyClass .= ' edit-mode';
}
if ($u->isAdmin()) {
	$bodyClass .= ' admin-user';
}

$mobile = new Mobile_Detect();
$isMobile = $mobile->isTablet() || $mobile->isMobile();
if ($isMobile) {
	$bodyClass .= ' is-touch-device';
}

$token    = new Concrete5_Helper_Validation_Token();

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en-us" class="ie10 ie8"> <![endif]-->
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
<div class="wrapper">
    <!-- Wrap all page content here -->
    <?php $this->inc('elements/header.php');?>
    <?php Loader::element('system_errors', array('error' => $error));?>
    <?php print $innerContent;?>
    <!-- This div is important to have in order to push the footer at the bottom -->
    <!-- <div class="push"></div> -->
</div>
<?php $this->inc('elements/footer.php');?>
<?php $this->inc('elements/scripts.php');?>
<?php Loader::element('footer_required');?>
<?php echo '<input type="hidden" data-type="token" name="token" value="' . $token->generate('api') . '" />'; ?>
</body>
</html>

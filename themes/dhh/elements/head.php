<?php
/** @var View $this */
defined('C5_EXECUTE') or die(_("Access Denied."));
/** @var HtmlHelper $htmlHelper */
$htmlHelper  = Loader::helper('html');
$page        = $c;
$title       = $page->getCollectionName();
// $description = $page->getCollectionDescription();
$description = $page->getCollectionAttributeValue('meta_description');

if ($property && ($property instanceof Property)) {
	$property_thumbnail = Property::getImagePath($property->getThumbnail(), 350, 350, false, 'img-size');
	$title              = $property->getName();
	$description        = $property->getDescription();
}

$image = BASE_URL . $this->getThemePath() . '/dist/images/logo.svg';
if ($property_thumbnail) {
	$image = $property_thumbnail;
}

?>
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="author" content="TenTwenty | Webdesign, Webshops & E-marketing | Dubai">

<!-- Meta Tags for Social Media -->
<meta property="og:site_name" content="<?php echo SITE; ?>">
<!-- Below tag will be used for android mobile browser colors, change it to main logo color of the project -->
<meta name="theme-color" content="#393939">
<meta property="og:image" content="<?php echo $image; ?>">
<meta property="og:title" content="<?php echo SITE; ?> | <?php echo $title; ?>">
<meta property="og:description" content="<?php echo $description; ?>">
<meta name="twitter:title" content="<?php echo SITE; ?> | <?php echo $title; ?>">
<meta name="twitter:image" content="<?php echo $image; ?>">
<meta name="twitter:description" content="<?php echo $description; ?>">
<meta name="twitter:card" content="summary_large_image"/>

<link rel="stylesheet" href="<?php echo $this->getThemePath() . '/dist/css/app.min.css'; ?>">
<link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
<?php
print $this->controller->outputHeaderItems();
$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && $_trackingCodePosition === 'top') {
	echo Config::get('SITE_TRACKING_CODE');
}
echo (is_object($c)) ? $c->getCollectionAttributeValue('header_extra_content') : '';
?>

<!--[if lt IE 9]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!--[if !IE]><!-->
<script>
    if (/*@cc_on!@*/false) {
        document.documentElement.className += ' ie10';
    }
</script>
<!--<![endif]-->

<script>
    //set cookie for site
    function setCookie(cname, cvalue) {
        var d = new Date();
        d.setTime(d.getTime() + 2160000000);
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MXBH59S');</script>
<!-- End Google Tag Manager -->

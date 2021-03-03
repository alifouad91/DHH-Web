<?php
/* @var View $this */
/* @var Page $c */
defined('C5_EXECUTE') or die(_('Access Denied.'));
$ih = Loader::helper('image');
$page = $c;
$pageType = $page->getAttribute('page_type');
$page_image = $page->getAttribute('page_image');
$showHomePageOptions = $page->getAttribute('show_homepage_options');
$switchLogoArr = ['home', 'blog', 'login', 'register', 'become-host'];
$user = new User();
$ui = UserInfo::getByID($user->uID);
if ($ui) {
    
	$bookingCount = $ui->getBookingCount();
	$reviewCount = $ui->getReviewCount();
	$favouriteCount = $ui->getFavouriteCount();
	$fullName = $ui->getFullName();
	$badge = $ui->getBadge();
	$avatar = $ui->getAvatar();
    $referralCode = $ui->getUniqueToken();
    $phone = $ui->getPhone();
    $email = $ui->uEmail;
	if ($user->isLandLord() || $user->isSuperUser()) {
		$userGroup = 'landlord';
	} else {
		$userGroup = 'regular';
	}
}
$mobile = new Mobile_Detect();
$isMobile = $mobile->isTablet() || $mobile->isMobile();
$whatsAppLink = $isMobile ? 'https://wa.me/+971509643016' : 'https://web.whatsapp.com/send?phone=971509643016&text=&source=&data=';

$cPath = $c->getCollectionPath();
$uh = Loader::helper('util');
?>

<?php 
$token    = new Concrete5_Helper_Validation_Token();

echo '<input type="hidden" id="api-token-csrf" data-type="token" name="token" value="' . $token->generate('api') . '" />'; ?>

<!-- Banner Images - Multi-Image attribute -->
<?php //$bannerImage = $page->getAttribute('banner_image'); ;;;?>

<div class="mobile-nav">
</div>
<header data-group="<?php echo $userGroup; ?>" class="header <?php echo $isMobile ? 'site-is-mobile' : ''; ?>  <?php echo $pageType; ?> <?php echo User::isLoggedIn() ? 'logged-in' : ''; ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-4 flex-center-left-horizontal">
                <a class="header__logo" href="<?php echo View::url('/'); ?>">
                    <img src="<?php echo $this->getThemePath(); ?>/dist/images/logo.svg" alt="<?php echo e(SITE); ?>"/>
                    <?php if (in_array($pageType, $switchLogoArr) && !$isMobile) {?>
                    <!-- <h6>Driven<br/>Holiday<br/>Homes</h6>
                    <h5>Best<br/>Places and<br/>Prices</h5> -->
                    <h6>Driven Holiday Homes</h6>
                    <?php }?>
                </a>
                <?php if (!in_array($pageType, $switchLogoArr)) {?>
                    <span class="ant-input-affix-wrapper ant-input-affix-wrapper-lg">
                        <span class="ant-input-prefix"><i aria-label="icon: search" class="anticon anticon-search" style="color: rgba(0, 0, 0, 0.4);"><svg viewBox="64 64 896 896" class="" data-icon="search" width="1em" height="1em" fill="currentColor" aria-hidden="true"><path d="M909.6 854.5L649.9 594.8C690.2 542.7 712 479 712 412c0-80.2-31.3-155.4-87.9-212.1-56.6-56.7-132-87.9-212.1-87.9s-155.5 31.3-212.1 87.9C143.2 256.5 112 331.8 112 412c0 80.1 31.3 155.5 87.9 212.1C256.5 680.8 331.8 712 412 712c67 0 130.6-21.8 182.7-62l259.7 259.6a8.2 8.2 0 0 0 11.6 0l43.6-43.5a8.2 8.2 0 0 0 0-11.6zM570.4 570.4C528 612.7 471.8 636 412 636s-116-23.3-158.4-65.6C211.3 528 188 471.8 188 412s23.3-116.1 65.6-158.4C296 211.3 352.2 188 412 188s116.1 23.2 158.4 65.6S636 352.2 636 412s-23.3 116.1-65.6 158.4z"></path></svg></i></span>
                        <input id="header-search" placeholder="Search by area name" maxlength="1000" type="text" class="ant-input ant-input-secondary" value="">
                    </span>
                <?php }?>
                <?php if ($isMobile) {?>
                    <div class="mobile-menu-icon ">
                        <svg class="ham ham6" viewBox="0 0 100 100" width="56" >
                            <path
                                class="line top"
                                d="m 30,33 h 40 c 13.100415,0 14.380204,31.80258 6.899646,33.421777 -24.612039,5.327373 9.016154,-52.337577 -12.75751,-30.563913 l -28.284272,28.284272" />
                            <path
                                class="line middle"
                                d="m 70,50 c 0,0 -32.213436,0 -40,0 -7.786564,0 -6.428571,-4.640244 -6.428571,-8.571429 0,-5.895471 6.073743,-11.783399 12.286435,-5.570707 6.212692,6.212692 28.284272,28.284272 28.284272,28.284272" />
                            <path
                                class="line bottom"
                                d="m 69.575405,67.073826 h -40 c -13.100415,0 -14.380204,-31.80258 -6.899646,-33.421777 24.612039,-5.327373 -9.016154,52.337577 12.75751,30.563913 l 28.284272,-28.284272" />
                        </svg>
                    </div>
                <?php }?>
            </div>
            <?php if (!$isMobile) {?>
            <div class="col-xs-12 col-sm-8 desktop-menu <?php echo User::isLoggedIn() ? 'logged-in' : ''; ?>">
                <?php if (!in_array($pageType, ['login', 'register', 'become-host'])) {?>
                    <?php if ($isMobile) {?>
                        <div class="header__logo mobile">
                            <h6>Driven<br/>Holiday<br/>Homes</h6>
                            <h5>Best<br/>Places and<br/>Prices</h5>
                        </div>
                    <?php }?>
                    <ul class="header__nav">
                        <li><a class="<?php echo $uh->getActiveNav('/event-rentals'); ?>" href="<?php echo View::url('/event-rentals'); ?>">Event Rentals</a></li>
                        <li><a class="<?php echo $uh->getActiveNav('/properties?monthly=true'); ?>" href="<?php echo View::url('/properties?monthly=true'); ?>">Monthly Rentals</a></li>
                        <li><a class="<?php echo $uh->getActiveNav('/blog'); ?>" href="<?php echo View::url('/blog'); ?>">Blog</a></li>
                        <li><a class="<?php echo $uh->getActiveNav('/list-property'); ?>" href="<?php echo View::url('/list-property'); ?>">List a Property</a></li>
                        <div class="ant-divider ant-divider-vertical"></div>
                        <li id="navigation__form"
                        data-currentcurrency="<?php echo App::getSessionLocale(); ?>"
                        data-bcount="<?php echo $bookingCount; ?>"
                        data-rcount="<?php echo $reviewCount; ?>"
                        data-fcount="<?php echo $favouriteCount; ?>"
                        data-name="<?php echo $fullName; ?>"
                        data-group="<?php echo $userGroup; ?>"
                        data-avatar="<?php echo $avatar; ?>"
                        data-badge="<?php echo $badge; ?>"
                        data-phone="<?php echo $phone; ?>"
                        data-email="<?php echo $email; ?>"
                        data-loggedin="<?php echo User::isLoggedIn(); ?>"></li>
                        <?php if (User::isLoggedIn()) {?>

                        <?php } else {?>
                            <li class="pr-0 m-0">
                                <div class="navigation__form__buttons">
                                    <a href="<?php echo View::url('/login'); ?>"><button class="ant-btn ant-btn-primary"><span>Login</span></button></a>
                                    <a href="<?php echo View::url('/register'); ?>"><button class="ant-btn ant-btn-secondary"><span>Register</span></button></a>
                                </div>
                            </li>
                        <?php }?>
                    </ul>
                <?php } else {?>
                    <ul class="header__nav">
                        <?php if ($pageType == 'login') {?>
                        <li><span class="register-label">Don't have an account?</span><a href="<?php echo View::url('/register'); ?>"><button class="ant-btn ant-btn-primary"><span>Register</span></button></a></li>
                        <?php } else if ($pageType == 'register') {?>
                        <li><span class="register-label">Already have an account?</span><a href="<?php echo View::url('/login'); ?>"><button class="ant-btn ant-btn-primary"><span>Login</span></button></a></li>
                        <?php } else if ($pageType == 'become-host') {?>
                        <li><span class="register-label">Already a host?</span><a href="<?php echo View::url('/login'); ?>"><button class="ant-btn ant-btn-primary"><span>Login</span></button></a></li>
                        <?php }?>
                    </ul>
                <?php }?>
            </div>
            <?php }?>
        </div>
    </div>
</header>
<?php if ($isMobile) {?>
<div class=" mobile-menu <?php echo $isMobile ? 'is-mobile' : ''; ?> <?php echo User::isLoggedIn() ? 'logged-in' : ''; ?>">
        <div class="header__logo mobile">
            <h6>Driven<br/>Holiday<br/>Homes</h6>
            <h5>Best<br/>Places and<br/>Prices</h5>
        </div>
        <ul class="header__nav <?php echo User::isLoggedIn() ? 'logged-in' : ''; ?>">
            <?php if (User::isLoggedIn()) {?>
                <li><a class="<?php echo $uh->getActiveNav('/profile'); ?>" href="<?php echo View::url('/profile'); ?>">Profile</a></li>
                <li id="mobile-notifications"></li>
                <?php if ($userGroup != 'landlord') {?>
                    <li>
                        <a class="<?php echo $uh->getActiveNav('/profile/mybookings'); ?>" href="<?php echo View::url('/profile/mybookings'); ?>">My Bookings</a>
                        <span><?php echo $bookingCount; ?></span>
                    </li>
                    <li>
                        <a class="<?php echo $uh->getActiveNav('/profile/myreviews'); ?>" href="<?php echo View::url('/profile/myreviews'); ?>">My Reviews</a>
                        <span><?php echo $reviewCount; ?></span>
                    </li>
                    <li>
                        <a class="<?php echo $uh->getActiveNav('/profile/favourites'); ?>" href="<?php echo View::url('/profile/favourites'); ?>">Favourites</a>
                        <span><?php echo $favouriteCount; ?></span>
                    </li>
                <?php } else {?>
                    <li>
                        <a class="<?php echo $uh->getActiveNav('/profile/my-properties'); ?>" href="<?php echo View::url('/profile/my-properties'); ?>">My Properties</a>
                    </li>
                    <li>
                        <a class="<?php echo $uh->getActiveNav('/profile/finances'); ?>" href="<?php echo View::url('/profile/finances'); ?>">Finances</a>
                    </li>
                    <li>
                        <a class="<?php echo $uh->getActiveNav('/profile/property-reviews'); ?>" href="<?php echo View::url('/profile/property-reviews'); ?>">Property Reviews</a>
                    </li>
                <?php }?>
                <div class="ant-divider ant-divider-vertical"></div>
            <?php }?>
            <li><a class="<?php echo $uh->getActiveNav('/event-rentals'); ?>" href="<?php echo View::url('/event-rentals'); ?>">Event Rentals</a></li>
            <li><a class="<?php echo $uh->getActiveNav('/properties?monthly=true'); ?>" href="<?php echo View::url('/properties?monthly=true'); ?>">Monthly Rentals</a></li>
            <li><a class="<?php echo $uh->getActiveNav('/blog'); ?>" href="<?php echo View::url('/blog'); ?>">Blog</a></li>
            <li><a class="<?php echo $uh->getActiveNav('/list-property'); ?>" href="<?php echo View::url('/list-property'); ?>">List a Property</a></li>
            <!-- <div class="ant-divider ant-divider-vertical"></div> -->
            <div class="mobile__curency" data-currentcurrency="<?php echo App::getSessionLocale(); ?>">
            </div>
            <li class="pr-0 m-0">
                    <div class="navigation__form__buttons">
                    <?php if (User::isLoggedIn()) {?>
                        <a href="<?php echo View::url('/login/logout'); ?>"><button class="ant-btn ant-btn-secondary"><span>Logout</span></button></a>
                    <?php } else {?>
                        <a href="<?php echo View::url('/login'); ?>"><button class="ant-btn ant-btn-primary"><span>Login</span></button></a>
                        <a href="<?php echo View::url('/register'); ?>"><button class="ant-btn ant-btn-secondary"><span>Register</span></button></a>
                    <?php }?>
                </div>
            </li>
        </ul>
</div>
<?php }?>
<?php if (in_array($pageType, ['home'])) {
	$properties = new PropertyList();
	$properties = $properties->get();
	$count = count($properties);
	?>
<div class="header__banner header__banner__primary" id="banner" <?php if ($page_image && !$isMobile) {?>
    
    <?php }?>>
    <?php if (!$isMobile) {?>
    <div class="vimeo-wrapper">
    <video id="vimeo-video" src="https://player.vimeo.com/external/349842215.hd.mp4?s=a2814990657b038b3a43f27b20c699ddb1da8164&profile_id=174"></video>
        <!-- <iframe src="https://player.vimeo.com/video/349842215?background=1&autoplay=1&loop=1&byline=0&title=0"
            frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe> -->
    </div>
    <?php } ?>
    <div class="page__limit">
        <h1 class="text-reveal"><span class="<?php echo $isMobile ? '' : 'tk-pacifico'; ?>">Book the coziest places in Dubai</span></h1>
        <div id="banner-form">
            <?php Loader::element('form_skeleton');?>
        </div>
        <?php if($showHomePageOptions) { ?>
        <span class="sub-header-2"><?php echo $count; ?> options of cozy<br />places in Dubai</span>
        <?php } ?>
    </div>
</div>
<?php }?>

<?php if (in_array($pageType, ['listing', 'rental'])) {
	?>
<div class="<?php echo $pageType; ?>  header__banner header__banner__secondary" id="listing-banner"
    <?php if ($page_image) {?>
    style="background-image: url(<?php echo $page_image->getURL(); ?>)"
    <?php }?>
>
    <div class="page__limit">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-1 header-title" >
                    <?php $a = new Area('Header Title');
	$a->display($c);?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-5 col-md-offset-1" data-aos="fade-left">
                    <?php if ($pageType == 'listing') {?><div id="listing-form" data-link="<?php echo $whatsAppLink; ?>"></div><?php }?>
                    <?php if ($pageType == 'rental') {
		?>
                        <div class="general__card general__card__small general__card__rental" data-aos="fade-left">
                            <?php
                                $stack = Stack::getByName('Form - Rental');
                                $stack->display();
                            ?>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }?>
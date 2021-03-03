<?php
/* @var View $this */
/* @var Page $c */
defined('C5_EXECUTE') or die(_('Access Denied.'));

$page = $c;
$pageType = $page->getAttribute('page_type');
$switchFooter = ['login', 'register', 'become-host'];
// $hideFooter = ['properties'];

// $switchFooter = [];
$hideFooter = [];
$mobile = new Mobile_Detect();
$isMobile = $mobile->isTablet() || $mobile->isMobile();
$whatsAppLink = $isMobile ? 'https://wa.me/+971528654208' : 'https://web.whatsapp.com/send?phone=971528654208&text=&source=&data=';
?>
<div id="login-popup-form">
    <?php Loader::element('login_form');?>
</div>

<div id="app-currency" data-currency="<?php echo App::getSessionLocale(); ?>"></div>
<?php if (!in_array($pageType, $hideFooter)) {?>
<footer class="footer ">
    <div class="container-fluid page__limit">
        <?php if (!in_array($pageType, $switchFooter)) {?>
        <div class="row footer__items">
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 dhh-logo">
                <a class="header__logo" href="<?php echo View::url('/'); ?>">
                    <img src="<?php echo $this->getThemePath(); ?>/dist/images/logo.svg" alt="<?php echo e(SITE); ?>"/>
                    <h5>Best<br/>Places and<br/>Prices</h5>
                </a>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-2 col-lg-2 navigation">
                <span class="sub-header-2">Driven Holiday Homes</span>
             
                <?php $a = new GlobalArea('Footer Links'); $a->display($c); ?>
            </div>
            <div class="col-md-2 col-lg-2 external-link">
                <!-- <span class="sub-header-2">Currency</span>
                <ul>
                    <li><a>USD</a></li>
                </ul> -->
                <span class="sub-header-2">We're at</span>
                <?php $a = new GlobalArea('Footer Links 2'); $a->display($c); ?>
               
            </div>
            <?php if ($isMobile) {?>
            <div class="mobile__curency" data-currentcurrency="<?php echo App::getSessionLocale(); ?>">
            </div>
            <?php }?>
            <div class="col-md-3 col-lg-3 landlord">
                <span class="sub-header-2">Are you a property owner?</span>
                <a href="<?php echo View::url('/become-host'); ?>">
                    <button type="button" class="ant-btn ant-btn-secondary"><span>List Property</span></button>
                </a>
            </div>
            <div class="col-md-1 col-lg-1 follow">
                <span class="sub-header-2">Follow Us?</span>
                <?php $a = new GlobalArea('Footer Links Social Media'); $a->display($c); ?>
            </div>
            <div class="col-md-2 col-lg-2 contact">
                <span class="sub-header-2">Contact Us</span>
                <?php $a = new GlobalArea('Footer Links Contact'); $a->display($c); ?>
            </div>
        </div>
        <div class="ant-divider ant-divider-horizontal"></div>
        <?php }?>
        <div class="row footer__bottom_items">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                <span class="copyright">Copyright © <?php echo date("Y"); ?> Driven Holiday Homes™. All rights reserved.</span>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-8 tnc">

                    <!-- <a href="<?php //echo View::url('/terms-and-policies'); ?>" class="terms"></a>  -->
                    <a href="<?php echo View::url('/privacy-policy'); ?>" class="terms">Privacy & Policies</a>
                
                
                 <div class="by-tentwenty" style="display: none;">
                    <a href="https://www.tentwenty.me/" target="_blank">
                        <img src="<?php echo $this->getThemePath(); ?>/dist/images/by-tentwenty-dark.png" alt="<?php echo e(SITE); ?>"/>
                    </a>
                 </div>
            </div>
        </div>
    </div>
</footer>
<?php } ?>
<!-- Go to top button -->
<div id="gotoTop">
    <span> Back to top</span>
</div>

<div class="sticky-icon">
    <a href="<?php echo $whatsAppLink; ?>" target="_blank">
        <img src="<?php echo $this->getThemePath(); ?>/dist/images/whatsapp.png" alt="<?php echo e(SITE); ?>"/>
    </a>
</div>
<!-- For Landscape Alert -->
<!-- <div class="landscape-alert">
    <p>For better web experience, please use the website in portrait mode</p>
</div> -->
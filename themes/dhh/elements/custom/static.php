<?php
/* @var View $this */
/* @var Page $c */
defined('C5_EXECUTE') or die(_('Access Denied.'));

$page = $c;
$pageType = $page->getAttribute('page_type');
$grid = $pageType == 'contact' ? 'col-lg-4 col-lg-offset-2' : 'col-lg-6';

$mobile = new Mobile_Detect();
$isMobile = $mobile->isTablet() || $mobile->isMobile();
$whatsAppLink = $isMobile ? 'https://wa.me/+971509643016' : 'https://web.whatsapp.com/send?phone=971509643016&text=&source=&data=';

?>
<div class="page__section container">
  <div class="page__section__header">
    <h1><?php echo $page->getCollectionName(); ?></h1>
    <span class="sub-text"><?php echo $page->getCollectionDescription(); ?></span>
  </div>

  <?php if(in_array($pageType, ['behavior', 'faq'])) { ?>
    <div id="<?php echo 'tab-'.$pageType; ?>" class="tabs__static tabs__<?php echo $pageType;?>"></div>
    <div class="tabs__concrete">
      <?php
        $this->inc('elements/tabs/'.$pageType.'.php');
      ?>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-9 col-lg-offset-2">
          <?php
              $stack = Stack::getByName('Banner - Visit Help Center');
              $stack->display();
          ?>
        </div>
      </div>
    </div>
  <?php } ?>

  <div class="page__section__body">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6 pl-0">
          <?php $a = new Area('Content Left'); $a->display($c); ?>
        </div>
        <div class="<?php echo $grid; ?> pr-0">
          <?php $a = new Area('Content Right'); $a->display($c); ?>
        </div>
      </div>
    </div>
    <?php if($pageType == 'contact') { ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6 contact-padding pl-0">
          <h3>Get In Touch</h3>
          <div class="general__card general__card__small">
            <!-- TODO: Make this text area -->
            <p class="general__card__title"> We are always happy to hear proposals & to get feedback</p>
            <?php
                $stack = Stack::getByName('Form - Contact');
                $stack->display();
            ?>
          </div>
        </div>
        <div class="col-lg-6 contact-padding pr-0">
          <h3>Agent Support</h3>
          <div class="general__card general__card__small p-0">
            <a href="<?php echo $whatsAppLink; ?>" target="_blank">
              <img src="<?php echo $this->getThemePath(); ?>/dist/images/start_chat.png" alt="<?php //echo e(SITE); ?>"/>
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
    <?php if($pageType == 'about') { ?>
      <!-- <div class="container-fluid page__team">
        <div class="row">
          <h3>Meet the team</h3>
          <?php //$a = new Area('Team Thumbnail'); $a->display($c); ?>
        </div>
      </div> -->
    <?php } ?>
  </div>

  <?php $a = new Area('Extra Page Content'); $a->display($c); ?>
</div>
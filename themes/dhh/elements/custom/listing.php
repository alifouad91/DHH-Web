<?php
/* @var View $this */
/* @var Page $c */
defined('C5_EXECUTE') or die(_('Access Denied.'));

$page = $c;
$pageType = $page->getAttribute('page_type');
$page_image = $page->getAttribute('page_image');
?>
<div class="page__listing <?php echo $pageType;?>">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-5 col-md-offset-1">
      <?php $a = new Area('Opening Content 1'); $a->display($c); ?>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-5">
      <?php $a = new Area('Opening Content 2'); $a->display($c); ?>
      </div>
    </div>
  </div>
  <div class="container become-host">
    <?php $a = new Area('Section Numeric Text Block title'); $a->display($c); ?>
    <div class="row">
      <div class="col-md-4 col-sm-12">
        <?php $a = new Area('Section Numeric Text Block 1'); $a->display($c); ?>
      </div>
      <div class="col-md-4 col-sm-12">
        <?php $a = new Area('Section Numeric Text Block 2'); $a->display($c); ?>
      </div>
      <div class="col-md-4 col-sm-12">
        <?php $a = new Area('Section Numeric Text Block 3'); $a->display($c); ?>
      </div>
    </div>
  </div>

  <?php if ($pageType == 'listing') { ?>
    <div class="container-fluid">
      <?php $a = new Area('Listing Banner'); $a->display($c); ?>
    </div>
    <div class="container-fluid review" id="landlord-reviews">
        <h2 data-aos="text-reveal"><span>Review of other property owners <img class="emoji-img" src="<?php echo $this->getThemePath(); ?>/dist/images/diamond.png" alt="Diamond Emoji"/></span></h2>
        <div class="landlord__reviews">
        </div>
        <div class="landlord-reviews-c5">
          <?php $a = new Area('Landlord Reviews'); $a->display($c); ?>
        </div>
    </div>
  <?php } ?>

  <?php if ($pageType == 'rental') { ?>
    <div class="container-fluid">
      <div class="row ">
        <div class="col-sm-6 col-xs-12 col-md-3">
          <?php $a = new Area('Section Gray Text Block 1'); $a->display($c); ?>
        </div>
        <div class="col-sm-6 col-xs-12 col-md-3">
          <?php $a = new Area('Section Gray Text Block 2'); $a->display($c); ?>
        </div>
        <div class="col-sm-6 col-xs-12 col-md-3">
          <?php $a = new Area('Section Gray Text Block 3'); $a->display($c); ?>
        </div>
        <div class="col-sm-6 col-xs-12 col-md-3">
          <?php $a = new Area('Section Gray Text Block 4'); $a->display($c); ?>
        </div>
      </div>
    </div>
    <div class="container-fluid rental-banner">
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5 p-0" data-aos="fade-up" data-aos-delay="200">
          <?php $a = new Area('Rental Banner 1'); $a->display($c); ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-7 p-0" data-aos="fade-up" data-aos-delay="350">
          <?php $a = new Area('Rental Banner 2'); $a->display($c); ?>
        </div>
      </div>
    </div>
    <div class="container-fluid review" id="landlord-reviews">
        <h2 data-aos="text-reveal"><span>Reviews of other tenants <img class="emoji-img" src="<?php echo $this->getThemePath(); ?>/dist/images/fire.png" alt="Fire Emoji"/></span></h2>
        <!-- <div class="tenant__reviews"></div> -->

        <div id="guest" class="block property__reviews" data-hasbutton="false">
        </div>
    </div>
  <?php } ?>

  <div class="container faqs hidden">
    <h2 data-aos="text-reveal"><span>FAQs <img class="emoji-img float-right" src="<?php echo $this->getThemePath(); ?>/dist/images/magic-ball.png" alt="Magicball Emoji"/></span></h2>
    <div class="collapse__faqs" data-aos="fade-up" data-aos-delay="200">
    </div>
    <div class="faqs__concrete" >
      <?php
          $stack = Stack::getByName('FAQs');
          $stack->display();
      ?>
    </div>
  </div>

  <div class="container">
    <?php $a = new Area('Listing Banner 2'); $a->display($c); ?>
  </div>
</div>
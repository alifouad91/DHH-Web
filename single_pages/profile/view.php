<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var UserInfo $profile */
$userHash = bin2hex(openssl_encrypt((int) $profile->getUserId(), MCRYPT_BLOWFISH, MCRYPT_KEY, OPENSSL_RAW_DATA,
                                    MCRYPT_IV));
?>
<?php if ($task == 'profile') { ?>
    <div class="page__wrapper page__limit">
        <div class="page__profile" data-uid="<?php echo $profile->uID; ?>" data-id="<?php echo $userHash; ?>"></div>
        <?php //Loader::element('profile/sidebar', array('profile'=> $profile)); ?>
        <?php //var_dump($profile)?>
        <!-- <div id="ccm-profile-body">
    	<div id="ccm-profile-body-attributes">
    	<div class="ccm-profile-body-item">
    	
        <h1><?php echo $profile->getUserName() ?></h1>
        <?php
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach ($uaks as $ua) { ?>
            <div>
                <label><?php echo $ua->getAttributeKeyDisplayName() ?></label>
                <?php echo $profile->getAttribute($ua, 'displaySanitized', 'display'); ?>
            </div>
        <?php } ?>		
        
        </div>

		</div>
		
		<?php
        $a = new Area('Main');
        $a->setAttribute('profile', $profile);
        $a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
        $a->setBlockWrapperEnd('</div>');
        $a->display($c);
        ?>

    </div>

	<div class="ccm-spacer"></div> -->

    </div>
<?php } elseif ($task == 'favourites') { ?>
    <div class="page__wrapper page__favourite">
        <div class="page__section container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="container">
                        <div class="page__section__header">
                            <h1>Favourites</h1>
                            <span class="sub-text">Your favourite properties in Dubai</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 page__favourite__render page__favourite__details " data-id="1"></div>
                <!-- <div class="col-lg-offset-1 col-lg-6">
                    <div class="page__favourite__details container" >

                    </div>
                </div>
                <div class="col-lg-4">
                  <div class="page__favourite__details__card container" >

                      </div>
                </div> -->
            </div>
        </div>
    </div>
<?php } elseif ($task == 'myBookings') { ?>
    <div class="page__wrapper page__limit page__mybookings">
        <div class="page__section container-fluid">
            <div class="row">
                <div class="col-lg-offset-1 col-lg-8">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="page__section__header">
                                <h1>My Bookings</h1>
                                <span class="sub-text"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-offset-1 col-lg-8 page__mybookings__render page__mybookings__details "
                     data-id="1"></div>
            </div>
        </div>
    </div>

    <div id="amend-booking-form" class="popup popup__amend">
        <div class="popup__title">
            <h4>Amend booking</h4>
            <p class="property"></p>
            <p>Please tell us more about the changes you want to make, and one of our team members will reach out to you
                to assist in your request.</p>
        </div>

        <?php
        $stack = Stack::getByName('Form - Amend Booking');
        $stack->display();
        ?>
    </div>

    <div id="cancel-booking-form" class="popup popup__cancel">
        <div class="popup__title">
            <h4>Cancel booking</h4>
            <p class="property"></p>
            <p>Please tell us more about why you wish to cancel your booking and one of our team members will reach out
                to assist you.</p>
        </div>

        <?php
        $stack = Stack::getByName('Form - Cancel Booking');
        $stack->display();
        ?>
    </div>
<?php } elseif ($task == 'myReviews') { ?>
    <div class="page__wrapper page__limit page__myreviews">
        <div class="page__section container-fluid">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-1">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="page__section__header">
                                <h1>My Reviews</h1>
                                <?php if ($profile->getReviewCount()) { ?>
                                    <span class="sub-text"><?php echo $profile->getReviewCount(); ?>
                                        reviews in total</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-offset-1 col-lg-8 page__myreviews__render page__myreviews__details "
                     data-id="1"></div>
            </div>
        </div>
    </div>
<?php } elseif ($task == 'myProperties') { ?>
    <div class="page__wrapper page__limit page__myproperties page__myproperties__render">
    </div>
<?php } elseif ($task == 'propertyReviews') { ?>
    <div class="page__wrapper page__limit page__propertyreviews page__propertyreviews__render" >

    </div>
<?php } elseif ($task == 'finances') { ?>
    <div data-id="1"  class="page__wrapper page__limit page__finances__render">
        <!-- <div class="page__section container-fluid page__finances">
          <div class="row">
            <div class="col-lg-offset-1 col-lg-8">
                <div class="container-fluid">
                  <div class="row">
                    <div class="page__section__header">
                      <h1>Utilities Invoices</h1>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-12"  data-id="1"></div>
          </div>
        </div> -->
    </div>
<?php } ?>
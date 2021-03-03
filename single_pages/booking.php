<?php
/** @var Booking $booking*/

?>
<?php if ($task == 'review') { ?>
<div class="page__wrapper page__limit page__reviewbooking" >
  <div class="page__section container-fluid">
    <div class="row">
      <div class="col-lg-offset-1 col-lg-10">
          <div class="">
            <div class="page__section__header">
              <h1>Review booking</h1>
              <span class="sub-text"><?php echo $booking->getBookingNo(); ?></span>
            </div>
          </div>
      </div>
      <div class="col-lg-12 page__reviewbooking__render page__reviewbooking__details "  data-id="<?php echo
      $booking->getBID();?>" data-bookingnum="<?php echo $booking->getBookingNo(); ?>"></div>
    </div>
  </div>
</div>
<?php } ?>
<?php if ($task == 'payment') { ?>
    <div><?php echo $booking->getBookingNo(); ?></div>
<?php } ?>
<?php if ($task == 'confirm') { ?>
  <div class="page__wrapper page__limit page__confirmbooking" >
    <div class="page__section container-fluid">
        <div class="col-lg-12 page__confirmbooking__render page__confirmbooking__details "  data-id="<?php echo
        $booking->getBID();?>" data-bookingnum="<?php echo $booking->getBookingNo(); ?>"></div>
      </div>
    </div>
  </div>
<?php } ?>

<?php if($task == 'cancelled') {?>
  <div class="page__wrapper page__limit page__cancelledbooking" >
    <div class="page__section container-fluid">
        <div class="col-lg-12 page__cancelledbooking__render page__cancelledbooking__details " data-title="Booking Cancelled" data-sub="You have cancelled your booking." data-id="<?php echo
        $booking->getBID();?>" data-bookingnum="<?php echo $booking->getBookingNo(); ?>"></div>
      </div>
    </div>
  </div>
<?php } ?> 

<?php if($task == 'failed') {?>
  <div class="page__wrapper page__limit page__cancelledbooking" >
    <div class="page__section container-fluid">
        <div class="col-lg-12 page__cancelledbooking__render page__cancelledbooking__details " data-title="Booking Failed" data-sub="There was an issue with your payment. Please try again." data-id="<?php echo
        $booking->getBID();?>" data-bookingnum="<?php echo $booking->getBookingNo(); ?>"></div>
      </div>
    </div>
  </div>
<?php } ?> 
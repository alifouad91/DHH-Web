<?php
$page = $c;
?>
<div class="page__wrapper page__limit page__mybookings" >
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
      <div class="col-lg-offset-1 col-lg-8 page__mybookings__render page__mybookings__details "  data-id="1"></div>
    </div>
  </div>
</div>

<div id="amend-booking-form" class="popup popup__amend">
  <div class="popup__title">
    <h4>Amend booking</h4>
    <p class="property"></p>
    <p>Please tell us more about the changes you want to make, and one of our team members will reach out to you to assist in your request.</p>
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
    <p>Please tell us more about why you wish to cancel your booking and one of our team members will reach out to assist you.</p>
  </div>
  
  <?php
      $stack = Stack::getByName('Form - Cancel Booking');
      $stack->display();
  ?>
</div>
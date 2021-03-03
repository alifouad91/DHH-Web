<?php

/** @var $bookings */
/** @var $booking */
/** @var FormHelper $form */
/** @var $bookingStatuses */
/** @var $eventStatuses */
$form            = Loader::helper('form');
$dashboardHelper = Loader::helper('concrete/dashboard');
/** @var FormDateTimeHelper $fdth */
$fdth = Loader::helper('form/date_time');
$ph = Loader::helper('price');
?>


<?php if ($task == 'overview') { ?>
    <?php echo $dashboardHelper->getDashboardPaneHeaderWrapper("Bookings", false, false, false) ?>
    <div class="ccm-pane-options">
        <form action="<?php echo $this->action(''); ?>" id="booking_search">
            <div class="ccm-pane-options-permanent-search">
                <div class="row offset-bottom">

                    <div class="span3 dateFilter">
                        <label class="control-label">Booking Dates</label>

                        <div class="controls">
                            <?php echo $fdth->date('from'); ?>
                            to
                            <?php echo $fdth->date('to'); ?>
                        </div>
                    </div>

                    <div class="span3">
                        <label class="control-label">Booking Type</label>

                        <div class="controls">
                            <?php echo $form->select('bookingType', ['all' => 'All', 'previous' => 'Previous', 'upcoming' => 'Upcoming']); ?>
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label">Property Name</label>

                        <div class="controls">
                            <?php echo $form->select('pID', ["" => "Please enter 1 or more characters"], '', ['style' => 'width:100%;', 'class' => 'property-names']);
                            ?>
                        </div>
                    </div>

                    <div  class="span3">
                        <label class="control-label"># Per Page</label>
                        <div class="controls">
                            <?php echo $form->select('items', $itemsOptions, ['style' => 'width: 60px;']); ?>
                        </div>
                    </div>

                    <div class="span1">
                        <?php echo $form->label(false, '&nbsp;'); ?>
                        <div class="controls">
                            <?php echo $form->submit('search', 'Search'); ?>
                        </div>
                    </div>
                    <div class="span1" style="float:right;">
                        <?php echo $form->label(false, '&nbsp;'); ?>
                        <div class="controls">
                            <?php echo $form->button('export_excel',t('Export to Excel'),array(),'btn success ccm-button-v2-right'); ?>
                            <input type="hidden" id="generate_preview" name="generate_preview" value="0"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <script>
        $(document).ready(function() {

            <?php if($property) { ?>

            var $newOption = $("<option selected='selected'></option>").val("<?php echo $property->getID(); ?>").text("<?php echo $property->getName(); ?>")
            $("#pID").append($newOption).trigger('change');

            <?php } ?>
        });
    </script>
    <style>
        /* Tooltip text */
        .bookingToolTip .tooltiptext {
            visibility: hidden;
            text-align: center;
            background:#ffffff;
            border:1px solid #cccccc;
            color:#6c6c6c;
            padding:2px 3px;
            margin-left:8px;
            margin-top: -20px;
        }

        /* Show the tooltip text when you mouse over the tooltip container */
        .bookingToolTip:hover .tooltiptext {
            visibility: visible;
        }
    </style>
    <div class="ccm-pane-body">

        <div class="row">

            <div class="ccm-pane-body">
                <div class="ccm-list-wrapper">
                    <div class="row" style="padding-left: 15px">

                        <h2>Bookings</h2>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <td>Booking No</td>
                                <td>User</td>
                                <td>Email</td>
                                <td>Start Date</td>
                                <td>End Date</td>
                                <td>Status</td>
                                <td>Action</td>

                            </tr>
                            <?php foreach ($bookings as $booking) {
                            /** @var $booking Booking */
                            ?>

                                <tr>
                                    <td> <?php echo $booking->getBookingNo() ?> </td>
                                    <td> <?php echo $booking->getUID() ?> </td>
                                    <td> <?php echo $booking->getEmail() ?> </td>
                                    <td> <?php echo $booking->getBookingStartDate() ?> </td>
                                    <td> <?php echo $booking->getbookingEndDate() ?> </td>
                                    <td> <?php echo $booking->getBookingStatus() ?> </td>
                                    <td>
                                        <div class="bookingToolTip">
                                            <a href="<?php echo View::url($configUrl . '/', 'details/' . $booking->getBID());?>">
                                                <i class="icon-edit" style="margin-left: 14px;"></i>
                                            </a>
                                            <span class="tooltiptext">View/Edit</span>
                                        </div>
                                    </td>
                                </tr>

                            <? } ?>
                            </thead>


                        </table>

                        <?php if ($success_message) { ?>
                            <div class="alert alert-info success_message">
                                <?php echo $success_message ?>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
        <?php $bookingsList->displaySummary(); ?>
    </div>
    <div class="ccm-pane-footer">
        <?php $bookingsList->displayPagingV2(); ?>
    </div>
    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>

<?php } ?>

<?php if ($task == 'details') { ?>

    <?php echo $dashboardHelper->getDashboardPaneHeaderWrapper("Booking Details", false, false, false) ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('add_edit'); ?>">
        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <div class="row">

                <input type="hidden" name="mode" value="update"/>
                <input type='hidden' name="bID" value="<?php echo $booking->getBID() ?>">
                <div class="span3">
                    <p><strong>Booking No</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('bookingNo', $booking->getBookingNo(), null, true); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Email</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('email', $booking->getEmail()); ?></p>
                </div>
                <div class="span3">
                    <p><strong>Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('uname', $uname, ['readonly' => true]); ?></p>
                </div>
                <div class="span3">
                    <p><strong>Phone</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('uphone', $uphone, ['readonly' => true]); ?></p>
                </div>
                <div class="span3">
                    <p><strong>Address</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('address', $address, ['readonly' => true]); ?></p>
                </div>
                <div class="span3">
                    <p><strong>City</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('city', $city, ['readonly' => true]); ?></p>
                </div>
                <div class="span3">
                    <p><strong>Country</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('country', $country, ['readonly' => true]); ?></p>
                </div>
                <div class="span3">
                    <p><strong>Property Name</strong></p>
                </div>
                <div class="span8">
                    <?php /** @var Property $property */ ?>
                    <p><?php
                        if(is_object($property)){
                            $propertyName = $property->getName();
                        }
                        echo $form->text('pName', $propertyName, ['readonly' => true]); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Booking Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('bookingDate', $booking->getBookingDate()); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Booking Start Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('bookingStartDate', $booking->getBookingStartDate()); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Booking End Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('bookingEndDate', $booking->getbookingEndDate()); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Number of Days</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('noOfDays', $booking->getNoOfDays(), ['disabled' => true], true);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>No Of Guest</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('noOfGuest', $booking->getNoOfGuest()); ?></p>
                </div>

                <div class="span3">
                    <p><strong>No Of Children</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('noOfChildren', $booking->getNoOfChildren()); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Booking Status</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->select('bookingStatus', $bookingStatuses, $booking->getBookingStatus()); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Event Status</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->select('eventStatus', $eventStatuses, $eventStatus, null, true); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Additional Requests</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->textarea('additionalRequests', $booking->getAdditionalRequests()); ?></p>
                </div>


                <div class="span11">
                    <p>
                    <h3>Pricing Details (in <?php echo CurrencyRates::DEFAULT_CURRENCY; ?>):</h3></p>
                </div>


                <div class="span5">
                    <table class="table-bordered table">
                        <tr>
                            <td><strong>Property Price</strong></td>
                            <?php
                            $property = Property::getByID($booking->getPID());
                            ?>
                            <td>
                                <?php if($property) {?>
                                    <label>
                                        <?php
                                        $priceBreakdown = $booking->getPriceBreakdown();
                                        if(is_array($priceBreakdown)){
                                            ?>
                                            <ul>
                                                <?php
                                                foreach($priceBreakdown as $key => $breakdown){
                                                ?>
                                                <li>
                                                    <?php
                                                    echo $breakdown->price." | ".date("d-m-Y", strtotime($breakdown->day));
                                                    ?>
                                                </li>
                                                    <?php
                                                }
                                            ?>
                                            </ul>
                                            <?php
                                        } ?>
                                    </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Sub Total</strong></td>
                            <td><label><?php echo $ph->format($booking->getSubtotal()); ?></label></td>
                        </tr>
                        <tr>
                            <td><strong>Additional Items Total</strong></td>
                            <td><label><?php echo $ph->format($booking->getBookingPropertyFacilitiesTotal()); ?></label></td>
                        </tr>

                        <?php if($booking->getCreditAmount()) {
                            $user = UserInfo::getByID($booking->getUID());
                            $refferedBy = $user->getReferredBy();
                            ?>
                            <tr>
                                <td><strong>Referral Credit (referred by <?php echo $refferedBy; ?>)</strong></td>
                                <td><label>- <?php echo $ph->format($booking->getCreditAmount()); ?></label></td>
                            </tr>
                        <?php } ?>


                        <?php if($booking->getDiscountReceived()) { ?>
                            <tr>
                                <td><strong>Coupon Applied</strong></td>
                                <td>
                                    <?php
                                    $coupons =  $booking->getAppliedCoupons();
                                    foreach ($coupons as $k => $v) {
                                        $coupon = DiscountCoupon::getByID($v);
                                        if($coupon) { ?>
                                            <label><?php echo $coupon->getCouponCode(); ?></label>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>


                            </tr>
                            <tr>
                                <td><strong>Coupon Discount</strong></td>
                                <td><label>- <?php echo $ph->format($booking->getDiscountReceived()); ?></label></td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td><strong>Vat Amount</strong></td>
                            <td><label><?php echo $ph->format($booking->getVat()); ?></label></td>
                        </tr>
                        <tr>
                            <td><strong>Tourism Fee</strong></td>
                            <td><label><?php echo $ph->format($booking->getDhiramFee()); ?></label></td>
                        </tr>

                        <tr>
                            <td><strong>Total </strong></td>
                            <td><label><?php echo $ph->format($booking->getTotal()) ?></label></td>
                        </tr>


                    </table>
                    </p><br>
                </div>

                <div class="span6">
                    <h4> Addition Property Facilities </h4>
                    <table class="table-bordered table">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Price</th>
                        </tr>
                        <?php
                        /** @var BookingPropertyFacilities $bookingPropertyFacility */
                        $i = 0;
                        foreach ($booking->getBookingPropertyFacilities() as $bookingPropertyFacility) { ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $bookingPropertyFacility->getName(); ?></td>
                                <td><?php echo $bookingPropertyFacility->getPrice(); ?></td>
                            </tr>

                            <?php $i++;
                        } ?>

                    </table>

                </div>

                <div class="span11">
                    <p>
                    <h3>Payment Details :</h3></p>
                </div>

                <?php
                $paymentList = new PaymentList();
                $paymentList->filterByBookingId($booking->getBID());
                $paymentList->sortByCreatedAt();
                $paymentDetails = $paymentList->get();

                foreach($paymentDetails as $payment){
                    $bookingStatus = $payment->getOrderStatus();
                    break;
                }
                ?>
                <div class="span5">
                    <table class="table-bordered table">
                        <tr>
                            <td><strong>Payment Status</strong></td>
                            <td><label><?php echo $bookingStatus; ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Attempts Done</strong></td>
                            <td><label><?php echo count($paymentDetails); ?></label></td>
                        </tr>
                    </table>
                    </p><br>
                </div>
            </div>

            <div class="row">
                <?php if ($errors) { ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error) { ?>
                            <p> <?php echo $error; ?></p>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url('/dashboard/booking/bookings'); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>
                <a href="<?php echo $this->action('delete/'.$booking->getBID()); ?>" class="btn danger">Delete Booking</a>
            </span>
        </div>
    </form>


    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>

<?php } ?>


<style>

</style>

<script>

    $(document).ready(function () {
        setTimeout(function () {
            $('.success_message').fadeOut();

        }, 3000);
    });


    $("#delete_form").on('submit', function (evt) {
        evt.preventDefault();
        var x = confirm('Are you sure you wan to delete this Booking?');
        if (x) {
            this.submit();
        }

    });

    $("#export_excel").on('click', function () {

        $("#generate_preview").val('1');
        $('#booking_search').get(0).submit();

    });

    $("#search").on('click', function (evt) {
        $("#generate_preview").val('0');
    });


</script>
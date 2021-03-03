<?php

/** @var FormHelper $form */
$form            = Loader::helper('form');
$dashboardHelper = Loader::helper('concrete/dashboard');
/** @var FormDateTimeHelper $fdth */
$fdth = Loader::helper('form/date_time');
/* @var ConcreteInterfaceHelper $interface */
$interface = Loader::helper('concrete/interface');
/** @var TextHelper $th */
$th = Loader::helper('text');
$dh          = Loader::helper('date');

?>
<style>
    .disabled {
        pointer-events: none;
        /* for "disabled" effect */
        opacity: 0.5;
        background: #CCC;
    }
</style>
<style>
    .ccm-pane-options .row.offset-bottom {
        margin-bottom: 10px;
    }

    table.results-list tr.ccm-list-record td.status {
        font-weight: bold;
    }

    table.results-list tr.ccm-list-record td.status.green {
        color: green;
    }

    table.results-list tr.ccm-list-record td.status.red {
        color: red;
    }

    table.results-list tr.ccm-list-record td.status.blue {
        color: blue;
    }
</style>

<?php
if ($task == 'overview') { ?>
    <?php echo $dashboardHelper->getDashboardPaneHeaderWrapper("Reviews", false, false, false) ?>
    <div class="ccm-pane-options">
        <form action="<?php echo $this->action(''); ?>">
            <div class="ccm-pane-options-permanent-search">
                <div class="row offset-bottom">

                    <div class="span3">
                        <label class="control-label">Keywords</label>

                        <div class="controls">
                            <?php echo $form->text('keywords'); ?>
                        </div>
                    </div>

                    <div class="span1">
                        <?php echo $form->label(false, '&nbsp;'); ?>
                        <div class="controls">
                            <?php echo $form->submit(false, 'Search'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="ccm-pane-body">

        <div class="row">

            <div class="ccm-pane-body">
                <div class="ccm-list-wrapper">
                    <?php echo $interface->button('Add reviews', View::url($configURL . '/add/'), 'right', 'primary',
                        ['style' => 'margin-bottom: 10px;']); ?>
                    <div class="row" style="padding-left: 15px">
                        <table class="table table-bordered table-striped results-list">
                            <thead>
                            <tr>
                                <td>Name</td>
                                <td>Email</td>
                                <td>Property</td>
                                <td>Booking No.</td>
                                <td>Review Rating</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reviews as $i => $review) { ?>

                                <?php /** @var Review $review */
                                $editUrl        = View::url($configURL . '/detail/' . $review->getID());
                                $name           = $review->getUserInfo()->getFullName();
                                $email          = $review->getUserInfo()->getUserEmail();
                                $propertyName   = $review->getProperty()->getName();
                                $bookingNo      = $review->getBooking()->getBookingNo();
                                $reviewRating   = $review->getReviewRating();

                                ?>
                                <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                                    <td>
                                        <a href="<?php echo $editUrl; ?>"><?php echo $th->wordSafeShortText($name, 60); ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $editUrl; ?>"><?php echo $email; ?></a>
                                    </td>
                                    <td><?php echo $propertyName; ?></td>
                                    <td><?php echo $bookingNo; ?></td>
                                    <td><?php echo $reviewRating; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
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
        <?php $reviewList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $reviewList->displayPagingV2(); ?>
    </div>
    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>
<?php } ?>

<?php if ($task == 'detail') { ?>


    <?php
    $owner = UserInfo::getByID($review->getUserId());
    $booking = $review->getBooking();
    echo $dashboardHelper->getDashboardPaneHeaderWrapper("Edit Review", false, false, false) ?>
    <form action="<?php echo $this->action('update'); ?>" method="post">
        <div class="ccm-pane-body">

            <input type="hidden" id="fetch-user-token" name="fetch-user-token" value="<?=$fetchUserToken?>">
            <input type="hidden" id="fetch-booking-token" name="fetch-booking-token" value="<?=$fetchBookingToken?>">
            <div class="row">

                <div class="ccm-pane-body">
                    <div class="ccm-list-wrapper">
                        <div class="row" style="padding-left: 15px">
                            <?php echo $form->hidden('reviewID', $review->getID()); ?>
                            <div class="row">

                                <div class="span4">
                                    <p><strong>User</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('uId', [],'', ['style' => 'width:100%;', 'required' => true]);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Property</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('pId', [ $review->getPropertyId() => $review->getProperty()->getName() .' - '.$review->getProperty()->getCaption(), ],$review->getPropertyId(),
                                            ['style' => 'width:100%;', 'required' => true]);

                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Booking Code</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('bId', [ $booking->getBID() => $booking->getBookingNo() ],$booking->getBID(),
                                            ['style' => 'width:100%;', 'required' => true]);

                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Review Rating</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('reviewRating', $ratings,$review->getReviewRating(),
                                            ['style' => 'width:100%;', 'required' => true]);

                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Review Comments</strong></p>
                                </div>
                                <div class="span4">
                                    <p>
                                        <?php echo $form->textarea('reviewComment', $review->getReviewComment()); ?>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
         <span style="float:left;">
             <a class='btn btn-default' href="<?php echo View::url($configURL); ?>"> Cancel </a>
        </span>
            <span style="float:right;">
            <input type="submit" class="primary btn ccm-input-submit" id="submit" name="submit" value="Save Changes">
            <?php echo $interface->button('Delete Review', View::url($configURL . '/delete/' . $review->getID()), '', 'danger'); ?>
        </span>
        </div>
    </form>

    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>

<?php } ?>

<?php if ($task == 'add') { ?>


    <?php
    echo $dashboardHelper->getDashboardPaneHeaderWrapper("Add Review", false, false, false) ?>
    <form action="<?php echo $this->action('save'); ?>" method="post">
        <div class="ccm-pane-body">

            <input type="hidden" id="fetch-user-token" name="fetch-user-token" value="<?=$fetchUserToken?>">
            <input type="hidden" id="fetch-booking-token" name="fetch-booking-token" value="<?=$fetchBookingToken?>">
            <div class="row">

                <div class="ccm-pane-body">
                    <div class="ccm-list-wrapper">
                        <div class="row" style="padding-left: 15px">
                            <?php echo $form->hidden('reviewID', 0); ?>
                            <div class="row">

                                <div class="span4">
                                    <p><strong>User</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('uId', [],'', ['style' => 'width:100%;', 'required' => true]);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Property</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('pId', [],'',
                                            ['style' => 'width:100%;', 'required' => true]);

                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Booking Code</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('bId', [],'',
                                            ['style' => 'width:100%;', 'required' => true]);

                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Review Rating</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('reviewRating', $ratings,'',
                                            ['style' => 'width:100%;', 'required' => true]);

                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Review Comments</strong></p>
                                </div>
                                <div class="span4">
                                    <p>
                                        <?php echo $form->textarea('reviewComment', ''); ?>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
         <span style="float:left;">
             <a class='btn btn-default' href="<?php echo View::url($configURL); ?>"> Cancel </a>
        </span>
            <span style="float:right;">
            <input type="submit" class="primary btn ccm-input-submit" id="submit" name="submit" value="Save Changes">
        </span>
        </div>
    </form>

    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>

<?php } ?>


<?php if ($task == 'detail') { ?>
    <script>
        $(document).ready(function() {

            <?php if($owner) { ?>

            var $newOption = $("<option selected='selected'></option>").val("<?php echo $owner->getUserID(); ?>").text("<?php echo $owner->getFullName(); ?>")
            $("#uId").append($newOption).trigger('change');

            <?php } ?>
        });
    </script>
<?php } ?>

<?php

/** @var FormHelper $form */
$form            = Loader::helper('form');
$dashboardHelper = Loader::helper('concrete/dashboard');
/** @var FormDateTimeHelper $fdth */
$fdth = Loader::helper('form/date_time');
/* @var ConcreteInterfaceHelper $interface */
$interface = Loader::helper('concrete/interface');
/** @var $coupon_codes */
/** @var $groups */
/** @var $properties */
/** @var $discount_coupon  DiscountCoupon */
/** @var $dc_properties  array */
/** @var $dc_user_groups array */
/** @var $dc_type_options array */
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
<?php if ($task == 'overview') { ?>
    <?php echo $dashboardHelper->getDashboardPaneHeaderWrapper("Discount Coupons", false, false, false) ?>
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

                    <div class="span3 dateFilter">
                        <label class="control-label">Dates</label>

                        <div class="controls">
                            <?php echo $fdth->date('startDate'); ?>
                            to
                            <?php echo $fdth->date('endDate'); ?>
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
                    <?php echo $interface->button('Add Discount Coupon', View::url($configURL . '/add/'), 'right', 'primary',
                        ['style' => 'margin-bottom: 10px;']); ?>
                    <div class="row" style="padding-left: 15px">
                        <table class="table table-bordered table-striped results-list">
                            <thead>
                            <tr>
                                <td>Name</td>
                                <td>Coupon Code</td>
                                <td>Type</td>
                                <td>Start Date</td>
                                <td>End Date</td>
                                <td>Status</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($discounts as $i => $discount) { ?>

                                <?php /** @var DiscountCoupon $discount */
                                $editUrl    = View::url($configURL . '/detail/' . $discount->getID());
                                $name       = $discount->getName();
                                $couponCode = $discount->getCouponCode();
                                $type       = $dc_type_options[$discount->getType()];
                                $startDate  = $discount->getStartDate();
                                $endDate    = $discount->getEndDate();
                                $status     = $discount->getActive();
                                switch ($status) {
                                    case 1:
                                        $status      = 'Active';
                                        $statusColor = 'green';
                                        break;
                                    case 0:
                                        $status      = 'In Active';
                                        $statusColor = 'red';
                                        break;
                                }

                                ?>
                                <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                                    <td>
                                        <a href="<?php echo $editUrl; ?>"><?php echo $th->wordSafeShortText($name, 60); ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $editUrl; ?>"><?php echo $th->wordSafeShortText($couponCode, 60); ?></a>
                                    </td>
                                    <td><?php echo $type; ?></td>
                                    <td><?php echo $startDate; ?></td>
                                    <td><?php echo $endDate; ?></td>
                                    <td class="status <?php echo $statusColor; ?>"><?php echo $status; ?></td>
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
    </div>
    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>
<?php } ?>

<?php if ($task == 'detail') { ?>


    <?php echo $dashboardHelper->getDashboardPaneHeaderWrapper("Edit Discount Coupon", false, false, false) ?>
    <form action="<?php echo $this->action('update'); ?>" method="post">
        <div class="ccm-pane-body">

            <div class="row">

                <div class="ccm-pane-body">
                    <div class="ccm-list-wrapper">
                        <div class="row" style="padding-left: 15px">
                            <?php echo $form->hidden('discountID', $discount_coupon->getID()); ?>
                            <div class="row">

                                <div class="span4">
                                    <p><strong>Usage</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('usage', $discount_coupon->getUsedByUser(), ['style' => 'width:100%;','readonly' => true]);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Name</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('name', $discount_coupon->getName(), ['style' => 'width:100%;']);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Coupon Code</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('couponCode', $discount_coupon->getCouponCode(), ['style' => 'width:100%;', 'required' => true]);
                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Coupon Type</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('type', $dc_type_options, $discount_coupon->getType(), ['style' => 'width:100%;']);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Value</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('value', $discount_coupon->getValue(), ['style' => 'width:50px', 'required' => true]);
                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Start Date</strong></p>
                                </div>
                                <div class="span4">
                                    <?php
                                    /** @var DateHelper $dh */
                                    $is_date_range = ($discount_coupon->getStartDate() && $discount_coupon->getEndDate() && $discount_coupon->getStartDate() != '0000-00-00 00:00:00' && $discount_coupon->getEndDate() != '0000-00-00 00:00:00') ? true : false;
                                    echo $form->checkbox('is_date_range', 'Date Range', $is_date_range); ?> Enable coupon
                                    date
                                    range
                                    <p class="is_date_range_cont">
                                        <?php
                                        echo $fdth->datetime('startDate', $dh->getFormattedDate($discount_coupon->getStartDate(), 'd-m-Y H:i:s')); ?>
                                    </p>
                                </div>
                                <div class="span4">
                                    <p><strong>End Date</strong></p>
                                </div>
                                <div class="span4">
                                    <p class="is_date_range_cont">
                                        <?php
                                        echo $fdth->datetime('endDate', $dh->getFormattedDate($discount_coupon->getEndDate(),'d-m-Y H:i:s')) ?>
                                    </p>
                                </div>

                                <div class="span4">
                                    <p><strong>User Groups</strong></p>
                                </div>
                                <div class="span4">
                                    <?php
                                    $is_user_groups = ($discount_coupon->getDiscountCouponUserGroups()) ? true : false;
                                    echo $form->checkbox('is_user_groups', 'Is Groups', $is_user_groups); ?> Enable specific
                                    user groups ( leave unchecked for any group )
                                    <p class="is_user_groups_cont"> <?php echo $form->selectMultiple('groups', $groups, $dc_user_groups); ?> </p>
                                </div>
                                <div class="span4">
                                    <p><strong>Select Properties</strong></p>
                                </div>
                                <div class="span4">
                                    <?php
                                    $is_properties = ($discount_coupon->getDiscountCouponProperties()) ? true : false;
                                    echo $form->checkbox('is_properties', 'Is Property', $is_properties); ?> Enable specific
                                    properties ( leave unchecked for all properties )
                                    <p class="is_properties_cont"><?php echo $form->selectMultiple('properties', $properties, $dc_properties); ?></p>
                                </div>

                                <div class="span4">

                                </div>
                                <div class="span4">

                                </div>
                                <div class="span4">
                                    <p>
                                        Usable <?php echo $form->text('timesUsableUser', $discount_coupon->getTimesUsableUser(), ['style' => 'width:20px']); ?>
                                        per User</p>
                                </div>
                                <div class="span4">
                                    <p>
                                        Usable <?php echo $form->text('timesUsableProperty', $discount_coupon->getTimesUsableProperty(), ['style' => 'width:20px']); ?>
                                        per Property</p>
                                </div>


                                <div class="span4">
                                    <p><strong>Active</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->checkbox('active', 1, ($discount_coupon->getActive()) ? true : false); ?></p>
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
            <?php echo $interface->button('Delete Coupon', View::url($configURL . '/delete/' . $discount_coupon->getID()), '', 'danger'); ?>
        </span>
        </div>
    </form>

    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>

<?php } ?>

<?php if ($task == 'add') { ?>


    <?php echo $dashboardHelper->getDashboardPaneHeaderWrapper("Add Discount Coupon", false, false, false) ?>
    <form action="<?php echo $this->action('save'); ?>" method="post">
        <div class="ccm-pane-body">

            <div class="row">

                <div class="ccm-pane-body">
                    <div class="ccm-list-wrapper">
                        <div class="row" style="padding-left: 15px">
                            <div class="row">

                                <div class="span4">
                                    <p><strong>Name</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('name', '', ['style' => 'width:100%;']);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Coupon Code</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('couponCode', '', ['style' => 'width:100%;']);
                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Coupon Type</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->select('type', $dc_type_options, '', ['style' => 'width:100%;']);
                                        ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Value</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->text('value', '', ['style' => 'width:50px']);
                                        ?></p>
                                </div>

                                <div class="span4">
                                    <p><strong>Start Date</strong></p>
                                </div>
                                <div class="span4">
                                    <?php
                                    $is_date_range = false;
                                    echo $form->checkbox('is_date_range', 'Date Range', $is_date_range); ?> Enable coupon
                                    date
                                    range
                                    <p class="is_date_range_cont">
                                        <?php
                                        echo $fdth->datetime('startDate', ''); ?>
                                    </p>
                                </div>
                                <div class="span4">
                                    <p><strong>End Date</strong></p>
                                </div>
                                <div class="span4">
                                    <p class="is_date_range_cont">
                                        <?php
                                        echo $fdth->datetime('endDate', ''); ?>
                                    </p>
                                </div>

                                <div class="span4">
                                    <p><strong>User Groups</strong></p>
                                </div>
                                <div class="span4">
                                    <?php
                                    $is_user_groups = false;
                                    echo $form->checkbox('is_user_groups', 'Is Groups', $is_user_groups); ?> Enable specific
                                    user groups ( leave unchecked for any group )
                                    <p class="is_user_groups_cont"> <?php echo $form->selectMultiple('groups', $groups, ''); ?> </p>
                                </div>
                                <div class="span4">
                                    <p><strong>Select Properties</strong></p>
                                </div>
                                <div class="span4">
                                    <?php
                                    $is_properties = false;
                                    echo $form->checkbox('is_properties', 'Is Property', $is_properties); ?> Enable specific
                                    properties ( leave unchecked for all properties )
                                    <p class="is_properties_cont"><?php echo $form->selectMultiple('properties', $properties, ''); ?></p>
                                </div>

                                <div class="span4">

                                </div>
                                <div class="span4">

                                </div>
                                <div class="span4">
                                    <p> Usable <?php echo $form->text('timesUsableUser', '', ['style' => 'width:20px']); ?>
                                        per User</p>
                                </div>
                                <div class="span4">
                                    <p>
                                        Usable <?php echo $form->text('timesUsableProperty', '', ['style' => 'width:20px']); ?>
                                        per Property</p>
                                </div>


                                <div class="span4">
                                    <p><strong>Active</strong></p>
                                </div>
                                <div class="span4">
                                    <p><?php echo $form->checkbox('active', 1, true); ?></p>
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
                <input type="submit" class="primary btn ccm-input-submit" id="submit" name="submit" value="Save">
            </span>
        </div>
    </form>

    <?php echo $dashboardHelper->getDashboardPaneFooterWrapper(false); ?>

<?php } ?>


<?php if ($task == 'detail' || $task == 'add') { ?>
    <script>

        class CouponCodeModule {

            is_date_range = null;
            is_user_groups = null;
            is_properties = null;

            constructor() {
                this.init();
            }

            init() {
                this.is_date_range = $('#is_date_range');
                this.is_user_groups = $('#is_user_groups');
                this.is_properties = $('#is_properties');

                //init on load
                this.config_date_range(this.is_date_range);
                this.config_user_groups(this.is_user_groups);
                this.config_properties(this.is_properties);

                //listners
                this.is_date_range.click((evt) => {
                    console.log(evt.target);
                    this.config_date_range(evt.target);
                });

                this.is_user_groups.click((evt) => {
                    console.log(evt.target);
                    this.config_user_groups(evt.target);
                });

                this.is_properties.click((evt) => {
                    console.log(evt.target);
                    this.config_properties(evt.target);
                });
            }

            config_date_range = (elem) => {
                if ($(elem).is(":checked")) {
                    $('.is_date_range_cont').removeClass('disabled');
                } else {
                    $('.is_date_range_cont').addClass('disabled');

                }
            }
            config_user_groups = (elem) => {

                if ($(elem).is(":checked")) {
                    $('.is_user_groups_cont').removeClass('disabled');
                } else {
                    $('.is_user_groups_cont').addClass('disabled');

                }

            }
            config_properties = (elem) => {

                if ($(elem).is(":checked")) {
                    $('.is_properties_cont').removeClass('disabled');
                } else {
                    $('.is_properties_cont').addClass('disabled');

                }

            }
        }

        $(document).ready(function () {
            $ccmod = new CouponCodeModule();
        });

    </script>
<?php } ?>

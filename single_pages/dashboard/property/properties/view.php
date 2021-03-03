<?php
/* @var View $this */
/* @var $c */
/* @var $configURL */
/* @var FormHelper $form */
$form = Loader::helper('form');
/* @var ConcreteInterfaceHelper $interface */
$interface = Loader::helper('concrete/interface');
/* @var ValidationErrorHelper $error */
/* @var string $task */
defined('C5_EXECUTE') or die("Access Denied.");
/** @var ConcreteDashboardHelper $cdh */
$cdh = Loader::helper('concrete/dashboard');
/* @var PriceHelper $ph */
$ph = Loader::helper('price');

$userList = new UserList();
$userList->filterByGroup(LANDLORD_GROUP_NAME);
$users = $userList->get();

$userArr[''] = '';
/** @var UserInfo $user */
foreach ($users as $user) {
    $userArr[$user->getUserID()] = $user->getUserEmail().' , '.$user->getFullName();
}
?>

<?php

if ($task === 'overview') { ?>

    <style>
        .ccm-pane-options .row.offset-bottom {
            margin-bottom: 10px;
        }

        table.ccm-results-list tr.ccm-list-record td.status {
            font-weight: bold;
        }

        table.ccm-results-list tr.ccm-list-record td.status.green {
            color: green;
        }

        table.ccm-results-list tr.ccm-list-record td.status.red {
            color: red;
        }

        table.ccm-results-list tr.ccm-list-record td.status.blue {
            color: blue;
        }
    </style>

    <?php
    /* @var array $filterOptions */
    /* @var array $itemsOptions */
    /* @var PropertyList $propertyList */
    /* @var array $propertys */

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');
    ?>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Properties', false, false, false); ?>

    <div class="ccm-pane-options">
        <form action="<?php echo $this->action(''); ?>">
            <div class="ccm-pane-options-permanent-search">
                <div class="row offset-bottom">
                    <div class="span3">
                        <?php echo $form->label('keywords', 'Keywords'); ?>
                        <div class="controls">
                            <?php echo $form->text('keywords'); ?>
                        </div>
                    </div>
                    <!--                    <div class="span3 dateFilter">-->
                    <!--                        <label class="control-label">Date Published</label>-->
                    <!---->
                    <!--                        <div class="controls">-->
                    <!--                            --><?php //echo $fdth->date('from'); ?>
                    <!--                            to-->
                    <!--                            --><?php //echo $fdth->date('to'); ?>
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <div class="span2">
                        <?php echo $form->label('filter', 'Filters'); ?>
                        <div class="controls">
                            <?php echo $form->select('filter', $filterOptions, ['style' => 'width: 140px;']); ?>
                        </div>
                    </div>
                    <div style="display: none;" class="span1">
                        <?php echo $form->label('items', '&nbsp;#'); ?>
                        <div class="controls">
                            <?php echo $form->select('items', $itemsOptions, ['style' => 'width: 60px;']); ?>
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
        <div class="ccm-list-wrapper">
            <?php echo $interface->button('Add Property', View::url($configURL . '/add'), 'right', 'primary',
                                          ['style' => 'margin-bottom: 10px;']); ?>

            <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
                <tr>
                    <th>Caption</th>
                    <th>Owner</th>
                    <th>Per day Price</th>
                    <th>Weekly Price</th>
                    <th>Mpnthly Price</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($properties as $i => $property) { ?>
                    <?php
                    /* @var Property $property */
                    $propertyID      = $property->getId();
                    $propertyEditURL = View::url($configURL . '/detail/' . $propertyID);
                    $title           = $property->getCaption();
                    $status          = $property->getStatus();
                    $uname           = $property->getOwnerInfo()->getFirstName();
                    $status          = $property->getStatus();

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
                        <td><a href="<?php echo $propertyEditURL; ?>"><?php echo $th->wordSafeShortText($title, 60); ?></a></td>
                        <td><?php echo $uname; ?></td>
                        <td><?php echo $ph->format($property->getPerDayPrice()); ?></td>
                        <td><?php echo $ph->format($property->getWeeklyPrice()) ; ?></td>
                        <td><?php echo $ph->format($property->getMonthlyPrice()); ?></td>
                        <td class="status <?php echo $statusColor; ?>"><?php echo $status; ?></td>
                    </tr>
                <?php } ?>
            </table>

        </div>
        <?php $propertyList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $propertyList->displayPagingV2(); ?>
    </div>

    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".chosen-select").chosen(ccmi18n_chosen);
        });
    </script>

<?php } else if ($task === 'detail') { ?>

    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php
    /* @var Property $property */
    /* @var array $localeOptions */
    /** @var array $competitionOptions */
    /** @var array $selectedLinks */
    /** @var string $previewURL */
    /** @var string $preview */

    /* @var RichTextHelper $rt */
    $rt = Loader::helper('rich_text');

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var ValidationTokenHelper $vth */
    $vth = Loader::helper('validation/token');

    $owner = UserInfo::getByID($property->getOwnerID());
    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Property', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('edit_save'); ?>"
          id="propertyForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>
        <?php echo $form->hidden('areaTypeID', 0); ?>
        <input type="hidden" id="fetch-user-token" name="fetch-user-token" value="<?=$fetchUserToken?>">


        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
            <ul class="tabs">
                <li class="active"><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>

            <div class="row">
                <div class="span3">
                    <p><strong>Property ID</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $property->getId(); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Name</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('name', $property->getName(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>URL Slug</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('pPath', $property->getPath(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Caption</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('caption', $property->getCaption(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Description</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->textarea('description', $property->getDescription(), ['style' => 'width:100%;min-height:80px;
                    ', 'required' => true]); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Owner Name</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->select('uID', [ $owner->getUserID() => $owner->getUserEmail().' , '.
                            $owner->getFullName() ],$owner->getUserEmail().' , '. $owner->getFullName(),
                                                ['style' => 'width:100%;', 'required' => true]);

                        ?></p>
                </div>
                <div class="span3">
                    <p><strong>Tourism Fee (in AED)</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('tourismFee', $property->getTourismFee(), ['style' => 'width:100%;', 'required' => true]);?></p>
                </div>

                <!--                <div class="span3">-->
                <!--                    <p><strong>Background Position</strong></p>-->
                <!--                </div>-->
                <!--                <div class="span8">-->
                <!--                    <p>-->
                <?php //echo $form->select('bgPosition', $bgPosition, $property->getBgPosition()); ?><!--</p>-->
                <!--                </div>-->

                <!--                <div class="span3">-->
                <!--                    <p><strong>Image</strong></p>-->
                <!--                </div>-->
                <!--                <div class="span8">-->
                <!---->
                <!--                    <div id="filesInfo">--><?php //$property->outputImage(300, 300); ?><!--</div>-->
                <!--                    <input type="file" name="image" id="filesToUpload"/>-->
                <!---->
                <!--                </div>-->


                <!--                <div class="span3">-->
                <!--                    <p><strong>Image Caption</strong></p>-->
                <!--                </div>-->
                <!--                <div class="span8">-->
                <!--                    <p>-->
                <?php //echo $form->text('caption', $property->getCaption(), ['style' => 'width:100%;']); ?><!--</p>-->
                <!--                </div>-->


                <!--                <div class="span3">-->
                <!--                    <p><strong>Competition</strong></p>-->
                <!--                </div>-->
                <!--                <div class="span8">-->
                <!--                    <p>-->
                <?php //echo $form->selectMultiple('competitions', $competitionOptions, $selectedLinks); ?><!--</p>-->
                <!--                </div>-->


                <div class="span12">
                    <h3>Pricing</h3>
                </div>

                <div class="span2">
                    <p><strong>Per Day Price</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('perDayPrice', $property->getPerDayPrice(), ['style' => 'width:100%;']);
                        ?></p>
                </div>
                <div class="span2">
                    <p>&nbsp;</p>
                </div>
                <div class="span2">
                    <p><strong>Monthly Price</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('monthlyPrice', $property->getMonthlyPrice(), ['style' => 'width:100%;']);
                        ?></p>
                </div>
                <div class="span2">
                    <p><strong>Weekly Price</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('weeklyPrice', $property->getWeeklyPrice(), ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span12">
                    <h3> Other Details</h3>
                </div>
                <div class="span2">
                    <p><strong>Latitude</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('latitude', $property->getLatitude(), ['style' => 'width:100%;']); ?></p>
                </div>
                <div class="span2">
                    <p>&nbsp;</p>
                </div>
                <div class="span2">
                    <p><strong>Longitude</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('longitude', $property->getLongitude(), ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p><strong>Number of Rooms</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('noOfRooms', $property->getNoOfRooms(), ['style' => 'width:100%;']); ?></p>
                </div>
                <div class="span2">
                    <p>&nbsp;</p>
                </div>

                <div class="span2">
                    <p><strong>Bedrooms</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->select('bedrooms', $bedroomOptions,
                                                $property->getBedrooms(), ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p><strong>Bathrooms</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('bathrooms', $property->getBathrooms(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span2">
                    <p>&nbsp;</p>
                </div>

                <div class="span2">
                    <p><strong>Guests per room</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('maxGuests', $property->getMaxGuests(), ['style' => 'width:100%;']); ?></p>
                </div>

                <div class="span2">
                    <p><strong>Beds</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('beds', $property->getBeds(), ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p>&nbsp;</p>
                </div>

<!--                <div class="span2">-->
<!--                    <p><strong>Apartment Area</strong></p>-->
<!--                </div>-->
<!--                <div class="span3">-->
<!--                    <p>--><?php //echo $form->select('apartmentAreaID', $apartmentAreaOptions, $property->getApartmentAreaId
//                        (), ['style' => 'width:100%;
//                    ']); ?><!--</p>-->
<!--                </div>-->

<!--                <div class="span2">-->
<!--                    <p><strong>Area Type</strong></p>-->
<!--                </div>-->
<!--                <div class="span3">-->
<!--                    <p>--><?php //echo $form->select('areaTypeID', $areaTypeOptions, $property->getAreaTypeID(), ['style'
//                                                                                                             => 'width:100%;']);
//                        ?><!--</p>-->
<!--                </div>-->
<!--                <div class="span1">-->
<!--                    <p>&nbsp;</p>-->
<!--                </div>-->
<!--                <div class="span2">-->
<!--                    <p><strong>Area Type</strong></p>-->
<!--                </div>-->
<!--                <div class="span3">-->
<!--                    <p>--><?php //echo $form->select('areaTypeID', $areaTypeOptions, $property->getAreaTypeID(), ['style' => 'width:100%;']);
//                        ?><!--</p>-->
<!--                </div>-->
                <div class="span2">
                    <p><strong>Apartment Type</strong></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->select('apartmentTypeID', $apartmentTypeOptions, $property->getApartmentTypeID
                        (), ['style' => 'width:100%;
                    ']); ?></p>
                </div>
<!--                <div class="span1">-->
<!--                    <p>&nbsp;</p>-->
<!--                </div>-->
                <div class="span2">
                    <p><strong>Location</strong></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->select('locationID', $locationOptions, $property->getLocationID(), ['style'
                                                                                                             => 'width:100%;']);
                        ?></p>
                </div>

                                <div class="span1">
                                    <p>&nbsp;</p>
                                </div>

                <div class="span2">
                    <p><strong>Property Status</strong></p>
                </div>
                <div class="span3">
                    <p>
                        <?php echo $form->select('status', ['1' => 'Active', '0' => 'In Active'], $property->getStatus(),
                                                 ['style' =>
                                                      'width:100%;']);
                        ?>
                    </p>
                </div>

                <div class="span2">
                    <p><strong>Minimum Nights</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->number('minNights', $property->getMinNights(), ['style' => 'width:100%;', 'min' => 1, 'required' => true]); ?></p>
                </div>



                <div class="span1">
                    <p>&nbsp;</p>
                </div>


                <div style="clear:both;">

                <div class="span2">
                    <p><strong>CheckIn Time</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->text('checkInTime', $property->getCheckInTime(), ['style' => 'width:100%;', 'required' =>
                            true]); ?></p>
                </div>
                <div class="span0 checkInTime" style="cursor: pointer;">
                    X
                </div>
                <div class="span0">
                    <p>&nbsp;</p>
                </div>
                <div class="span0">
                    <p>&nbsp;</p>
                </div>

                <div class="span2">
                    <p><strong>CheckOut Time</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->text('checkOutTime', $property->getCheckOutTime(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>
                <div class="span0 checkOutTime" style="cursor: pointer;">
                    X
                </div>

                <div class="span12">
                    <h3>Amenities</h3>
                </div>
                <?php
                $propertyAmenities = $property->getAmenities(true);
                $propertyAmenities = array_column($propertyAmenities, 'value');
                $amenities         = Amenity::getAll();
                /** @var Amenity $amenity */
                foreach ($amenities as $amenity) { ?>

                        <div class="span3">
                            <p><?php echo $form->checkbox('amenities[]', $amenity->getID(), in_array($amenity->getName(), $propertyAmenities) ? 'checked' : '');
                                ?>
                            <span>
                                <?php echo $amenity->getName(); ?>
                            </span>
                            </p>

                        </div>

                <?php } ?>

                <div class="span12">
                    <h3>HomePage Filters</h3>
                </div>
                <?php
                $homePageFilters = $property->getHomePageFilters(true);
                $homePageFilters = array_column($homePageFilters, 'value');
                $filters         = HomePageFilters::getAll();

                /** @var HomePageFilters $filter */
                foreach ($filters as $filter) { ?>

                    <div class="span3">
                        <p><?php echo $form->checkbox('homePageFilters[]', $filter->getID(), in_array($filter->getName
                            (), $homePageFilters) ? 'checked' : '');
                            ?>
                            <span>
                                <?php echo $filter->getName(); ?>
                            </span>
                        </p>

                    </div>
                <?php } ?>
            </div>


        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>
                 <a class='btn danger ' href="<?php echo $this->action('delete', $property->getID()); ?>"> Delete Property </a>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>

    <script>
        function fileSelect(evt) {
            if (window.File && window.FileReader && window.FileList && window.Blob) {
                var files = evt.target.files;

                var result = '';
                var file;
                for (var i = 0; file = files[i]; i++) {
                    // if the file is not an image, continue
                    if (!file.type.match('image.*')) {
                        continue;
                    }

                    reader = new FileReader();
                    reader.onload = (function (tFile) {
                        return function (evt) {
                            var div = document.createElement('div');
                            div.innerHTML = '<img style="width: 300px;" src="' + evt.target.result + '" />';
                            $('#filesInfo').html(div);
                        };
                    }(file));
                    reader.readAsDataURL(file);
                }
            } else {
                alert('The File APIs are not fully supported in this browser.');
            }
        }

        document.getElementById('filesToUpload').addEventListener('change', fileSelect, false);
    </script>


    <script type="text/javascript">

        var EventDates = {
            $checkIn: $('#checkInTime'),
            $checkOut: $('#checkOutTime'),
            $clearCheckIn: $('.checkInTime'),
            $clearCheckOut: $('.checkOutTime'),

            $form: $('form')[0],
            $saveBtn: $('a[data-ref="btn-pr-save"]'),

            init: function () {
                EventDates.initDateTimePickers();
                EventDates.$saveBtn.click(EventDates.submitForm);
                EventDates.$clearCheckIn.click(function () {
                    EventDates.$checkIn.val('');
                });
                EventDates.$clearCheckOut.click(function () {
                    EventDates.$checkOut.val('');
                });
            },
            initDateTimePickers: function () {

                var config_with_time = {
                    enableTime: true,
                    dateFormat: 'H:i',
                    noCalendar: true
                };

                EventDates.$checkIn.flatpickr(config_with_time);
                EventDates.$checkOut.flatpickr(config_with_time);
            },
            submitForm: function () {
                EventDates.$form.submit();
            }
        };

        $(document).ready(function () {
            EventDates.init();
        });

    </script>

<?php } else if ($task == 'add') {

    /* @var array $localeOptions */
    /* @var RichTextHelper $rt */
    /** @var array $competitionOptions */
    /** @var string $preview */

    $rt = Loader::helper('rich_text');

    ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Add Property', false, false, false); ?>

    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('add_save'); ?>">
        <input type="hidden" id="fetch-user-token" name="fetch-user-token" value="<?=$fetchUserToken?>">
        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <div class="row">

                <div class="span3">
                    <p><strong>Name</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('name', '', ['style' => 'width:100%;', 'required' => true]); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Caption</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('caption', '', ['style' => 'width:100%;', 'required' => true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Description <span style="color: red;">*</span></strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->textarea('description', '', ['style' => 'width:100%;min-height:80px;
                    ', 'required' => true]); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Owner Name</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->select('uID', $userArr,'', ['style' => 'width:100%;', 'required' => true]);
                        ?></p>

                </div>

                <div class="span3">
                    <p><strong>Tourism Fee (in AED)</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('tourismFee', '', ['style' => 'width:100%;', 'required' =>
                            true]);?></p>
                </div>
                <div class="span12">
                    <h3>Pricing (in <?php echo CurrencyRates::DEFAULT_CURRENCY; ?>)</h3>
                </div>

                <div class="span2">
                    <p><strong>Per Day Price</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('perDayPrice', '', ['style' => 'width:100%;']);
                        ?></p>
                </div>
                <div class="span2">
                    <p>&nbsp;</p>
                </div>
                <div class="span2">
                    <p><strong>Monthly Price</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('monthlyPrice', '', ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p><strong>Weekly Price</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('weeklyPrice', '', ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span12">
                    <h3> Other Details</h3>
                </div>
                <div class="span2">
                    <p><strong>Latitude</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('latitude', '', ['style' => 'width:100%;']); ?></p>
                </div>
                <div class="span2">
                    <p>&nbsp;</p>
                </div>
                <div class="span2">
                    <p><strong>Longitude</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('longitude', '', ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p><strong>Number of Rooms</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('noOfRooms', '', ['style' => 'width:100%;']); ?></p>
                </div>
                <div class="span2">
                    <p>&nbsp;</p>
                </div>

                <div class="span2">
                    <p><strong>Bedrooms</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->select('bedrooms', $bedroomOptions,'', ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p><strong>Bathrooms</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('bathrooms', '', ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span2">
                    <p>&nbsp;</p>
                </div>

                <div class="span2">
                    <p><strong>Guests per room</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('maxGuests', '', ['style' => 'width:100%;']); ?></p>
                </div>

                <div class="span2">
                    <p><strong>Beds</strong></p>
                </div>
                <div class="span2">
                    <p><?php echo $form->text('beds', '', ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span2">
                    <p>&nbsp;</p>
                </div>

<!--                <div class="span2">-->
<!--                    <p><strong>Area Type</strong></p>-->
<!--                </div>-->
<!--                <div class="span3">-->
<!--                    <p>--><?php //echo $form->select('areaTypeID', $areaTypeOptions, '', ['style' => 'width:100%;']);
//                        ?><!--</p>-->
<!--                </div>-->

                <div class="span2">
                    <p><strong>Apartment Type</strong></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->select('apartmentTypeID', $apartmentTypeOptions, '', ['style' => 'width:100%;
                    ']); ?></p>
                </div>



                <div class="span2">
                    <p><strong>Location</strong></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->select('locationID', $locationOptions, '', ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span1">
                    <p>&nbsp;</p>
                </div>

                <div class="span2">
                    <p><strong>Property Status</strong></p>
                </div>
                <div class="span3">
                    <p>
                        <?php echo $form->select('status', $statusOptions, '',
                                                 ['style' =>
                                                      'width:100%;']);
                        ?>
                    </p>
                </div>

                <div class="span2">
                    <p><strong>Minimum Nights</strong> <span style="color: red;">*</span></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->number('minNights','', ['style' => 'width:100%;', 'min' => 1, 'required' => true]); ?></p>
                </div>



<!--                <div class="span2">-->
<!--                    <p><strong>Apartment Area</strong></p>-->
<!--                </div>-->
<!--                <div class="span3">-->
<!--                    <p>--><?php //echo $form->select('apartmentAreaID', $apartmentAreaOptions, '', ['style' => 'width:100%;
//                    ']); ?><!--</p>-->
<!--                </div>-->

                <div style="clear:both;">
                    <div class="span2">
                        <p><strong>CheckIn Time</strong> <span style="color: red;">*</span></p>
                    </div>
                    <div class="span3">
                        <p><?php echo $form->text('checkInTime', '', ['style' => 'width:100%;', 'required' =>
                                true]); ?></p>
                    </div>
                    <div class="span0 checkInTime" style="cursor: pointer;">
                        X
                    </div>
                    <div class="span0">
                        <p>&nbsp;</p>
                    </div>
                    <div class="span0">
                        <p>&nbsp;</p>
                    </div>

                    <div class="span2">
                        <p><strong>CheckOut Time</strong> <span style="color: red;">*</span></p>
                    </div>
                    <div class="span3">
                        <p><?php echo $form->text('checkOutTime', '', ['style' => 'width:100%;', 'required' =>
                                true]);
                            ?></p>
                    </div>
                    <div class="span0 checkOutTime" style="cursor: pointer;">
                        X
                    </div>
                </div>

                <div class="span12">
                    <h3>Amenities</h3>
                </div>
                <?php
                $amenities = Amenity::getAll();
                /** @var Amenity $amenity */
                foreach ($amenities as $amenity) { ?>
                    <div class="span2">
                        <p><?php echo $form->checkbox('amenities[]', $amenity->getID());
                            ?>
                            <span>
                                <?php echo $amenity->getName(); ?>
                            </span>
                        </p>

                    </div>
                <?php } ?>
            </div>


        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save', ['class' => 'primary']); ?>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
    <script type="text/javascript">

        var EventDates = {
            $checkIn: $('#checkInTime'),
            $checkOut: $('#checkOutTime'),
            $clearCheckIn: $('.checkInTime'),
            $clearCheckOut: $('.checkOutTime'),

            $form: $('form')[0],
            $saveBtn: $('a[data-ref="btn-pr-save"]'),

            init: function () {
                EventDates.initDateTimePickers();
                EventDates.$saveBtn.click(EventDates.submitForm);
                EventDates.$clearCheckIn.click(function () {
                    EventDates.$checkIn.val('');
                });
                EventDates.$clearCheckOut.click(function () {
                    EventDates.$checkOut.val('');
                });
            },
            initDateTimePickers: function () {

                var config_with_time = {
                    enableTime: true,
                    dateFormat: 'H:i',
                    noCalendar: true
                };

                EventDates.$checkIn.flatpickr(config_with_time);
                EventDates.$checkOut.flatpickr(config_with_time);
            },
            submitForm: function () {
                EventDates.$form.submit();
            }
        };

        $(document).ready(function () {
            EventDates.init();
        });

    </script>

<?php } elseif ($task == 'add_images') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }

        .thumbnail {
            position: relative;
            width: 50%;
        }

        .img-size {
            width: 400px;
            height: 300px;
        }

        .removeImage {
            z-index: 15;
        }

        .image {
            height: 150px;
            opacity: 1;
            display: block;
            width: 100%;
            transition: .5s ease;
            backface-visibility: hidden;
        }

        .top-right {
            font-size: 30px;
            position: absolute;
            color: #ff0000;
            right: 8px;
            top: 2px;
            cursor: pointer;
        }

        .middle {
            transition: .5s ease;
            opacity: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            text-align: center;
            z-index: -5;
        }

        .thumbnail:hover .image {
            opacity: 0.3;
        }

        .thumbnail:hover .middle {
            opacity: 1;
            z-index: 1;
        }

        .text {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            padding: 16px 32px;
            cursor: pointer;
        }
    </style>

    <?php
    /* @var Property $property */
    /* @var array $localeOptions */
    /** @var array $competitionOptions */
    /** @var array $selectedLinks */
    /** @var string $previewURL */
    /** @var string $preview */

    /* @var RichTextHelper $rt */
    $rt = Loader::helper('rich_text');

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var ValidationTokenHelper $vth */
    $vth = Loader::helper('validation/token');
    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Property', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('images_save'); ?>"
          id="propertyForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>
        <?php echo $form->hidden('thumbnailUrl', $configURL . '/thumbnail_save'); ?>
        <?php echo $form->hidden('imageRemoveUrl', $configURL . '/image_remove'); ?>

        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li class="active"><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>

            <div class="row">
                <?php if ($property->getThumbnail()) { ?>
                    <div class="span12">
                        <h3>Hero Image</h3>
                    </div>
                    <div class="span3">
                        <p>&nbsp;</p>
                    </div>
                    <div class="span8">
                        <div class="filesInfo">
                            <?php Property::outputImage($property->getThumbnail(), 350, 350, false, 'img-size');?>
                        </div>
                    </div>
                    <div class="span12">
                        <p>&nbsp;</p>
                    </div>
                    <div class="span12">
                        <p>&nbsp;</p>
                    </div>
                <?php } ?>
                <?php if ($property->getPropertyImages()) { ?>
                    <div class="span12">
                        <h3>More Images</h3>
                    </div>
                    <?php foreach ($property->getPropertyImages() as $image) { ?>
                        <div class="span3 thumbnail">
                            <div style="background: url('<?php echo Property::getImagePath($image, 300, 300); ?>');background-size: cover;"
                                 class="image">
                                <div class="top-right">
                                    <div class="removeImage" data-id="<?php echo $image->getID(); ?>">x</div>
                                </div>
                                <div class="middle">
                                    <div class="text set-thumb" data-id="<?php echo $image->getID(); ?>">Set as
                                        Thumbnail
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <div class="span12">
                    <p>&nbsp;</p>
                </div>
                <div class="span12">
                    <p>&nbsp;</p>
                </div>
                <div class="span12">
                    <h3>Add Image</h3>
                </div>
                <div class="span3">
                    <p><strong>Background Position</strong></p>
                </div>
                <div class="span8">
                    <p>
                        <?php echo $form->select('bgPosition', $bgPosition); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Image</strong></p>
                </div>
                <div class="span8">

                    <div class="filesInfo"></div>
                    <input type="file" name="image" id="filesToUpload"/>

                </div>


                <div class="span3">
                    <p><strong>Image Caption</strong></p>
                </div>
                <div class="span5">
                    <p>
                        <?php echo $form->text('caption', '', ['style' => 'width:100%;']); ?></p>
                </div>
                <div class="span12">
                    <p>&nbsp;</p>
                </div>
                <div class="span12">
                    <h4 style="margin-left: 160px;">OR</h4>
                </div>
                <div class="span12">
                    <p>&nbsp;</p>
                </div>
                <div class="span3">
                    <p><strong>Images (Bulk Upload)</strong></p>
                </div>
                <div class="span8">

                    <div class="filesInfo"></div>
                    <input type="file" name="images[]" id="filesToUploadMultiple" multiple/>

                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/detail/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Upload', ['class' => 'primary']); ?>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>

    <script>
      function fileSelect(evt) {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
          var files = evt.target.files;

          var result = '';
          var file;
          for (var i = 0; file = files[i]; i++) {
            // if the file is not an image, continue
            if (!file.type.match('image.*')) {
              continue;
            }

            reader = new FileReader();
            reader.onload = (function (tFile) {
              return function (evt) {
                var div = document.createElement('div');
                div.innerHTML = '<img style="width: 300px;" src="' + evt.target.result + '" />';
                $('#filesInfo').html(div);
              };
            }(file));
            reader.readAsDataURL(file);
          }
        } else {
          alert('The File APIs are not fully supported in this browser.');
        }
      }

        document.getElementById('filesToUpload').addEventListener('change', fileSelect, false);
        document.getElementById('filesToUploadMultiple').addEventListener('change', fileSelect, false);
        document.getElementById('thumbnail').addEventListener('change', fileSelect, false);
    </script>
    <script>
        $('.set-thumb').click(function () {
            var imageID = $(this).data('id');
            var propertyID = $('#propertyID').val();
            var thumbnailUrl = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '/' + $('#thumbnailUrl').val();
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", thumbnailUrl);

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "imageID");
            hiddenField.setAttribute("value", imageID);
            form.appendChild(hiddenField);
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "propertyID");
            hiddenField.setAttribute("value", propertyID);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        });
    </script>
    <script>
        $('.removeImage').click(function () {
            var imageID = $(this).data('id');
            var propertyID = $('#propertyID').val();
            var imageRemoveUrl = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '/' + $('#imageRemoveUrl').val();
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", imageRemoveUrl);

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "imageID");
            hiddenField.setAttribute("value", imageID);
            form.appendChild(hiddenField);
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "propertyID");
            hiddenField.setAttribute("value", propertyID);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        });
    </script>
<?php } else if ($task == 'add_property_rules') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php
    /* @var Property $property */

    /* @var RichTextHelper $rt */
    $rt = Loader::helper('rich_text');

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var ValidationTokenHelper $vth */
    $vth = Loader::helper('validation/token');
    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Property', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('save_property_rules'); ?>"
          id="propertyForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>

        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li class="active"><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>

            <div class="row">
                <div class="span12">
                    <h3>Property Rules</h3>
                </div>
                <div class="span11">
                    <?php echo $rt->getRichTextEditor('propertyRules',$property->getPropertyRules());?>
                </div>
                <div class="span12">
                    <p>&nbsp;</p>
                </div>
                <div class="span12">
                    <h3>Cancellation Policy</h3>
                </div>
                <div class="span11">
                    <?php echo $rt->getRichTextEditor('cancellationPolicy',$property->getCancellationPolicy());?>
                </div>
                <div class="span12">
                    <p>&nbsp;</p>
                </div>
                <div class="span12">
                    <h3>Location Description</h3>
                </div>
                <div class="span11">
                    <?php echo $rt->getRichTextEditor('locationDescription',$property->getLocationDescription());?>
                </div>
            </div>

        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/detail/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Details', ['class' => 'primary']); ?>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } else if ($task == 'add_property_facilities') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
        span
        {
            position: relative;
            top: 6px;
        }
    </style>

    <?php
    /* @var Property $property */

    /* @var RichTextHelper $rt */
    $rt = Loader::helper('rich_text');

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var ValidationTokenHelper $vth */
    $vth = Loader::helper('validation/token');
    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Property', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('save_property_facilities'); ?>"
          id="propertyForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>

        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li class="active"><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>

            <div class="row">


                <div class="span12" style="display: flex">
                    <h3>Facilities</h3>&nbsp;<span> (Enter Prices wherever applicable)</span>
                </div>
                <?php
                $propertyFacilities = $property->getPropertyFacilities(true,false);
                $facilities         = Facility::getAll();
                /** @var Facility $facilities */
                foreach ($facilities as $facility) {
                    $price = isset($propertyFacilities[$facility->getID()]) ?
                        $propertyFacilities[$facility->getID()]['price'] : '';
                    ?>
                    <div class="span5">
                        <div class="span0" style="float: left">
                            <p><?php echo $facility->getName(); ?></p>
                        </div>
                        <div class="span3" style="float: right">
                            <p>AED <?php echo $form->text("facilities[{$facility->getID()}]",
                                                          $price,['style' => 'width:60%;']);
                                ?>

                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/detail/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Details', ['class' => 'primary']); ?>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } else if ($task == 'add_property_seasons') { ?>
    <style>
        .ccm-pane-options .row.offset-bottom {
            margin-bottom: 10px;
        }

        table.ccm-results-list tr.ccm-list-record td.status {
            font-weight: bold;
        }

        table.ccm-results-list tr.ccm-list-record td.status.green {
            color: green;
        }

        table.ccm-results-list tr.ccm-list-record td.status.red {
            color: red;
        }

        table.ccm-results-list tr.ccm-list-record td.status.blue {
            color: blue;
        }
    </style>

    <?php
    /** @var array $filterOptions */
    /** @var array $itemsOptions */
    /** @var PropertyList $propertyList */
    /** @var array SeasonList $seasonList */

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');
    ?>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Property Seasons', false, false, false); ?>

    <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
        <ul class="tabs">
            <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
            <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
            </li>
            <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
            </li>
            <li><a href="<?php echo
                $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
            </li>
            <li class="active"><a href="<?php echo
                $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
            </li>
            <li><a href="<?php echo
                $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
            </li>
        </ul>
        <br style="clear: both;"/>
        <div class="ccm-pane-options" style="border-top: 1px solid #ddd;">
            <form action="<?php echo $this->action('add_property_seasons/'.$property->getID()); ?>">
                <div class="ccm-pane-options-permanent-search">
                    <div class="row offset-bottom">
                        <div class="span3">
                            <?php echo $form->label('seasonName', 'Season Name'); ?>
                            <div class="controls">
                                <?php echo $form->text('seasonName'); ?>
                            </div>
                        </div>
                        <div class="span3 dateFilter">
                            <label class="control-label">Date</label>

                            <div class="controls">
                                <?php echo $fdth->date('seasonStartDate'); ?>
                                to
                                <?php echo $fdth->date('seasonEndDate'); ?>
                            </div>
                        </div>
                        <div class="span2">
                            <?php echo $form->label('filter', 'Filters'); ?>
                            <div class="controls">
                                <?php echo $form->select('filter', $filterOptions, ['style' => 'width: 140px;']); ?>
                            </div>
                        </div>
                        <div style="display: none;" class="span1">
                            <?php echo $form->label('items', '&nbsp;#'); ?>
                            <div class="controls">
                                <?php echo $form->select('items', $itemsOptions, ['style' => 'width: 60px;']); ?>
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
    </div>



    <div class="ccm-pane-body">
        <div class="ccm-list-wrapper">
            <?php echo $interface->button('Add Season', View::url($configURL . '/add_season/'.$property->getID()), 'right', 'primary',
                ['style' => 'margin-bottom: 10px;']); ?>

            <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
                <tr>
                    <th>Season Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($seasons as $i => $season) { ?>
                    <?php
                    /* @var Property $property */
                    $seasonID      = $season->getId();
                    $seasonEditURL = View::url($configURL . '/edit_season/' . $property->getID().'/'.$seasonID);
                    $seasonName           = $season->getSeasonName();
                    $seasonStartDate           = $season->getSeasonStartDate();
                    $seasonEndDate           = $season->getSeasonEndDate();
                    $seasonPrice           = $season->getSeasonPrice();
                    $status          = $season->getSeasonStatus();


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
                        <td><a href="<?php echo $seasonEditURL; ?>"><?php echo $th->wordSafeShortText($seasonName, 60); ?></a></td>
                        <td><?php echo $seasonStartDate; ?></td>
                        <td><?php echo $seasonEndDate; ?></td>
                        <td><?php echo $seasonPrice; ?></td>
                        <td class="status <?php echo $statusColor; ?>"><?php echo $status; ?></td>
                    </tr>
                <?php } ?>
            </table>

        </div>
        <?php $seasonList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $seasonList->displayPagingV2(); ?>
    </div>

    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".chosen-select").chosen(ccmi18n_chosen);
        });
    </script>
<?php } else if ($task === 'add_season') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php

   /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');

    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Property Seasons - Add', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" autocomplete="off" action="<?php echo $this->action('save_season'); ?>"
          id="seasonForm">
       <?php echo $form->hidden('propertyID', $property->getID()); ?>
        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li class="active"><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>
            <div class="row">
                <div class="span3">
                    <p><strong>Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('seasonName', '', '');
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Start Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('seasonStartDate', ''); ?></p>
                </div>


                <div class="span3">
                    <p><strong>End Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('seasonEndDate', ''); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Price</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('seasonPrice', '', '');
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Status</strong></p>
                </div>
                <div class="span8">
                    <p>
                        <?php echo $form->select('seasonStatus', ['1' => 'Active', '0' => 'In Active'], '',
                            '');
                        ?>
                    </p>
                </div>

                <div class="span3">
                    <p><strong>Minimum nights</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->number('minNightsSeason', 0, '');
                        ?></p>
                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/add_property_seasons/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save', ['class' => 'primary']); ?>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } else if ($task === 'edit_season') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php

    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');

    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Property Seasons - Edit', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('update_season'); ?>"
          id="seasonForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>
        <?php echo $form->hidden('seasonID', $season->getID()); ?>
        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li class="active"><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>
            <div class="row">
                <div class="span3">
                    <p><strong>Season Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('seasonName', $season->getSeasonName(), ['style' => 'width:100%;']);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Season Start Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('seasonStartDate', $season->getSeasonStartDate()); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Booking End Date</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $fdth->date('seasonEndDate', $season->getSeasonEndDate()); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Price</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('seasonPrice', $season->getSeasonPrice(), '');
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Property Status</strong></p>
                </div>
                <div class="span8">
                    <p>
                        <?php echo $form->select('seasonStatus', ['1' => 'Active', '0' => 'In Active'], $season->getSeasonStatus(),
                            ['style' =>
                                 'width:100%;']);
                        ?>
                    </p>
                </div>

                <div class="span3">
                    <p><strong>Minimum nights</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->number('minNightsSeason', $season->getMinNightsSeason(), '');
                        ?></p>
                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
             <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/add_property_seasons/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>

                <a href="<?php echo $this->action('delete_season', $property->getID(), $season->getID()); ?>" class="btn danger">Delete Season</a>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } else if ($task === 'view_block_dates') { ?>
    <style>
        .ccm-pane-options .row.offset-bottom {
            margin-bottom: 10px;
        }

        table.ccm-results-list tr.ccm-list-record td.status {
            font-weight: bold;
        }

        table.ccm-results-list tr.ccm-list-record td.status.green {
            color: green;
        }

        table.ccm-results-list tr.ccm-list-record td.status.red {
            color: red;
        }

        table.ccm-results-list tr.ccm-list-record td.status.blue {
            color: blue;
        }

        /* Tooltip text */
        .blockDateToolTip .tooltiptext {
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
        .blockDateToolTip:hover .tooltiptext {
            visibility: visible;
        }
    </style>

    <?php
    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');
    ?>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Property Block Dates', false, false, false); ?>

    <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
        <ul class="tabs">
            <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
            <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
            </li>
            <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
            </li>
            <li><a href="<?php echo
                $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
            </li>
            <li><a href="<?php echo
                $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
            </li>
            <li class="active"><a href="<?php echo
                $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
            </li>
        </ul>
        <br style="clear: both;"/>
        <div class="ccm-pane-options" style="border-top: 1px solid #ddd;">
            <form action="<?php echo $this->action('view_block_dates/'.$property->getID()); ?>">
                <div class="ccm-pane-options-permanent-search">
                    <div class="row offset-bottom">
                        <div class="span3 dateFilter">
                            <label class="control-label">Date</label>

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
    </div>



    <div class="ccm-pane-body">
        <div class="ccm-list-wrapper">
            <?php echo $interface->button('Add Block Date', View::url($configURL . '/add_block_dates/'.$property->getID()), 'right', 'primary',
                ['style' => 'margin-bottom: 10px;']); ?>
            <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
                <tr>
                    <th>Action</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
                <?php foreach ($blockDates as $i => $blockDate) { ?>
                    <?php
                    /* @var Property $property */
                    /* @var PropertyBlockDates $blockDate */
                    $blockID      = $blockDate->getId();
                    $startDate           = $blockDate->getStartDate();
                    $endDate           = $blockDate->getEndDate();
                    $editBlockDateURL = View::url($configURL . '/edit_block_date/' . $property->getID().'/'.$blockID);
                    ?>
                    <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                        <td><div class="blockDateToolTip"><a href="<?php echo $editBlockDateURL; ?>"><i class="icon-edit" style="margin-left: 14px;"></i></a><span class="tooltiptext">View/Edit</span></div></td>
                        <td><?php echo $startDate; ?></td>
                        <td><?php echo $endDate; ?></td>
                        <td><?php echo $blockDate->getDescription(); ?></td>
                        <td><?php echo $blockDate->getPrice(); ?></td>
                    </tr>
                <?php } ?>
            </table>

        </div>
        <?php $blockDatesList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $blockDatesList->displayPagingV2(); ?>
    </div>

    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } else if ($task === 'add_block_dates') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php

    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');

    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Property Block Dates - Add', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" autocomplete="off" action="<?php echo $this->action('save_block_date'); ?>"
          id="seasonForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>
        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li class="active"><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>
            <div class="row">
                <div class="span3">
                    <p><strong>Date Range</strong></p>
                </div>
                <div class="span8">
                    <p>
                        <?php echo $fdth->date('startDate'); ?>
                        to
                        <?php echo $fdth->date('endDate'); ?>
                        <?php echo $form->text('blockDesc','',['placeholder' => 'Add details']); ?>
                        <?php echo $form->text('blockPrice','',['placeholder' => 'Add total price', 'style' => 'width: 175px;']); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
            <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/view_block_dates/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save', ['class' => 'primary']); ?>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } else if ($task === 'edit_block_date') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php

    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');

    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Property Block Dates - Edit', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" autocomplete="off" action="<?php echo $this->action('update_block_date'); ?>"
          id="seasonForm">
        <?php echo $form->hidden('propertyID', $property->getID()); ?>
        <?php echo $form->hidden('blockID', $blockDate->getID()); ?>
        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
            <ul class="tabs">
                <li><a href="<?php echo $this->action('detail', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Edit')?></a></li>
                <li><a href="<?php echo $this->action('add_images', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Add Images')?></a>
                </li>
                <li><a href="<?php echo $this->action('add_property_rules', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Rules')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_facilities', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Facilities')?></a>
                </li>
                <li><a href="<?php echo
                    $this->action('add_property_seasons', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Property Seasons')?></a>
                </li>
                <li class="active"><a href="<?php echo
                    $this->action('view_block_dates', $property->getID()); ?>" onclick="$('ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide();"><?php   echo t('Block Dates')?></a>
                </li>
            </ul>
            <br style="clear: both;"/>
            <div class="row">
                <div class="span3">
                    <p><strong>Date Range</strong></p>
                </div>
                <div class="span8">
                    <p>
                        <?php echo $fdth->date('startDate',$blockDate->getStartDate()); ?>
                        to
                        <?php echo $fdth->date('endDate',$blockDate->getEndDate()); ?>
                        <?php echo $form->text('blockDesc',$blockDate->getDescription(),['placeholder' => 'Add details']); ?>
                        <?php echo $form->text('blockPrice',($blockDate->getPrice())?$blockDate->getPrice():'',['placeholder' => 'Add total price', 'style' => 'width: 175px;']); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="ccm-pane-footer">
            <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL.'/view_block_dates/'.$property->getID()); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>
                <a class='btn danger ' href="<?php echo View::url($configURL . '/delete_block_date/'.$property->getID().'/'.$blockDate->getID()); ?>"> Delete Block Date</a>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
<?php } ?>


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
    /* @var array $itemsOptions */
    /* @var FacilityList $facilityList */

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    ?>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Facilities', false, false, false); ?>

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
            <?php echo $interface->button('Add Facility', View::url($configURL . '/add'), 'right', 'primary',
                                          ['style' => 'margin-bottom: 10px;']); ?>

            <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
                <tr>
                    <th style="width:90%;">Title</th>
                    <th style="width:10%;">Action</th>
                </tr>
                <?php foreach ($facilities as $i => $facility) { ?>
                    <?php
                    /* @var Facility $facility */
                    $facilityID      = $facility->getId();
                    $facilityEditURL = View::url($configURL . '/detail/' . $facilityID);
                    $title           = $facility->getName();
                    ?>
                    <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                        <td><a href="<?php echo $facilityEditURL; ?>"><?php echo $th->wordSafeShortText($title, 60); ?></a>
                        </td>
                        <td><a href="<?php echo $this->action('delete', $facility->getID()); ?>"> Delete </a></td>
                    </tr>
                <?php } ?>
            </table>

        </div>
        <?php $facilityList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $facilityList->displayPagingV2(); ?>
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

        .image {
            height: auto;
            opacity: 1;
            display: block;
            width: 100%;
            transition: .5s ease;
            backface-visibility: hidden;
            position: relative;
        }

        .thumbnail-facility {
            display: block;
            padding: 4px;
            line-height: 1;
        }

        .top-left {
            position: absolute;
            top: 0;
            left: 3px;
        }

        .removeImage {
            font-size: 1.5em;
            cursor: pointer;
        }
    </style>

    <?php
    /* @var Facility $facility */
    $th = Loader::helper('text');
    /** @var ValidationTokenHelper $vth */
    $vth = Loader::helper('validation/token');
    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Facility', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('edit_save'); ?>"
          id="facilityForm">
        <?php echo $form->hidden('facilityID', $facility->getID()); ?>
        <?php echo $form->hidden('imageRemoveUrl', $configURL . '/image_remove'); ?>

        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <br>

            <div class="row">
                <div class="span3">
                    <p><strong>Facility ID</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $facility->getId(); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('name', $facility->getName(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>
                <div class="span12">&nbsp;</div>
                <div class="span3">
                    <p><strong>Icon</strong></p>
                </div>
                <?php if ($facility->getIcon()) { ?>
                    <div class="span3 thumbnail-facility">
                        <div class="image">
                            <img src="<?php echo $facility->getImagePath(); ?>">
                            <div class="top-left">
                                <div class="removeImage">x</div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="span8">
                        <div id="filesInfo"></div>
                        <input type="file" name="image" id="filesToUpload"/>

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
                <a class='btn danger ' href="<?php echo $this->action('delete', $facility->getID()); ?>"> Delete </a>
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
                            div.innerHTML = '<img style="width: 50px;" src="' + evt.target.result + '" />';
                            $('#filesInfo').html(div);
                        };
                    }(file));
                    reader.readAsDataURL(file);
                }
            } else {
                alert('The File APIs are not fully supported in this browser.');
            }
        }

        if (typeof $('#filesToUpload').get(0) !== "undefined") {
            document.getElementById('filesToUpload').addEventListener('change', fileSelect, false);
        }
        $('.top-left').click(function () {
            var fID = $('#facilityID').val();
            var imageRemoveUrl = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '/' + $('#imageRemoveUrl').val();
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", imageRemoveUrl);

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "fID");
            hiddenField.setAttribute("value", fID);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        });
    </script>

<?php } else if ($task == 'add') { ?>
    <style>
        #ccm-dashboard-discount-body h3 {
            margin-bottom: 10px;
            text-decoration: underline;
        }

        #ccm-dashboard-discount-body .row {
            margin-bottom: 15px;
        }
    </style>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Add Facility', false, false, false); ?>

    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('add_save'); ?>">

        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">
            <br>
            <div class="row">

                <div class="span3">
                    <p><strong>Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('name', '', ['style' => 'width:100%;', 'required' => true]); ?></p>
                </div>
                <div class="span12">&nbsp;</div>
                <div class="span3">
                    <p><strong>Icon</strong></p>
                </div>
                <div class="span8">
                    <div id="filesInfo"></div>
                    <input type="file" name="image" id="filesToUpload"/>

                </div>
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
                            div.innerHTML = '<img style="width: 50px;" src="' + evt.target.result + '" />';
                            $('#filesInfo').html(div);
                        };
                    }(file));
                    reader.readAsDataURL(file);
                }
            } else {
                alert('The File APIs are not fully supported in this browser.');
            }
        }

        if (typeof $('#filesToUpload').get(0) !== "undefined") {
            document.getElementById('filesToUpload').addEventListener('change', fileSelect, false);
        }
    </script>

<?php } ?>
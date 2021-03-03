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
    /* @var array $filterOptions */
    /* @var array $itemsOptions */
    /* @var BillList $billList */
    /* @var array $bills */

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    /** @var FormDateTimeHelper $fdth */
    $fdth = Loader::helper('form/date_time');
    ?>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Maintenance Bills', false, false, false); ?>

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
            <?php echo $interface->button('Add Bill', View::url($configURL . '/add'), 'right', 'primary',
                                          ['style' => 'margin-bottom: 10px;']); ?>

            <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
                <tr>
                    <th>Title</th>
                </tr>
                <?php foreach ($bills as $i => $bill) { ?>
                    <?php
                    /* @var Bill $bill */
                    $billID      = $bill->getId();
                    $billEditURL = View::url($configURL . '/detail/' . $billID);
                    $title       = $bill->getDescription();
                    ?>
                    <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                        <td><a href="<?php echo $billEditURL; ?>"
                               target="_blank"><?php echo $th->wordSafeShortText($title, 60); ?></a></td>

                    </tr>
                <?php } ?>
            </table>

        </div>
        <?php $billList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $billList->displayPagingV2(); ?>
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


        .removeImage {
            z-index: 15;
        }

        .top-right {
            font-size: 30px;
            position: absolute;
            color: #ff0000;
            right: 2px;
            top: 0px;
            cursor: pointer;
        }
        .pdfobject-container { height: 30rem; border: 1rem solid rgba(0,0,0,.1); }
    </style>

    <?php
    /* @var Bill $bill */
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
    $vth      = Loader::helper('validation/token');
    $property = $bill->getProperty();

    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Bill', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('edit_save'); ?>"
          id="billForm">
        <div class="ccm-pane-options">

            <!--            --><?php //echo $form->submit('billPreview', 'Preview', ['class' => 'default']); ?>
            <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>
            <?php echo $form->hidden('imageRemoveUrl', $configURL . '/image_remove'); ?>
            <?php echo $form->hidden('billID', $bill->getID()); ?>
        </div>

        <div class="ccm-pane-body ccm-pane-body-footer" id="ccm-dashboard-discount-body">

            <br>

            <div class="row">
                <div class="span3">
                    <p><strong>Bill ID</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $bill->getID(); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Property Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->select('pID', [$property->getID() => $property->getName() . '-' . $property->getCaption()],
                                                $property->getName(), ['style' => 'width:100%;', 'required' => true, 'class' => 'property-names']);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Amount</strong>(AED)</p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('amount', $bill->getAmount(), ['style' => 'width:100%;', 'required' => true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Description</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->textarea('description', $bill->getDescription(), ['style' => 'width:100%;min-height:80px;
                    ', 'required' => true]); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Maintained By</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('fixedBy', $bill->getFixedBy(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Date</strong></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->text('date', $bill->getDate(), ['style' => 'width:100%;']);
                        ?></p>
                </div>
                <div class="span0 date" style="cursor: pointer;">
                    X
                </div>
                <div class="span12">&nbsp;</div>
                <div class="span3">
                    <p><strong>PDF</strong></p>
                </div>
                <?php if ($bill->getBillImage()) { ?>
                    <div class="span8" style="position: relative">
                        <div class="image">
                        </div>
                        <div class="top-right">
                            <div class="removeImage">x</div>
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
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>
    <script>
        $('.removeImage').click(function () {
            var billID = $('#billID').val();
            var imageRemoveUrl = CCM_BASE_URL + CCM_DISPATCHER_FILENAME + '/' + $('#imageRemoveUrl').val();
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", imageRemoveUrl);

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", "billID");
            hiddenField.setAttribute("value", billID);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        });
        PDFObject.embed('<?php echo $bill->getPDFPath(); ?>', ".image");
    </script>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
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

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Add Bill', false, false, false); ?>

    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('add_save'); ?>">
        <div class="ccm-pane-options">
            <?php echo $form->submit('submit', 'Save', ['class' => 'primary']); ?>
        </div>

        <div class="ccm-pane-body ccm-pane-body-footer" id="ccm-dashboard-discount-body">

            <br>


            <div class="row">

                <div class="span3">
                    <p><strong>Property Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->select('pID', ["" => "Please enter 1 or more characters"], '', ['style' => 'width:100%;', 'required' =>
                            true,
                                                                                                         'class' => 'property-names']);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>Amount</strong>(AED)</p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('amount', '', ['style' => 'width:100%;', 'required' => true]); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Description</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->textarea('description', '', ['style' => 'width:100%;min-height:80px;
                    ', 'required' => true]); ?></p>
                </div>

                <div class="span3">
                    <p><strong>Maintained By</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('fixedBy','', ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

                <div class="span3">
                    <p><strong>PDF</strong></p>
                </div>
                <div class="span8">

                    <div id="filesInfo"></div>
                    <input type="file" name="image" id="filesToUpload"/>

                </div>

                <div class="span3">
                    <p><strong>Date</strong></p>
                </div>
                <div class="span3">
                    <p><?php echo $form->text('date', '', ['style' => 'width:100%;']);
                        ?></p>
                </div>
                <div class="span0 date" style="cursor: pointer;">
                    X
                </div>

            </div>


        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>
<?php } ?>
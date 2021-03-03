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
    /* @var FacilityList $apartmentTypeList */

    /** @var TextHelper $th */
    $th = Loader::helper('text');
    ?>

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Apartment Types', false, false, false); ?>

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
            <?php echo $interface->button('Add Apartment Type', View::url($configURL . '/add'), 'right', 'primary',
                                          ['style' => 'margin-bottom: 10px;']); ?>

            <table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list">
                <tr>
                    <th style="width:90%;">Title</th>
                    <th style="width:10%;">Action</th>
                </tr>
                <?php foreach ($apartmentTypes as $i => $apartmentType) { ?>
                    <?php
                    /* @var Facility $apartmentType */
                    $apartmentTypeID      = $apartmentType->getId();
                    $apartmentTypeEditURL = View::url($configURL . '/detail/' . $apartmentTypeID);
                    $title           = $apartmentType->getName();
                    ?>
                    <tr class="ccm-list-record <?php if ($i % 2 === 1) echo 'ccm-list-record-alt'; ?>">
                        <td><a href="<?php echo $apartmentTypeEditURL; ?>"><?php echo $th->wordSafeShortText($title, 60); ?></a>
                        </td>
                        <td><a href="<?php echo $this->action('delete', $apartmentType->getID()); ?>"> Delete </a></td>
                    </tr>
                <?php } ?>
            </table>

        </div>
        <?php $apartmentTypeList->displaySummary(); ?>
    </div>

    <div class="ccm-pane-footer">
        <?php $apartmentTypeList->displayPagingV2(); ?>
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
    /* @var Facility $apartmentType */
    $th = Loader::helper('text');
    /** @var ValidationTokenHelper $vth */
    $vth = Loader::helper('validation/token');
    ?>
    <?php echo $cdh->getDashboardPaneHeaderWrapper('Edit Apartment Type', false, false, false); ?>
    <form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('edit_save'); ?>"
          id="apartmentTypeForm">
        <?php echo $form->hidden('apartmentTypeID', $apartmentType->getID()); ?>

        <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

            <br>

            <div class="row">
                <div class="span3">
                    <p><strong>Facility ID</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $apartmentType->getId(); ?></p>
                </div>


                <div class="span3">
                    <p><strong>Name</strong></p>
                </div>
                <div class="span8">
                    <p><?php echo $form->text('name', $apartmentType->getName(), ['style' => 'width:100%;', 'required' =>
                            true]);
                        ?></p>
                </div>

            </div>


        </div>
        <div class="ccm-pane-footer">
            <span style="float:left;">
                <a class='btn btn-default' href="<?php echo View::url($configURL); ?>"> Cancel </a>
            </span>
            <span style="float:right;">
                <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>
                <a class='btn danger ' href="<?php echo $this->action('delete', $apartmentType->getID()); ?>"> Delete </a>
            </span>
        </div>
    </form>
    <?php echo $cdh->getDashboardPaneFooterWrapper(false); ?>

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

    <?php echo $cdh->getDashboardPaneHeaderWrapper('Add Apartment Type', false, false, false); ?>

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

<?php } ?>
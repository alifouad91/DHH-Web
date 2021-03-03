<?php
/* @var View $this */
/* @var $c */
/* @var FormHelper $form */
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
/** @var ConcreteDashboardHelper $cdh */
$cdh = Loader::helper('concrete/dashboard');
/** @var CurrencyRates $currency */

?>


<?php echo $cdh->getDashboardPaneHeaderWrapper('Currency Rates', false, false, false); ?>

<style>
    #ccm-dashboard-discount-body h3 {
        margin-bottom: 10px;
        text-decoration: underline;
    }

    #ccm-dashboard-discount-body .row {
        margin-bottom: 15px;
    }
</style>

<form method="POST" enctype="multipart/form-data" action="<?php echo $this->action('save'); ?>">

    <div class="ccm-pane-body" id="ccm-dashboard-discount-body">

        <br>
        <?php if ($success_message) { ?>
            <div class="alert alert-info success_message">
                <?php echo $success_message ?>
            </div>
        <?php } ?>

        <div class="row">

            <div class="span3">
                <p><strong>Euro</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('eur', $currency->getEur(), ''); ?></p>
            </div>

            <div class="span3">
                <p><strong>American Dollar</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('usd', $currency->getUsd(), ''); ?></p>
            </div>

            <div class="span3">
                <p><strong>Saudi Riyal</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('sar', $currency->getSar(), ''); ?></p>
            </div>

            <div class="span3">
                <p><strong>Russian Ruble</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('rub', $currency->getRub(), ''); ?></p>
            </div>

            <div class="span3">
                <p><strong>Kuwaiti Dinar</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('kwd', $currency->getKwd(), ''); ?></p>
            </div>
        </div>


    </div>
    <div class="ccm-pane-footer">
        <?php echo $form->submit('submit', 'Save Changes', ['class' => 'primary']); ?>
    </div>
</form>

<script>

    $(document).ready(function () {
        setTimeout(function () {
            $('.success_message').fadeOut();

        }, 3000);
    });

</script>
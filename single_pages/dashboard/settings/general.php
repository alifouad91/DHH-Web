<?php
/* @var View $this */
/* @var $c */
/* @var FormHelper $form */
$form = Loader::helper('form');
defined('C5_EXECUTE') or die("Access Denied.");
/** @var ConcreteDashboardHelper $cdh */
$cdh = Loader::helper('concrete/dashboard');

?>

<?php echo $cdh->getDashboardPaneHeaderWrapper('Setting', false, false, false); ?>

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
            <div class="span11">
                <p>
                <h3>SMS Settings :</h3></p>
            </div>
            <div class="span3">
                <p><strong>Enable SMS</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->checkbox('enableSms', 1 , $enableSms ); ?></p>
            </div>

            <div class="span3">
                <p><strong>SMS API Key</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('smsApiKey', $smsApiKey, ['style'=>'width:500px' ]); ?></p>
            </div>


            <div class="span3">
                <p><strong>Endpoint</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('smsApiEndpoint', $smsApiEndpoint, ''); ?></p>
            </div>


            <div class="span3">
                <p><strong>From Number</strong></p>
            </div>
            <div class="span8">
                <p><?php echo $form->text('smsApiFromNumber', $smsApiFromNumber, ''); ?></p>
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
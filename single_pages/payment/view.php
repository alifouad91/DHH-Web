<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php /** @var UserInfo $profile  */
$actionUrl = View::url('payment/submit');
//https://8fe45f85.ngrok.io/dhh/index.php/payment/submit (ngrok url to test)

?>

<?php if ($task == 'payment') { ?>
<div class="page__wrapper page__limit">
    <div class="" data-uid="" data-id=""></div>
    <form method="post" action="<?php echo $actionUrl; ?>">
        <input type="hidden" name="bID" value="88"/>
        <div class="control-group active">
            <label>First Name</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_first_name" type="text" name="billing_first_name" value="<?php echo $profile->getBillingFirstName(); ?>" class="span5 ccm-input-text">
            </div>
        </div>
        <div class="control-group active">
            <label>Last Name</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_last_name" type="text" name="billing_last_name" value="<?php echo $profile->getBillingLastName(); ?>" class="span5 ccm-input-text">
            </div>
        </div>
        <div class="control-group active">
            <label for="billing_email">Email Address</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_email" type="text" name="billing_email" value="<?php echo $profile->getBillingEmail(); ?>" class="ccm-input-text">
            </div>
        </div>
        <div class="control-group active">
             <label for="billing_phone">Phone</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_phone" type="text" name="billing_phone" value="<?php echo $profile->getBillingPhone(); ?>" class="span5 ccm-input-text">
            </div>
        </div>
        <div class="control-group active">
             <label for="billing_address">Address</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_address" type="text" name="billing_address" value="<?php echo $profile->getBillingAddress(); ?>" class="span5 ccm-input-text">
            </div>
        </div>
        <div class="control-group active">
             <label for="billing_city">City</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_city" type="text" name="billing_city" value="<?php echo $profile->getBillingCity(); ?>" class="span5 ccm-input-text">
            </div>
        </div>
        <div class="control-group active">
            <label for="billing_country">Country</label>
            <span class="ccm-required">*</span>
            <div class="controls">
                <input id="billing_country" type="text" name="billing_country" value="<?php echo $profile->getBillingCountry(); ?>" class="span5 ccm-input-text">
            </div>
        </div>

        <div class="ccm-core-commerce-cart-buttons">
            <input type="submit" class="ccm-core-commerce-checkout-button-next btn ccm-input-submit" id="submit_next" name="submit_next" value="Next">            </div>
        <div class="ccm-spacer"></div>
    </form>

</div>
<?php } elseif ($task == 'submit') { ?>

    <iframe src="<?php echo $production_url?>" id="paymentFrame" width="482" height="450" frameborder="0" scrolling="No" ></iframe>
    <script type="text/javascript">
        $(document).ready(function(){
            window.addEventListener('message', function(e) {
                $("#paymentFrame").css("height",e.data['newHeight']+'px');
            }, false);

        });
    </script>
<?php }  elseif ($task == 'afterSubmit') { ?>
    <div>Payment status <?php echo $status; ?></div>
<?php } elseif ($task == 'cancelled') { ?>
    <div>Cancelled</div>
<?php } ?>
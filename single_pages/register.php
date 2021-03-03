<?php defined('C5_EXECUTE') or die("Access Denied.");

$captcha = Loader::helper('validation/captcha');

$FBloginUrl = Facebook::getURL();


?>

<div class="page__wrapper">
    <div class="page__login">
        <div class="general__card <?php echo $success; ?>">
            <?php 
                if($error) {
                    echo $error->output();
                }
            ?>
            <?php if($success) {?>
                <div class="rows">
                    <div class="register-success">
                        <?php switch ($success) {
                            case "registered":
                                ?>
                                <p><strong>Confirmed!</strong><br/>Your email address { email address here } has been verified and now all you need is to <button class="button button__primary button__primary--yellow button__textdark button__downloadApp" target="_blank"><span class="button__label initial">Download the App</span><span class="button__label over"><a href="#" class="linkHoverX-style2">iOS</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" class="linkHoverX-style2">Android</a></span></button>, if you have not already done so.</p>
                                <a href="<?php echo $this->url('/')?>" class="button button__primary button__primary--yellow button__textdark" target="_blank"><span class="button__label">Click here to continue back to the website</span></a>

                                <?php
                                break;
                            case "validate":
                                ?>
                                <h4>Almost registered...</h4>
                                <p>Please check your Inbox or Spam folder! A link has been sent to verify your email ID. Please click on it!</p>
                                <!--  ≈. -->
                                <img src="<?php echo $this->getThemePath(); ?>/dist/images/form-success-image.svg" alt="<?php echo e(SITE); ?>"/>
                                <a href="<?php echo $this->url('/')?>" ><button class="ant-btn ant-btn-primary" >BACK TO HOME PAGE</button></a>
                                <?php
                                break;
                            case "pending":
                                ?>
                                <p><?php echo $successMsg ?></p>
                                <p><a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?></a></p>
                                <div class="loginDownloadBtn">
                                <button class="button button__primary button__primary--yellow button__textdark button__downloadApp desktop-app" target="_blank"><span class="button__label initial">Download App</span><span class="button__label over"><a href="https://itunes.apple.com/us/app/dxbdw/id1435925730?ls=1&mt=8" target="_blank" class="linkHoverX-style2">iOS</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://play.google.com/store/apps/details?id=com.tentwenty.dubaidesignweek" target="_blank" class="linkHoverX-style2">Android</a></span></button>
                                <button class="button button__primary button__primary--yellow button__textdark button__downloadApp ios-app" target="_blank">
                                    <span class="button__label"><a href="https://itunes.apple.com/us/app/dxbdw/id1435925730?ls=1&mt=8" target="_blank" class="">Download App</a></span>
                                </button>
                                <button class="button button__primary button__primary--yellow button__textdark button__downloadApp android-app" target="_blank">
                                    <span class="button__label"><a href="https://play.google.com/store/apps/details?id=com.tentwenty.dubaidesignweek" target="_blank" class="">Download App</a>
                                    </span>
                                </button>
                                </div>
                                <?php
                                break;
                        } ?>
                    </div>
                </div>
            <?php } else { ?>
            
            <h4>Register</h4>
            <form method="post" action="<?php echo $this->url('/register', 'do_register') ?>" class="form-horizontal" id="register_form">
                <div class="">
                    <div class="element">
                        <?php echo $form->label('fullName', t('Full Name')); ?>

                        <div class="controls">
                            <?php echo $form->text('fullName'); ?>
                        </div>
                        <div class="input-line"></div>
                    </div>
                    <div class="element">
                        <?php echo $form->label('uEmail', t('Email')); ?>

                        <div class="controls">
                            <?php echo $form->text('uEmail'); ?>
                        </div>
                        <div class="input-line"></div>
                    </div>
                    <div class="element">
                        <?php echo $form->label('uPassword', t('Password')); ?>

                        <div class="controls">
                            <?php echo $form->password('uPassword'); ?>
                        </div>
                        <div class="input-line"></div>
                    </div>

                    <div class="element">
                        <div id='recaptcha' class="g-recaptcha"
                             data-sitekey="<?php echo GCAPTCHA_SITE_KEY;?>"
                             data-callback="onSubmit"
                             data-size="invisible"></div>
                        <div class="input-line"></div>
                    </div>
                    <?php if($referralToken){?>
                        <div class="element">
                            <?php echo $form->label('token', t('Referral Token')); ?>

                            <div class="controls">
                                <?php echo $form->text('token',$referralToken); ?>
                            </div>
                            <div class="input-line"></div>
                        </div>
                    <?php }?>
                    <div class="control-group remember-me">
                        <?php echo $form->checkbox('acceptTerms', 1); ?>
                        <label for="acceptTerms">I agree with <a target="_blank" href="<?php echo View::url('/terms-and-policies'); ?>">terms & policies</a></label>
                        <!-- <label class="checkbox "><?php echo $form->checkbox('acceptTerms', 1); ?> <span><?php echo t('I agree with terms'); ?></span></label> -->
                    </div>
                    <div class="">
                        <button type="submit" id="register" name="register" disabled class="ant-btn ant-btn-primary btn-register">Register</button>
                        <div class="ant-divider ant-divider-horizontal ant-divider-with-text"><span
                                    class="ant-divider-inner-text">or register using</span></div>
                    </div>
                    <div class="">
                        <div class="actions">
                            <a class="ant-btn  ant-btn-social ant-btn-social-fb" href="<?php echo $FBloginUrl; ?>">
                                <img class="go"
                                        src="<?php echo $this->getThemePath(); ?>/dist/images/facebook.svg"/>
                                Facebook</a>
                            <a class="ant-btn  ant-btn-social ant-btn-social-google icon-google google-sign-in">
                                <img class="go" src="<?php echo $this->getThemePath(); ?>/dist/images/google.svg"/>
                                Google</a>
                        </div>
                    </div>
            </form>
            <?php } ?>
        </div>
    </div>
</div>


<script>
    var form = document.getElementById('register_form');
    var element = document.getElementById('register');
    function onSubmit(token) {
        form.submit();
    }

    function validate(event) {
        event.preventDefault();
        grecaptcha.execute();
     }
    element.onclick = validate;
    </script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>


<!-- <main class="site-body register-page">
    <div class="container">
        <div class="page__header">
            <div class="page__header__container container bg-lavender">
                <div class="row">
                    <div class="col--8sm">
                        <h1>Register</h1>                                
                    </div>
                </div>
            </div>
        </div>
      <div class="process-step-block">
        <div class="process-step-box">

            <div class="ccm-form">

                <?php if ($success) { ?>

                    <div class="rows">
                        <div class="register-success">
                            <?php switch ($success) {
                                case "registered":
                                    ?>
                                    <p><strong>Confirmed!</strong><br/>Your email address { email address here } has been verified and now all you need is to <button class="button button__primary button__primary--yellow button__textdark button__downloadApp" target="_blank"><span class="button__label initial">Download the App</span><span class="button__label over"><a href="#" class="linkHoverX-style2">iOS</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" class="linkHoverX-style2">Android</a></span></button>, if you have not already done so.</p>
                                    <a href="<?php echo $this->url('/')?>" class="button button__primary button__primary--yellow button__textdark" target="_blank"><span class="button__label">Click here to continue back to the website</span></a>

                                    <?php
                                    break;
                                case "validate":
                                    ?>
                                    <p>You are now registered and just need to validate your email address in order to gain access to all the functionalities on this site. An email has been sent to you. Kindly click on the link provided in the email in order to verify your email address. Thank you.</p>
                                    
                                    <a href="<?php echo $this->url('/')?>" class="button button__primary button__primary--yellow button__textdark" target="_blank"><span class="button__label">Back to home</span></a>
                                    <?php
                                    break;
                                case "pending":
                                    ?>
                                    <p><?php echo $successMsg ?></p>
                                    <p><a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?></a></p>
                                    <div class="loginDownloadBtn">
                                    <button class="button button__primary button__primary--yellow button__textdark button__downloadApp desktop-app" target="_blank"><span class="button__label initial">Download App</span><span class="button__label over"><a href="https://itunes.apple.com/us/app/dxbdw/id1435925730?ls=1&mt=8" target="_blank" class="linkHoverX-style2">iOS</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://play.google.com/store/apps/details?id=com.tentwenty.dubaidesignweek" target="_blank" class="linkHoverX-style2">Android</a></span></button>
                        <button class="button button__primary button__primary--yellow button__textdark button__downloadApp ios-app" target="_blank">
                            <span class="button__label"><a href="https://itunes.apple.com/us/app/dxbdw/id1435925730?ls=1&mt=8" target="_blank" class="">Download App</a></span>
                        </button>
                        <button class="button button__primary button__primary--yellow button__textdark button__downloadApp android-app" target="_blank">
                            <span class="button__label"><a href="https://play.google.com/store/apps/details?id=com.tentwenty.dubaidesignweek" target="_blank" class="">Download App</a>
                            </span>
                        </button>
                                    </div>
                                    <?php
                                    break;
                            } ?>
                        </div>
                    </div>

                <?php } else { ?>

                    <form method="post" action="<?php echo $this->url('/register', 'do_register') ?>" class="form-horizontal" id="register_form">
                        <div class="rows register-form">
                            <h3>Enter your details</h3>

                            <div class="register-field">
                                <div class="col-sm-12">
                                    <div class="element">
                                        <?php echo $form->label('fullName', t('Full Name')); ?>

                                        <div class="controls">
                                            <?php echo $form->text('fullName'); ?>
                                        </div>
                                        <div class="input-line"></div>
                                    </div>
                                    <div class="element">
                                        <?php echo $form->label('uEmail', t('Email')); ?>

                                        <div class="controls">
                                            <?php echo $form->text('uEmail'); ?>
                                        </div>
                                        <div class="input-line"></div>
                                    </div>
                                    <div class="element">
                                        <?php echo $form->label('uPassword', t('Password')); ?>

                                        <div class="controls">
                                            <?php echo $form->password('uPassword'); ?>
                                        </div>
                                        <div class="input-line"></div>
                                    </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="span10 offset1 ">
                                <?php echo $form->hidden('uName', uniqid()); ?>
                                <?php echo $form->hidden('rcID', $rcID); ?>
                                <?php //echo $form->submit('register', t('Register') . '', array('class' => 'primary')) ?>
                                <button type="submit" id="register" name="register" class="button button__primary button__primary--yellow"><span class="button__label">Register</span></button>
                            </div>

                                <div class="row">
                                    <div class="span10 offset1">
                                        <div class="actions">
                                            <a class="ant-btn  ant-btn-social ant-btn-social-fb" href="<?php echo $FBloginUrl; ?>">
                                                <img class="go"
                                                     src="<?php echo $this->getThemePath(); ?>/dist/images/facebook.svg"/>
                                                Facebook</a>
                                            <a class="ant-btn  ant-btn-social ant-btn-social-google icon-google google-sign-in">
                                                <img class="go" src="<?php echo $this->getThemePath(); ?>/dist/images/google.svg"/>
                                                Google</a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</main> -->
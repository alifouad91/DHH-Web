<?php defined('C5_EXECUTE') or die('Access Denied.');
Loader::library('authentication/open_id');
$form = Loader::helper('form');

$FBloginUrl = Facebook::getURL();
?>
<script type="text/javascript">
    // $(function() {
    // 	$("input[name=uName]").focus();
    // });
</script>
<!-- @todo Password sent -->
     
<div class="page__wrapper">
    <div class="page__login__message alert-message block-message success"><p><?php echo $intro_msg; ?></p></div>
    <div class="page__login">
        <div class="general__card">
        <?php if (isset($passwordReset)) { ?>
            <h4>Your password reset<br/>link has been sent</h4>
            <p>A link to reset your password has been sent</p>
            <img src="<?php echo $this->getThemePath(); ?>/dist/images/form-password-success.svg" alt="<?php echo e(SITE); ?>"/>
            <a href="<?php echo View::url('/login'); ?>"><button class="ant-btn ant-btn-primary">BACK TO LOGIN</button></a>
        <?php } else { ?>
            <?php
            if($error) {
                echo $error->output();
            }
            ?>
            <?php if ($passwordChanged) { ?>
                <div class="block-message info alert-message">
                    <p><?php echo t('Password changed.  Please login to continue. '); ?></p></div>
            <?php } ?>

            <?php if ($changePasswordForm) { ?>
                <form method="post" action="<?php echo $this->url('/login', 'change_password', $uHash); ?>"
                      class="form-horizontal ccm-change-password-form">
                    <div class="row">
                        <div class="span10 offset1">
                            <fieldset>
                                <legend><?php echo t('Change Password'); ?></legend>
                                <p><?php echo t('Enter your new password below.'); ?></p>
                                <div class="control-group">
                                    <label for="uPassword"
                                           class="control-label"><?php echo t('New Password'); ?></label>
                                    <div class="controls">
                                        <input type="password" name="uPassword" id="uPassword" class="ccm-input-text"
                                               autofocus>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="uPasswordConfirm"
                                           class="control-label"><?php echo t('Confirm Password'); ?></label>
                                    <div class="controls">
                                        <input type="password" name="uPasswordConfirm" id="uPasswordConfirm"
                                               class="ccm-input-text">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span10 offset1">
                            <div class="actions">
                                <?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
                            </div>
                        </div>
                    </div>
                </form>

            <?php } elseif ($validated) { ?>
                <div class="row">
                    <div class="span10 offset1">
                        <fieldset>
                            <legend><?php echo t('Email Address Verified'); ?></legend>
                            <div class="success alert-message block-message">
                                <p><?php echo t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail); ?></p>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="span10 offset1">
                        <div class="actions">
                            <a class="btn primary"
                               href="<?php echo $this->url('/'); ?>"><?php echo t('Continue to Site'); ?></a>
                        </div>
                    </div>
                </div>

            <?php } elseif (isset($_SESSION['uOpenIDError']) && isset($_SESSION['uOpenIDRequested'])) {
                switch ($_SESSION['uOpenIDError']) {
                    case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE:
                        ?>
                        <form method="post" action="<?php echo $this->url('/login', 'complete_openid_email'); ?>"
                              class="form-horizontal ccm-openid-login-form">
                            <div class="row">
                                <div class="span10 offset1">
                                    <fieldset>
                                        <legend><?php echo t('Specify your OpenID email address'); ?></legend>
                                        <p><?php echo t('To complete the signup process, you must provide a valid email address.'); ?></p>
                                        <div class="control-group">
                                            <label for="uEmail"
                                                   class="control-label"><?php echo t('Email Address'); ?></label>
                                            <div class="controls">
                                                <?php echo $form->email('uEmail', array('autofocus' => 'autofocus')); ?>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="span10 offset1">
                                    <div class="actions">
                                        <?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php
                        break;
                        case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
                        $ui = UserInfo::getByID($_SESSION['uOpenIDExistingUser']);
                        ?>
                        <form method="post" action="<?php echo $this->url('/login', 'do_login'); ?>"
                              class="form-horizontal ccm-openid-merge-form">
                            <div class="row">
                                <div class="span10 offset1">
                                    <fieldset>
                                        <legend><?php echo t('Merge local account with OpenID'); ?></legend>
                                        <p><?php echo t(/*i18n: %s is an email address */
                                                'The OpenID account returned an email address already registered on this site (%s). To join this OpenID to the existing user account, login below:', '<strong>' . $ui->getUserEmail() . '</strong>'); ?></p>
                                        <div class="control-group">
                                            <label for="uName" class="control-label"><?php
                                                if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
                                                    echo t('Email Address');
                                                } else {
                                                    echo t('Username');
                                                }
                                                ?></label>
                                            <div class="controls">
                                                <input type="text" name="uName"
                                                       id="uName" <?php echo isset($uName) ? ('value="' . h($uName) . '"') : ''; ?>
                                                       class="ccm-input-text" autofocus>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label for="uPassword"
                                                   class="control-label"><?php echo t('Password'); ?></label>
                                            <div class="controls">
                                                <input type="password" name="uPassword" id="uPassword"
                                                       class="ccm-input-text">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="span10 offset1">
                                    <div class="actions">
                                        <?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php
                        break;
                }

            } elseif ($invalidRegistrationFields == true) { ?>
                <form method="post" action="<?php echo $this->url('/login', 'do_login'); ?>"
                      class="form-horizontal ccm-missing-login-fields-form">
                    <div class="row">
                        <div class="span10 offset1">
                            <fieldset>
                                <legend><?php echo t('Fill in missing fields'); ?></legend>
                                <p><?php echo t('You must provide the following information before you may login.'); ?></p>
                                <?php
                                $attribs = UserAttributeKey::getRegistrationList();
                                $af      = Loader::helper('form/attribute');
                                $i       = 0;
                                foreach ($unfilledAttributes as $ak) {
                                    if ($i > 0) {
                                    }
                                    echo $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());
                                    $i++;
                                }
                                echo $form->hidden('uName', Loader::helper('text')->entities($_POST['uName']));
                                echo $form->hidden('uPassword', Loader::helper('text')->entities($_POST['uPassword']));
                                echo $form->hidden('uOpenID', $uOpenID);
                                echo $form->hidden('completePartialProfile', true);
                                ?>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span10 offset1">
                            <div class="actions">
                                <?php echo $form->submit('submit', t('Sign In'), array('class' => 'primary')); ?>
                                <?php echo $form->hidden('rcID', $rcID); ?>
                            </div>
                        </div>
                    </div>
                </form>
                
            <?php } elseif ($forgot_password) { ?>
                <h4>Reset Password</h4>
                <p>Enter the email address linked to your account below, and we will send you instructions on how to reset your password</p>
                <p class="second">For security reasons, we do NOT store your password. So rest assured that we will never send your password via email.</p>
                <form method="post" action="<?php echo $this->url('/login', 'forgot_password'); ?>" id="forgot_pass"
                      class="form-horizontal ccm-forgot-password-form">
                    <div class="">
                        <div class="span10 offset1">
                            <fieldset>
                                <input type="hidden" name="rcID" value="<?php echo $rcID; ?>"/>
                                <div class="control-group">
                                    <label for="uEmail" class="control-label"><?php echo t('Email Address'); ?></label>
                                    <div class="controls">
                                        <input type="text" name="uEmail" value="" class="ccm-input-text">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="element">
                        <div id='recaptcha' class="g-recaptcha"
                             data-sitekey="<?php echo GCAPTCHA_SITE_KEY;?>"
                             data-callback="onSubmit"
                             data-size="invisible"></div>
                        <div class="input-line"></div>
                    </div>
                    <div class="">
                        <div class="span10 offset1">
                            <div class="actions">
                                <?php echo $form->submit('forgot_pass_btn', 'SEND RESET LINK', array(), 'ant-btn ant-btn-primary btn-reset-password'); ?>
                            </div>
                        </div>
                    </div>
                </form>


                <script>
                    var form = document.getElementById('forgot_pass');
                    var element = document.getElementById('forgot_pass_btn');
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


            <?php }
            else { ?>
            <?php Loader::element('login_form'); ?>
        </div>
    </div>
    <?php } ?>
    <? } ?>

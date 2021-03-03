<?php defined('C5_EXECUTE') or die('Access Denied.');
Loader::library('authentication/open_id');
$form = Loader::helper('form');
$page = Page::getByPath('/login');
$cPage = Page::getCurrentPage();
$FBloginUrl   = Facebook::getURL();
$redirectPath = $_SERVER['PATH_INFO'];
?>

<h4>Login</h4>
<div class="container-fluid">
    <form method="post" action="<?php echo $this->url('/login', 'do_login'); ?>"
          class="form-horizontal ccm-login-form">
        <?php if ($page->getCollectionID() != $cPage->getCollectionID()) { ?>
            <input type="hidden" name="redirectPath" value="<?php echo $redirectPath; ?>" id="redirectPath"/>
        <?php } ?>
        <div class="row">
            <div class="span5 offset1">
                <fieldset>
                    <div class="control-group">
                        <label for="uName" class="control-label"><?php
                            echo t('Email Address');
                            ?></label>
                        <div class="controls">
                            <input type="text" name="uName"
                                   id="uName" <?php echo isset($uName) ? ('value="' . $uName . '"') : ''; ?>
                                   class="ccm-input-text" autofocus>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="uPassword" class="control-label"><?php echo t('Password'); ?></label>
                        <div class="controls">
                            <input type="password" name="uPassword" id="uPassword" class="ccm-input-text"/>
                        </div>
                    </div>
                </fieldset>
                <?php if (OpenIDAuth::isEnabled()) { ?>
                    <fieldset>
                        <legend><?php echo t('OpenID'); ?></legend>
                        <div class="control-group">
                            <label for="uOpenID" class="control-label"><?php echo t('Login with OpenID'); ?>
                                :</label>
                            <div class="controls">
                                <input type="text" name="uOpenID"
                                       id="uOpenID" <?php echo isset($uOpenID) ? ('value="' . $uOpenID . '"') : ''; ?>
                                       class="ccm-input-openid">
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>
            </div>
            <div class="span4 offset1">
                <fieldset>
                    <?php if (isset($locales) && is_array($locales) && count($locales) > 0) { ?>
                        <div class="control-group">
                            <label for="USER_LOCALE"
                                   class="control-label"><?php echo t('Language'); ?></label>
                            <div class="controls"><?php echo $form->select('USER_LOCALE', $locales); ?></div>
                        </div>
                    <?php } ?>
                    <div class="control-group remember-me">
                        <?php echo $form->checkbox('uMaintainLogin', 1); ?>
                        <label for="uMaintainLogin">Remember me</label>
                        <!-- <label class="checkbox "><?php echo $form->checkbox('uMaintainLogin', 1); ?> <span><?php echo t('Remember me'); ?></span></label> -->
                    </div>
                    <a href="<?php echo View::url('/login/forgot_password'); ?>" class="forgot-password">Forgot
                        password?</a>
                    <?php $rcID = isset($_REQUEST['rcID']) ? Loader::helper('text')->entities($_REQUEST['rcID']) : $rcID; ?>
                    <input type="hidden" name="rcID" value="<?php echo $rcID; ?>"/>
                    
                </fieldset>
                
            </div>
        </div>
        <div class="row">
            <a href="<?php echo View::url('/register'); ?>" class="w-100 ant-btn ant-btn-secondary btn-register">REGISTER</a>
            <?php echo $form->submit('submit', t('LOGIN'), array('class' => 'ant-btn ant-btn-primary btn-login')); ?>
            <div class="ant-divider ant-divider-horizontal ant-divider-with-text"><span
                        class="ant-divider-inner-text">or</span></div>
        </div>
        <div class="row">
            <div class="span10 offset1">
                <div class="actions">
                    <a class="ant-btn  ant-btn-social ant-btn-social-fb" href="<?php echo $FBloginUrl; ?>">
                        <img class="go"
                             src="<?php echo $this->getThemePath(); ?>/dist/images/facebook.svg" alt="facebook-login-logo"/> 
                        Facebook</a>
                    <a class="ant-btn  ant-btn-social ant-btn-social-google icon-google google-sign-in">
                        <img class="go" src="<?php echo $this->getThemePath(); ?>/dist/images/google.svg" alt="google-login-logo"/>
                        Google</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('.ant-btn-social').on('click',function () {
            if ($('#redirectPath').length)
            var date = new Date();
            date.setTime(date.getTime()+(5*60*1000));
            var expires = "; expires="+date.toTimeString();
            document.cookie = "redirectPath" + "="+ $('#redirectPath').val() + expires+"; path=/";
        })
    })
</script>
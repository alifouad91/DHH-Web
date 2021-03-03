<?php
/** @var View $this */
defined('C5_EXECUTE') or die(_("Access Denied."));
/** @var HtmlHelper $htmlHelper */
$htmlHelper = Loader::helper('html');
?>

<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
<!-- <script src="<?php //echo $this->getThemePath() . '/dist/js/vendors.min.js'; ?>"></script> -->
<script src="<?php echo $this->getThemePath() . '/dist/js/app.min.js?v='.FILE_VERSION; ?>"></script>


<script>
    function init()
    {
        gapi.load('auth2', function () {
            googleAuth2 = gapi.auth2.init({
                client_id: '<?php echo GOOGLE_OAUTH_CLIENT_ID; ?>'
            });
        });
    }
</script>
<script>
    $('.ant-btn-social-google').click(function () {
        googleAuth2.grantOfflineAccess({'redirect_uri': 'postmessage'}).then(function (authResult) {
            window.location = CCM_DISPATCHER_FILENAME + '/login/google?code=' + encodeURIComponent(authResult.code);
        });
    });
</script>

<!-- Uncomment below if you need to add google captcha (also in custom.js)
<script src="https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit" async defer></script>
-->
<!-- script>

    setInterval(function(){
        getNotifications();
    }, 120000);

    function getNotifications() {
        $.ajax({
            url : CCM_BASE_TOOLS_PATH + '/notifications.php',
            type : 'GET',
            success : function(res) {
                console.log(res)
            }
        });
    }
</script-->
<?php
defined('C5_EXECUTE') or die('Access Denied.');
$theme = PageTheme::getSiteTheme();
?>
<table width="640" border="0" cellspacing="0" style="border: 0 none;">
    <tr>
      <td height="210" style="
        position: relative;
        background-image: url(<?php echo BASE_URL . $theme->getThemeURL() . '/src/images/banner_general.png'; ?>);
        background-position: center;
        background-repeat: no-repeat;
        " >
        <h1 style="
          height: 36px;
          width: 592px;
          color: #191919;
          font-family: Effra, Roboto;
          font-size: 40px;
          font-weight: 900;
          line-height: 36px;
          text-align: center;
          margin: 0;
          width: 100%;
          margin-bottom: 15px;
        ">Forgot your password?</h1>
        <p style="
          color: #B6B6C0;
          font-family: Roboto;
          font-size: 20px;
          line-height: 24px;
          text-align: center;
          width: 100%;
          margin: 0;
        ">It looks like you requested for a New password.</p>
      </td>
    </tr>
    <!-- Spacing -->
    <tr>
      <td height="67"></td>
    </tr>
    <!-- End -->
    <tr>
      <td height="67">
        <p style="
          color: #000000;
          font-family: Roboto;
          font-size: 16px;
          line-height: 28px;
          text-align: center;
          margin: 0 0 15px;
        ">If this sounds right, you can reset the password by clicking on the button below:</p>
        <button style="
          height: 48px;
          width: 189px;
          border-radius: 2px;
          background-color: #FE6768;
          border: none;
          display: block;
          margin: 0 auto 9px;
        ">
          <a href="<?php echo $changePassURL?>" style="
            color: #FFFFFF;
            font-family: Roboto;
            font-size: 16px;
            font-weight: 500;
            line-height: 19px;
            text-decoration: none;
          ">Reset Password</a>
        </button>
        <p style="
          color: #B6B6C0;
          font-family: Roboto;
          font-size: 14px;
          line-height: 28px;
          display: block;
          margin: auto;
          text-align: center;
        "
        >If you didn’t request the reset the password, please contact us ASAP. Otherwise, just ignore it if you wish to keep your old password!
        </p>
      </td>
    </tr>
</table>

<?php
defined('C5_EXECUTE') or die('Access Denied.');
$theme = PageTheme::getSiteTheme();
?>
<table width="640" border="0" cellspacing="0" style="border: 0 none;">
    <tr>
      <td height="210" style="position: relative;" >
        <img style="
          display:block;
          height: 210px;
          margin: auto;
          "
          src="<?php echo BASE_URL . $theme->getThemeURL() . '/src/images/banner_general.png'; ?>" alt="<?php echo e(SITE); ?>"/>
        <h1 style="
          height: 36px;
          width: 592px;
          color: #191919;
          font-family: Effra;
          font-size: 40px;
          font-weight: 900;
          line-height: 36px;
          text-align: center;
          position: absolute;
          top: 52px;
          margin: 0;
          width: 100%;
        ">Your account has<br />been deleted</h1>
        <p style="
          color: #B6B6C0;
          font-family: Roboto;
          font-size: 20px;
          line-height: 24px;
          text-align: center;
          position: absolute;
          top: 141px;
          width: 100%;
          margin: 0;
        ">It’s really sad, hope you will change your mind</p>
      </td>
    </tr>
    <!-- Spacing -->
    <tr>
      <td height="67"></td>
    </tr>
    <!-- End -->
    <tr>
      <td height="67">
        <button style="
          height: 48px;
          width: 258px;
          border-radius: 2px;
          background-color: #FE6768;
          border: none;
          display: block;
          margin: 0 auto 37px;
        ">
          <a style="
            color: #FFFFFF;
            font-family: Roboto;
            font-size: 16px;
            font-weight: 500;
            line-height: 19px;
          ">I changed my mind, recover it</a>
        </button>
      </td>
    </tr>
</table>

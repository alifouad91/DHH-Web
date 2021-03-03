<?php
defined('C5_EXECUTE') or die('Access Denied.');
$theme = PageTheme::getSiteTheme();
$activationURL = BASE_URL . View::url('/login', 'v', $uHash);
$username      = explode(' ',$fullname)[0];
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
          height: auto;
          width: 592px;
          color: #191919;
          font-family: Effra;
          font-size: 40px;
          font-weight: 900;
          line-height: 36px;
          text-align: center;
          margin: 0;
          width: 100%;
          margin-bottom: 15px;
        "><?php echo $username;?>!,<br />Welcome to DHH!</h1>
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
        ">Please confirm your email so you can start making reservations!<br />If you experience troubles and/or link is not working, please contact us at <a href="mailto:hello@dhh.ae" style="
          color: #E75056;
          text-decoration: none;
        ">hello@dhh.ae</a>
            and we will be happy to assist!</p>
       
          <a style="
            color: #FFFFFF;
            font-family: Roboto;
            font-size: 16px;
            font-weight: 500;
            line-height: 19px;
            text-decoration: none;
            height: auto;
            width: 149px;
            border-radius: 2px;
            background-color: #FE6768;
            border: none;
            display: block;
            margin: 0 auto 9px;
            text-align: center;
            padding: 15px 0;
          " href="<?php echo $activationURL; ?>">Confirm email</a>
      </td>
    </tr>
</table>

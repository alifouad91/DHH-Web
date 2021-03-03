<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 18/3/19
 * Time: 1:12 PM
 */

/** @var Booking $booking */
$property = $booking->getProperty();
/** @var DateHelper $dh */
$dh = Loader::helper('date');
$startDate = $booking->getBookingStartDate();
$endDate = $booking->getbookingEndDate();

$m1 = $dh->getFormattedDate($startDate,'M');
$y1 = $dh->getFormattedDate($startDate,'Y');
$m2 = $dh->getFormattedDate($endDate,'M');
$y2 = $dh->getFormattedDate($endDate,'Y');

$printableDate = $m1 .' '.$dh->getFormattedDate($startDate,'d');
if ($y1 != $y2)
    $printableDate .= ' '.$y1;
$printableDate .= ' - '.$dh->getFormattedDate($endDate,'d');

if ($m1 != $m2)
    $printableDate .= ' '.$m2;
$printableDate.=','.$y2;

?>

<?php
defined('C5_EXECUTE') or die('Access Denied.');
$theme = PageTheme::getSiteTheme();
?>
<table width="640" border="0" cellspacing="0" style="border: 0 none; padding: 0 20px;">
    <tr>
      <td height="" style="position: relative;" >
        <h1 style="

          color: #191919;
          font-family: Effra;
          font-size: 40px;
          font-weight: 900;
          letter-spacing: 0.1px;
          line-height: 36px;
          margin: 0 0 9px;
        ">Rate your stay</h1>
        <p style="
          color: #B6B6C0;
          font-family: Roboto;
          font-size: 20px;
          line-height: 24px;
          margin: 0 0 22px;
        ">Thank you for staying with DHH! It was a pleasure to host you!</p>

        <p style="
          color: #B6B6C0;
          font-family: Roboto;
          font-size: 14px;
          line-height: 28px;
          display: block;
          margin: auto;
          text-align: left;
        "
        >Your feedback is very important to us, so we would be grateful if you share your thoughts with us and the rest of the world! </p>
        <div style="
          height: auto;
          width: 544px;
          border: 1px solid #F0F0F0;
          border-radius: 3px;
          background-color: #FFFFFF;
          box-shadow: 0 3px 5px 0 rgba(0,0,0,0.04), 0 2px 10px 0 rgba(0,0,0,0.08);
          position: relative;
          padding: 24.5px 24.5px 21.5px;
        ">
          <h6 style="
            color: #000000;
            font-family: Roboto;
            font-size: 22px;
            font-weight: bold;
            line-height: 25px;
            margin: 0 0 4px;
          "><?php echo $property->getName();?></h6>
          <p style="
            color: #8C9399;
            font-family: Roboto;
            font-size: 14px;
            line-height: 16px;
            margin: 0 0 12px;
          "><?php echo $property->getLocation();?></p>
          <div style="
            display: flex;
            justify-content: flex-start;
            align-items: center;
          ">
            <img style="
              height: 12px;
              width: auto;
            "
            src="<?php echo BASE_URL . $theme->getThemeURL() . '/src/images/user.png'; ?>" alt="<?php echo e(SITE); ?>"/>
            <span style="
              color: #000000;
              font-family: Roboto;
              font-size: 14px;
              line-height: 16px;
              margin-left: 8px;
            "><?php echo $booking->getNoOfGuest();?> guest(s)</span>
          </div>
          <div style="
            border-radius: 16px;
            background-color: #F3F4F5;
            width: 150px;
            margin: 10px 0;
          ">
            <span style="
              padding: 7px 16px;
              display: block;
              font-family: Roboto;
              font-size: 15px;
              line-height: 18px;
              text-align: center;
            "><?php echo $printableDate; ?></span>
          </div>
          <div style="
          ">
            <span style="
              color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
            "><?php echo $booking->getNoOfDays();?> night(s)</span>
          </div>
        </div>
      </td>
    </tr>
    <!-- Spacing -->
    <tr>
      <td height="25"></td>
    </tr>
    <!-- End -->
    <tr>
      <td height="67">
        <button style="
          height: 48px;
          width: 137px;
          border-radius: 2px;
          background-color: #FE6768;
          border: none;
        ">
          <a href="<?php echo BASE_URL . View::url('profile','my-bookings');?>" style="
            color: #FFFFFF;
            font-family: Roboto;
            font-size: 16px;
            font-weight: 500;
            line-height: 19px;
            text-decoration: none;
          ">Write review</a>
        </button>
      </td>
    </tr>
</table>

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
        ">Request has been sent</h1>
      </td>
    </tr>
    <!-- Spacing -->
    <!-- End -->
    <tr>
      <td height="67">
        <p style="
          color: #000000;
          font-family: Roboto;
          font-size: 16px;
          line-height: 28px;
          margin: 0 0 42px;
        ">We recieved your support request <a href="" style="
          color: #E75056;
          text-decoration: none;
        ">#38912343</a>, please give our support team some time and we will back to you with the answer. <br/><br/>Let’s keep in touch!</p>
      </td>
    </tr>
</table>


<!-- <p></p>

<p><?php echo (int) $property->getAverageRating();?></p>
<p><?php echo $property->getTotalRatings();?></p>
<p><?php echo $printableDate;?></p>
<p><?php echo $booking->getNoOfDays();?></p>
<p><?php echo $property->getPerDayPrice();?></p>


<p> My Bookings <?php echo View::url('profile','my-bookings');?></p> -->

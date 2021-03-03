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
        <p style="
          
          color: #191919;
          font-family: Effra;
          font-size: 24px;
          font-weight: 900;
          letter-spacing: 0.1px;
          line-height: 32px;
          margin: 0 0 9px;
        ">We have received your request regarding the rental for events!</p>
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
        ">One of DHH members will be in touch with you in the next 24 hours!</p>
      </td>
    </tr>

    <tr>
      <td height="67">
        <p style="
          color: #000000;
          font-family: Roboto;
          font-size: 16px;
          line-height: 28px;
          margin: 20px 20px 42px;
          text-align: center;
        ">For any last minute enquiries, please call us on <br><a href="tel:00971528654208" style="color: #E75056; text-decoration: none;">00971 52 865 42 08</a> or <a href="tel:00971509643016" style="color: #E75056; text-decoration: none;">00971 50 964 30 16</a>.
        </p>
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

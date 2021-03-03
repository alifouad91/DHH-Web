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
$ph = Loader::helper('price');
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

$pricePerDay = $booking->getPriceBreakdown();
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
        ">Your reservation with Driven Holiday Homes has been cancelled.</p>
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
        ">We will be happy to host you next time!
</p>
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
                       src="<?php echo BASE_URL . $theme->getThemeURL() . '/src/images/rate-'. (int) $property->getAverageRating() . '.png'; ?>" alt="<?php echo e(SITE); ?>"/>
                  <span style="
              color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 17px;
              margin-left: 3px;
              text-align: right;
            ">• <?php echo $property->getTotalRatings();?></span>
                  <img style="
              height: 12px;
              width: auto;
              margin-left: 20px;
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
            text-align: center;
            margin: 10px 0;
          ">
            <span style="
              padding: 7px 16px;
              display: block;
              font-family: Roboto;
              font-size: 15px;
              line-height: 18px;
            "><?php echo $printableDate; ?></span>
              </div>
              <div>
                <span style="
              color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: inline-block;
            ">Booking No:</span>
                  <span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $booking->getBookingNo();?></span>
              </div>
              <div>
            <span style="
              color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: inline-block;
            ">Number of days:</span>
                  <span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $booking->getNoOfDays();?></span>
              </div>
              <div style="margin: 15px 0;">
            <span style="
               color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: block;
              margin-bottom: 5px;
            ">Price Per Day: </span>
                  <?php foreach ($pricePerDay as $priceItem) { ?>
                      <span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo 'Date:  ' . $priceItem->day .' <span style="margin-left:15px;"></span>'. ' Price: ' . $priceItem->price;?> </span><br>
                  <?php } ?>
              </div>
              <?php
              $propertyFacilities = $booking->getBookingPropertyFacilities(true);
              if (count($propertyFacilities)) {
                  foreach ($propertyFacilities as $facilities) {
                      ?>
                      <div style="margin: 15px 0;">
            <span style="
               color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: block;
              margin-bottom: 5px;
            ">Extra Facilities: </span>
                          <span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $facilities['value'] . ' <span style="margin-left:15px;"></span>' . ' Price: '
                                  . $facilities['price']; ?> </span><br>
                      </div>
                      <?php
                  }
              }
              ?>

              <?php
              if(!empty($booking->getCreditAmount()))
              {
                  ?>
                  <div>
                    <span style="
                      color: #8C9399;
                      font-family: Roboto;
                      font-size: 14px;
                      font-weight: 500;
                      line-height: 16px;
                      width: 120px;
                      display: inline-block;
                    ">Referral Credit: </span><span style="
                      color: #333333;
                      font-family: Roboto;
                      font-size: 14px;
                      font-weight: bold;
                      letter-spacing: -0.4px;
                      line-height: 16px;
                    ">- <?php echo $ph->format($booking->getCreditAmount());?> </span>
                  </div>
                  <?php
              }
              ?>

              <?php
              if(!empty($booking->getDiscountReceived()))
              {
                  ?>
                  <div>
                    <span style="
                      color: #8C9399;
                      font-family: Roboto;
                      font-size: 14px;
                      font-weight: 500;
                      line-height: 16px;
                      width: 120px;
                      display: inline-block;
                    ">Savings: </span><span style="
                      color: #333333;
                      font-family: Roboto;
                      font-size: 14px;
                      font-weight: bold;
                      letter-spacing: -0.4px;
                      line-height: 16px;
                    ">- <?php echo $ph->format($booking->getDiscountReceived());?> </span>
                  </div>
                  <?php
              }
              ?>

              <div>
            <span style="
               color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: inline-block;
            ">VAT: </span><span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $ph->format($booking->getVat());?> </span>
              </div>




              <div>
<span style="
               color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: inline-block;
            ">Tourism Fee: </span><span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $ph->format($booking->getDhiramFee()).'('. $booking->getNoOfDays().' Nights)' ;?> </span>
              </div>
              <div>
            <span style="
              color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: inline-block;
            ">Total:</span>
                  <span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $ph->format($booking->getTotal());?></span>
              </div>
              <?php
              if(!empty($booking->getAdditionalRequests()))
              {
                  ?>
                  <div>
            <span style="
              color: #8C9399;
              font-family: Roboto;
              font-size: 14px;
              font-weight: 500;
              line-height: 16px;
              width: 120px;
              display: inline-block;
            ">Additional Requests:</span>
                      <span style="
              color: #333333;
              font-family: Roboto;
              font-size: 14px;
              font-weight: bold;
              letter-spacing: -0.4px;
              line-height: 16px;
            "><?php echo $booking->getAdditionalRequests(); ?></span>
                  </div>
                  <?php
              }
              ?>
          </div>
          </div>
      </td>
    </tr>

    <tr>
      <td height="67">
        <p style="
          color: #000000;
          font-family: Roboto;
          font-size: 16px;
          line-height: 28px;
          margin: 0 0 42px;
        ">If you didn’t cancel the booking, please contact us ASAP.
        </p>
      </td>
    </tr>

    
</table>


<!-- <p></p>

<p><?php //echo (int) $property->getAverageRating();?></p>
<p><?php //echo $property->getTotalRatings();?></p>
<p><?php //echo $printableDate;?></p>
<p><?php //echo $booking->getNoOfDays();?></p>
<p><?php //echo $property->getPerDayPrice();?></p>


<p> My Bookings <?php //echo View::url('profile','my-bookings');?></p> -->

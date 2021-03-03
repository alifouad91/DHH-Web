<?php
defined('C5_EXECUTE') or die('Access Denied.');
$theme = PageTheme::getSiteTheme();
$ph = Loader::helper('price');
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
        ">You would like these apartments</h1>
        <p style="
          color: #B6B6C0;
          font-family: Roboto;
          font-size: 20px;
          line-height: 24px;
          margin: 0 0 22px;
        ">Around Dubai Marina Walk</p>
      </td>
    </tr>
    <tr>
      <td>
        <div>
        <?php foreach ($properties as $property) { ?>
          <div style="
            float: left;
            padding: 0 10px 30px;
            width: calc(50% - 20px);
            position: relative;
          ">
              <img src="<?php echo $property->getThumbnailPath(); ?>"
              style="
                height: 197px;
                width: 100%;
                border-radius: 3px;
                object-fit: cover;
              "/>
              <div>
                <h6 style="
                  color: #000000;
                  font-family: Effra;
                  font-size: 16px;
                  font-weight: 500;
                  line-height: 19px;
                  margin: 9px 0 0;
                "><?php echo $property->getName();?></h6>
                <p style="
                  opacity: 0.6;
                  color: #000000;
                  font-family: Roboto;
                  font-size: 13px;
                  letter-spacing: 0.3px;
                  line-height: 15px;
                  margin: 0 0 9px;
                "><?php echo $property->getLocation();?></p>
                <p style="
                  color: #666666;
                  font-family: Roboto;
                  font-size: 13px;
                  line-height: 15px;
                  margin: 0;
                ">From <b style="color: #000;"><?php echo $ph->format($property->getPerDayPrice()); ?></b> per night</p>
                <div style="
                  position: absolute;
                  right: 10px;
                  display: flex;
                  align-items: center;
                  bottom: 31px;
                ">
                  <img style="
                    height: 10px;
                    width: auto;
                  "
                  src="<?php echo BASE_URL . $theme->getThemeURL() . '/src/images/rate-'. (int) $property->getAverageRating() . '.png'; ?>" alt="<?php echo e(SITE); ?>"/>
                  <span style="
                    color: #000000;
                    font-family: Roboto;
                    font-size: 10px;
                    font-weight: bold;
                    line-height: 12px;
                    text-align: right;
                    margin-left 2px;
                  ">• <?php echo $property->getTotalRatings();?></span>
                </div>
              </div>
          </div>
        <? } ?>
        </div>
      </td>
    </tr>
    <tr>
      <td>
         <button style="
            height: 48px;
            width: 160px;
            border-radius: 2px;
            background-color: #FE6768;
            border: none;
          ">
            <a href="<?php echo BASE_URL . View::url('properties');?>" style="
              color: #FFFFFF;
              font-family: Roboto;
              font-size: 16px;
              font-weight: 500;
              line-height: 19px;
              text-decoration: none;
            ">Explore options</a>
          </button>
      </td>
    </tr>
</table>


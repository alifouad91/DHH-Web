<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Loader::helper('navigation');
?>

<div class="banner__rental " data-aos="fade-in" data-aos-delay="300">
  <div class="banner__rental__bg" style="background-image: url(<?php echo $field_8_image->src;?>)"></div>
  <?php  if (!empty($field_1_textbox_text)): ?>
    <h2 >
      <?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
    </h2>
  <?php  endif; ?>
</div>

<?php 
$nh = Loader::helper('navigation');
$link_url = $nh->getLinkToCollection(Page::getByID($field_6_link_cID), true);
?>

<a href="<?php echo $link_url; ?>" class="banner__general banner__general__primary" data-aos="fade-in" data-aos-delay="300" >
  <?php  if (!empty($field_2_textbox_text)): ?>
    <span><?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?></span>
  <?php  endif; ?>
  <?php  if (!empty($field_1_textbox_text)): ?>
    <h2 >
      <?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
      <?php  if (!empty($field_8_image)): ?>
        <img class="emoji" src="<?php  echo $field_8_image->src; ?>"  alt="<?php  echo $field_8_image_altText; ?>" />
      <?php  endif; ?>
    </h2>
  <?php  endif; ?>
  <img class="go" src="<?php echo $this->getThemePath(); ?>/dist/images/banner_arrow.svg" alt="Banner Arrow Image"/>
</a>
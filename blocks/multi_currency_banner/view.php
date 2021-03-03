<?php  defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="col-sm-12 col-md-6" data-aos="fade-in">
	<div class="banner__promo">
		<?php  if (!empty($field_2_textbox_text)): ?>
			<a href="<?php  echo htmlentities($field_11_textbox_text, ENT_QUOTES, APP_CHARSET); ?>"></a>
		<?php endif; ?>
		<div class="banner__promo__bg" style="background-image: url(<?php echo $field_10_image->src;?>)"></div>
		<div class="banner__promo__remarks">
			<?php  if (!empty($field_3_textbox_text)): ?>
				<p><?php  echo htmlentities($field_3_textbox_text, ENT_QUOTES, APP_CHARSET); ?> properties</p>
			<?php  endif; ?>
				<p>From <b>
				<?php
					switch (App::getSessionLocale()) {
						case 'ar_AE':
							echo htmlentities($field_4_textbox_text, ENT_QUOTES, APP_CHARSET);
							break;
						case 'en_US':
							echo htmlentities($field_5_textbox_text, ENT_QUOTES, APP_CHARSET);
							break;
						case 'de_DE':
							echo htmlentities($field_6_textbox_text, ENT_QUOTES, APP_CHARSET);
							break;	
						case 'ar_SA':
							echo htmlentities($field_7_textbox_text, ENT_QUOTES, APP_CHARSET);
							break;
						case 'ru_RU':
							echo htmlentities($field_8_textbox_text, ENT_QUOTES, APP_CHARSET);
							break;
						case 'ar_KW':
							echo htmlentities($field_9_textbox_text, ENT_QUOTES, APP_CHARSET);
							break;
						default:
							# code...
							break;
					}
				?>
				
				</b> per night</p>
		</div>
		<div class="banner__promo__title">
			<?php  if (!empty($field_1_textbox_text)): ?>
				<span class="large-text">
					<?php  echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>
				</span>
			<?php  endif; ?>
			<?php  if (!empty($field_2_textbox_text)): ?>
				<p><?php  echo htmlentities($field_2_textbox_text, ENT_QUOTES, APP_CHARSET); ?></p>
			<?php  endif; ?>
		</div>
	</div>
</div>
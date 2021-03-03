<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<div class="page-404">
<h1 class="error"><?php echo t('404 Page Not Found')?></h1>


<?php if (is_object($c)) { ?>
	<br/><br/>
	<?php $a = new Area('Main'); $a->display($c); ?>
<?php } ?>


<a href="<?php echo DIR_REL?>/"><?php echo t('Back to Home')?></a>.
</div>
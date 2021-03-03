<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php 
$count = (integer) $field_4_select_value;
$featured = $count < 4;
$grid = $featured ? 4 : 3;
?>
<div class="block property__home property__<?php echo $featured ? 'featured': 'picks'; ?>" 
data-count="<?php echo $count; ?>"
data-filter="<?php echo htmlentities($field_3_select_value, ENT_QUOTES, APP_CHARSET); ?>"
data-title="<?php echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?>">
    <div class="container-fluid">
        <h4><?php echo htmlentities($field_1_textbox_text, ENT_QUOTES, APP_CHARSET); ?></h4>
        <div class="row">
            <div class="col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                <?php Loader::element('property_skeleton'); ?>
            </div>
            <div class="col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                <?php Loader::element('property_skeleton'); ?>
            </div>
            <div class="col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                <?php Loader::element('property_skeleton'); ?>
            </div>
            <?php if($featured) { ?>
                <div class="hidden-xs hidden-sm hidden-md hidden-lg hidden-xlg visible-xlg col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                    <?php Loader::element('property_skeleton'); ?>
                </div>
            <?php } ?>
            <?php if(!$featured) { ?>
            <div class="col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                <?php Loader::element('property_skeleton'); ?>
            </div>
            <div class="hidden-xs hidden-sm hidden-md hidden-lg hidden-xlg visible-fhd col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                <?php Loader::element('property_skeleton'); ?>
            </div>
            <div class="hidden-xs hidden-sm hidden-md hidden-lg hidden-xlg visible-fhd col-sm-12 col-md-<?php echo $grid;?> col-lg-<?php echo $grid;?> col-xlg-<?php echo $grid;?> col-fhd-<?php echo $grid - 1;?> property__card">
                <?php Loader::element('property_skeleton'); ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
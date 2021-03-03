<?php
/** @var Property $property */
/** @var $task */
$user = new User();
?>
<?php if ($task == 'listing') { 
    $loc = $_SERVER['REQUEST_URI'];
    $isMonthly = strpos($loc, '?monthly');
    $featuredId = $c->getAttribute('featured_filter_id');
    $featuredText = $c->getAttribute('featured_filter_text');
  ?>
    <section class="page__limit page__wrapper properties">
      <div id="property-results" class="property__results" 
      data-id="<?php echo $user->getUserID();?>"  data-monthly="<?php echo $isMonthly;?>" 
      data-featuredid="<?php echo $featuredId; ?>"
      data-featuredtext="<?php echo $featuredText; ?>"
      >
      </div>
    </section>
<?php } ?>
<?php if ($task == 'detail') {
    $_REQUEST['propertyName'] = $property->getName();
    $_REQUEST['propertyPath'] = BASE_URL.$_SERVER['REQUEST_URI'];
    
    ?>
  <section class="page__wrapper">
    <div class="page__propertyitem" data-uID="<?php echo $user->getUserID();?>" data-pID="<?php echo $property->getID(); ?>"></div>
    <div class="page__propertyitem__rules">
<!--     --><?php //$a = new Area('Property Rules'); $a->display($c); ?>
    </div>
  </section>
  <div id="book-monthly-form">
    <?php
        $stack = Stack::getByName('Form - Book Monthly');
        $stack->display();
    ?>
  </div>
  <div id="book-weekly-form">
    <?php
    $stack = Stack::getByName('Form - Book Weekly');
    $stack->display();
    ?>
  </div>
<?php } ?>
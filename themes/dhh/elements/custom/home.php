<?php
$reviewList = new ReviewList();
$reviewList->populateBookings();
$reviewList->populateProperties();
$reviewList->filterByRatings(4, '>=');

$count = count($reviewList);
?>
<?php
$a = new Area('Featured Block 1');
$a->display($c);
?>

<div id="explore-city" class="block banner__half">
    <div class="container-fluid">
        <h4>Explore the city</h4>
        <div class="row">
            <?php
                $stack = Stack::getByName('Homepage Promotion Banner');
                $stack->display();
            ?>
        </div>
    </div>
</div>

<?php
$a = new Area('Featured Block 2');
$a->display($c);
?>
<div class="block banner__full">
    <div class="container-fluid">
        <div class="row">
            <?php
                $stack = Stack::getByName('Homepage Full Banner');
                $stack->display();
            ?>
        </div>
    </div>
</div>
<?php
    $a = new Area('Featured Block 3');
    $a->display($c);
?>
<div id="guest" data-count="<?php echo $count; ?>" class="block property__reviews" data-title="Review of our guests">

</div>
<?php
    $a = new Area('Featured Block 4');
    $a->display($c);
?>

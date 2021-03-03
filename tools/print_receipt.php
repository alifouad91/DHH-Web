<?php
$mh = Loader::helper('mail');
$th = new TextHelper();

$u = new User();

$bookingNo = $th->sanitize($_GET['bookingNo']);
if (!$bookingNo) {
    die();
}
if (!$u->getUserID() > 0) {
    $_COOKIE['redirectPath'] = 'index.php/tools/print_receipt?bookingNo=' . $bookingNo;
    header('Location: ' . View::url('/login'));
    die();
}
//153
//booking confirm
$booking = Booking::getByBookingNo($bookingNo);
if (!$booking) {
    die();
}
if ($u->getUserID() != $booking->getUID() && $u->getUserID() != USER_SUPER_ID) {
    die();
}
$mh->addParameter('uName', 'test user');
$mh->addParameter('booking', $booking);
$mh->load('print_booking_confirmed');
$html = $mh->getBodyHTML();
echo $html;
?>
<script>
    window.print();
</script>

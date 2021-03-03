<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 19/3/19
 * Time: 5:31 PM
 */

/** @var AjaxHelper $ah */
$ah = Loader::helper('ajax');

$notifications = new NotificationList();
$notifications->filterByReadStatus(0);
$notifications = $notifications->get(0);

$result        = [];

/** @var Notification $notification */
foreach ($notifications as $notification) {
    array_push($result, [
        'nID'        => $notification->getID(),
        'title'      => $notification->getTitle(),
        'body'       => $notification->getBody(),
        'readStatus' => $notification->getReadStatus(),
    ]);
}

$ah->sendResult($result);
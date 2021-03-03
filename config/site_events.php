<?php
defined('C5_EXECUTE') or die('Access Denied.');

// App Events
Events::extend('on_property_booked', 'SiteEventsHelper', 'on_property_booked', 'helpers/site_events.php');
Events::extend('on_property_reviewed', 'SiteEventsHelper', 'on_property_reviewed', 'helpers/site_events.php');


Events::extend('upcoming_booking', 'SiteEventsHelper', 'upcoming_booking', 'helpers/site_events.php');
Events::extend('past_booking', 'SiteEventsHelper', 'past_booking', 'helpers/site_events.php');
Events::extend('payment_success', 'SiteEventsHelper', 'payment_success', 'helpers/site_events.php');
Events::extend('payment_processing', 'SiteEventsHelper', 'payment_processing', 'helpers/site_events.php');
Events::extend('payment_failed', 'SiteEventsHelper', 'payment_failed', 'helpers/site_events.php');
Events::extend('payment_cancelled', 'SiteEventsHelper', 'payment_cancelled', 'helpers/site_events.php');
Events::extend('rate_booking_email', 'SiteEventsHelper', 'rate_booking_email', 'helpers/site_events.php');


Events::extend('on_utility_add', 'SiteEventsHelper', 'on_utility_add', 'helpers/site_events.php');
Events::extend('send_bill_as_email', 'SiteEventsHelper', 'send_bill_as_email', 'helpers/site_events.php');


Events::extend('on_send_invite', 'SiteEventsHelper', 'on_send_invite', 'helpers/site_events.php');
Events::extend('on_email_verification_success', 'SiteEventsHelper', 'on_email_verification_success', 'helpers/site_events.php');
Events::extend('on_booking_delete', 'SiteEventsHelper', 'on_booking_delete', 'helpers/site_events.php');

<?php
define('SITE', 'Driven Holiday Homes');

define('EMAIL_DEFAULT_FROM_ADDRESS', 'hello@dhh.ae');
define('EMAIL_DEFAULT_FROM_NAME', 'Driven Holiday Homes');
define('FORM_BLOCK_SENDER_EMAIL', EMAIL_DEFAULT_FROM_ADDRESS);
define('ENABLE_MARKETPLACE_SUPPORT', false);
define('ENABLE_NEWSFLOW_OVERLAY', false);
define('URL_REWRITING_ALL', true);
define('PAGE_TITLE_FORMAT', '%2$s | %1$s');
define('APP_TIMEZONE', 'Asia/Dubai');
define('CLIENT_ADMIN_GROUP_NAME', 'Client');
define('PAGING_STRING', 'page');
define('TENTWENTY_ORG_URL', 'https://www.tentwenty.me/');
define('COMPRESS_ASSETS', false);
define('ASYNC_JS', false);
define('DEFAULT_CURRENCY', 'AED');
define('USE_ZEND_CURRENCY', true);
define('CURRENCY_LAYER_API_KEY', '6a159ef5a03cf8721ece77d5e8b7bbce');
define('CURRENCY_LAYER_URL', 'http://www.apilayer.net/api/live?access_key=%s&format=1');


// JWT Configurations
define('JWT_SERVER_SECRET', 'L3LksumXgHbyPx3BnPACAV96JgvntmkEGhfJUsfq');
define('JWT_ID', 'yYdw9DUsEk55SqRJ');
define('JWT_ISSUER', 'drivenholidayhomes.vom');
define('JWT_AUDIENCE_APP', 'com.tentwenty.drivenholidayhomes');
define('MCRYPT_KEY', 'dhhomes1');
define('MCRYPT_IV', 'dhhomes1');

// Date Formats
define('DATE_APP_GENERIC_MDYT_FULL', 'd F Y \a\t g:i A');
define('DATE_APP_GENERIC_MDYT_FULL_SECONDS', 'd F Y \a\t g:i:s A');
define('DATE_APP_GENERIC_MDYT', 'j-n-Y \a\t g:i A');
define('DATE_APP_GENERIC_MDY', 'j-n-Y');
define('DATE_APP_GENERIC_MDY_FULL', 'j F Y');
define('DATE_APP_GENERIC_T', 'g:i A');
define('DATE_APP_GENERIC_TS', 'g:i:s A');
define('DATE_APP_DATE_PICKER', 'd-m-yy');

define('AVATAR_WIDTH', 400);
define('AVATAR_HEIGHT', 400);

//Social Media
//362450004485448
//41c7d816c9339787844cb4a8643bf0ce
define('FACEBOOK_APP_ID', '572625390002065');
define('FACEBOOK_APP_SECRET', '5490ab41d85e14075c2e16d56af6060e');
define('FACEBOOK_GRAPH_VERSION', 'v3.1');
define('FB_REDIRECT_URL', '/index.php/login/facebook');
//482294101430352106805695-ar05f7k4nc73275tjd609nede1asj3e7.apps.googleusercontent.com
//30p4pRfYIQKYc242xUMp2fJL
define('GOOGLE_OAUTH_CLIENT_ID', '482294101430-ar8pt11o4vedllvjipn054sr5fm49rad.apps.googleusercontent.com');
define('GOOGLE_OAUTH_CLIENT_SECRET', 'kPx5QSQhXzI7T9MRmK46btWu');

define('CHAT_PORT','9922');
define('JS_PLUGINS_DIR', '/js/plugins');

define('LANDLORD_GROUP_NAME','Owner');

define('ADMIN_EMAIL_NOTIFICATIONS', true);
define('FILE_VERSION', 8);

define('GCAPTCHA_SITE_KEY', '6Le1I8MZAAAAAGcWEGu925najH3NiyvP97TMgcn1');
define('BILL_KEY', '30p4pR');
<?php
define('APP_BASE_URL','/ui/trunk/importer');
define('TIMEZONE','Asia/Kolkata');
define('LOG_FILE','logs/main.txt');
define('DEBUG',true);//show extensive debug information on each page.
define('PRODUCTION',false);//Strip out development specific frills.

define('PERM_GUEST',1);
define('PERM_DEMO',2|PERM_GUEST);
define('PERM_USER',4|PERM_DEMO);
define('PERM_MOD',8|PERM_USER);
define('PERM_ADMIN',16|PERM_MOD);
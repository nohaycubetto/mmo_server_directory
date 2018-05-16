<?php
define('APP_PATH','/srv/www/releases/gaturro/directorio');
require_once(APP_PATH.'/libs/bootstrap.php');
require_once(APP_PATH.'/libs/chatmode_dao.php');

$chatmode = new ChatModeDAO();
echo $chatmode->getChatModeJSON();
?>

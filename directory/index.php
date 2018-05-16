<?php
define('APP_PATH','/srv/directory');
require_once(APP_PATH.'/libs/bootstrap.php');
require_once(APP_PATH.'/libs/server_dao.php');

$servers = new ServerDAO();
echo $servers->getServerListJSON(1);
?>

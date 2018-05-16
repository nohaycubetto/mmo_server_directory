<?php

$is_debug = $_GET['debug'] == '1';

function debug($what){
	if (!$is_debug)
		return;
	
	echo '<pre>';
	print_r($what);
	echo '</pre>';
}
function redirect($where, $time=0){
	header('Refresh: '.$time.'; '.$where);
}

require_once(APP_PATH.'/config.php');

?>
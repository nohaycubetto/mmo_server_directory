<?php
	define('APP_PATH','/srv/directory');
	require_once(APP_PATH.'/libs/bootstrap.php');
	require_once(APP_PATH.'/libs/server_dao.php');
	
	function handle_error($message){
		redirect('index.php',5);
		die($message);
	}
	
	try {

		$servers = new ServerDAO();	
		$server = $servers->getServerByName($_GET['name']);
		$servers->removeServer($server);
		
		redirect('index.php');
		
	} catch (ServerAlreadyExistsException $e){
		
		handle_error('Server already exists');

	} catch (ServerNotFound $e){
		
		handle_error('Server not found');

	}
?>

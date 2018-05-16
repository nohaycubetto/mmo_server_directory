<?php
	define('APP_PATH','/srv/directory');
	require_once(APP_PATH.'/libs/bootstrap.php');
	require_once(APP_PATH.'/libs/server_dao.php');

	function handle_error($message){
		redirect('index.php',5);
		die($message.' <a href="index.php">ok</a>');
	}
	
	try {

		$servers = new ServerDAO();
		
		
		$priv = new Host($_POST['host'], $_POST['port']);
		$pub = new Host($_POST['pubHost'], $_POST['pubPort']);
		$newserver = new Server($priv, $pub, $_POST['name'], $_POST['capacity']);
		$servers->addServer($newserver);
		
		redirect('index.php');
		
	} catch (ServerAlreadyListed $e){
		
		handle_error('Server Already Exists');

	} catch (InvalidHost $e){

		handle_error('Invalid Host');
		
	} catch (InvalidPublicHost $e){

		handle_error('Invalid Public IP');
		
	} catch (InvalidPort $e){

		handle_error('Invalid Port');
		
	}
?>

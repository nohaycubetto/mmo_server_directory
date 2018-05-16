<?php
	define('APP_PATH','/srv/directory');
	require_once(APP_PATH.'/libs/bootstrap.php');
?>
<?php include(APP_PATH.'/admin/header.php');?>

<form method="post" action="add_controller.php">
	<div>
	<label>Nombre</label>
	<input type="text" name="name" />
	</div>
	
	<div>
	<label>Host Privado</label>
	<input type="text" name="host" />
	</div>
	<div>
	<label>Puerto Privado</label>
	<input type="text" name="port" value="9899"/>
	</div>
	
	<div>
	<label>Host P&uacute;blico</label>
	<input type="text" name="pubHost" />
	</div>
	
	<div>
	<label>Puerto P&uacute;blico</label>
	<input type="text" name="pubPort" value="9899"/>
	</div>	
	
	<div>
	<label>Capacidad</label>
	<input type="text" name="capacity" value="1200"/>
	</div>
	<input type="submit" value="Agregar" name="metodo">
</form>

<?php include(APP_PATH.'/admin/footer.php');?>

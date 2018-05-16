<?php
	define('APP_PATH','/srv/directory');
	require_once(APP_PATH.'/libs/bootstrap.php');
	require_once(APP_PATH.'/libs/server_dao.php');
	
	$dao = new ServerDAO();
	$servers = $dao->getServerList()
	
?>
<?php include(APP_PATH.'/admin/header.php');?>
<div class="panel">
	<a href="add.php">Agregar Servidor</a>
</div>
<table width="500">
	<tr>
		<th>Servidor</th>
		<th>Host privado</th>
		<th>Host p&uacute;blico</th>
		<th>Uso</th>
		<th>Acciones</th>
	</tr>
	
	<?php foreach($servers as $s): ?>
	<tr>
		<td><?php echo $s->getName(); ?></td>
		<td><?php echo $s->getPrivateHost()->getAddress(); ?>:<?php echo $s->getPrivateHost()->getPort(); ?></td>
		<td><?php echo $s->getPublicHost()->getAddress(); ?>:<?php echo $s->getPublicHost()->getPort(); ?></td>
		<td><?php echo $s->getUsage(); ?>%</td>
		<td><a href="remove.php?name=<?php echo $s->getName()?>">Remover</a></td>
	</tr>
	<?php endforeach; ?>
	
</table>

<?php debug($dao->mem->getExtendedStats()); ?>
<?php include(APP_PATH.'/admin/footer.php');?>

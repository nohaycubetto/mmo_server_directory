<?php
require_once(APP_PATH.'/libs/server.php');
require_once(APP_PATH.'/libs/adodb_lite/adodb.inc.php');
require_once(APP_PATH.'/libs/adodb_lite/adodb-exceptions.inc.php');

Class ServerAlreadyListed extends Exception { }
Class ServerNotFound extends Exception { }
Class InvalidPrivateHost extends Exception { }
Class InvalidPublicHost extends Exception { }
Class UnableToConnect extends Exception { }

Class ServerDAO {
	
	var $mem;
	var $serverList;
	var $lastCacheUpdate;
	
	public function ServerDAO(){
		
		//Initialize access to memcache
		$this->mem = new Memcache();
		$this->mem->addServer(MEMCACHE_SERVER);

		//Try to get the server list from mem
		$this->serverList = $this->mem->get('server_list');
		
		//If mem is empty, read from file and write to mem
		if($this->serverList === false)
		{	
			$this->readServerList();
			$this->updateServerListUsage();
			$this->updateCache();
		}
		
		$this->lastCacheUpdate = time() - $this->mem->get('last_cache_update');
		
	}
	
	// Modificadores de la lista
	public function addServer(Server $server){
		
		if (array_key_exists($this->getServerId($server), $this->serverList))
			throw new ServerAlreadyListed;
			
	/*
		TRY TO CONNECT TO SOCKET TO VALIDATE IT
	
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		$priv = $server->getPrivateHost();
		if (!socket_connect($socket, $priv->getAddress()->, $priv->getPort()))
		{	
			throw new UnableToConnect;
		}
	*/	
		
		$this->pushServer($server);
		$this->saveServerList();
		$this->updateServerListUsage();
		$this->updateCache();
	}
	
	private function updateCache(){
		$this->mem->set('server_list', $this->serverList, false, MEMCACHE_EXPIRATION_TIME);
		$this->mem->set('last_cache_update', time(), false, MEMCACHE_EXPIRATION_TIME);
	}
	
	public function removeServer(Server $server){
		unset($this->serverList[$this->getServerId($server)]);
		$this->saveServerList();
		$this->updateServerListUsage();
		$this->updateCache();
	}
	
	private function saveServerList(){
		$rawdata = array();
		
		foreach($this->serverList as $s){
			$priv = $s->getPrivateHost();
			$pub = $s->getPublicHost();
			
			array_push($rawdata, array(
				'host'=>$priv->getAddress(), 'port'=>$priv->getPort(),
				'pubHost'=>$pub->getAddress(), 'pubPort'=>$pub->getPort(),
				'name'=>$s->getName(), 'capacity'=>$s->getCapacity()
			));
		}

		file_put_contents(SERVERLIST, json_encode($rawdata),LOCK_EX);
	}
	
	// What to use to index it on serverList
	private function getServerId($s){
		return $s->getName();
	}
	
	private function pushServer($server){
		
		if(!$server->getPrivateHost()->isValid())
			throw new InvalidPrivateHost;
		
		if(!$server->getPublicHost()->isValid())
			throw new InvalidPublicHost;
		
		$this->serverList[$this->getServerId($server)] = $server;
	}
	
	private function readServerList(){
		
		$rawdata = json_decode(file_get_contents(SERVERLIST));
		
		foreach($rawdata as $s){
			
			$priv = new Host($s->host, $s->port);
			$pub = new Host($s->pubHost, $s->pubPort);
			$server = new Server($priv, $pub, $s->name, $s->capacity);
			
			try
			{
				$this->pushServer($server);
			} catch (InvalidHost $e){
				// Ignore Server
			} catch (InvalidPort $e){
				// Ignore Server
			}
			
		}
		
	}
	
	private function updateServerListUsage(){

		try {
			$db = ADONewConnection(ACCOUNTSDB_DRIVER);
			$db->Connect(ACCOUNTSDB_HOST,ACCOUNTSDB_USER, ACCOUNTSDB_PASS, ACCOUNTSDB_DBNAME);
			$rs = $db->Execute('select server, port, count(profiles.id) as users from profiles where server IS NOT NULL group by server, port');
		
			foreach($rs->GetRows() as $s){
				try {
					$server = $this->getServerByHost($s['server'], $s['port']);
					$server->setUserCount($s['users']);
				} catch (ServerNotFound $e){
					// Do something?
				}
			}
		} catch (ADODB_Exception $e){
			// Do something?
		}

	}
	
	// Busquedas
	public function getServerByName($name){
		
		if (!array_key_exists($name, $this->serverList))
			Throw new ServerNotFound;
		return $this->serverList[$name];
		
	}

	public function getServerByHost($host, $port){
		foreach ($this->serverList as $server) {
			if ($server->getPrivateHost()->getAddress() == $host && $server->getPrivateHost()->getPort() == $port) {
				return $server;
			}
		}
		
		Throw new ServerNotFound;
		
	}
	
	public function getServerList()
	{
		return $this->serverList;
	}

	public function getServerListJSON($shuffle=false)
	{
		
		$prediction = $this->lastCacheUpdate * USER_PREDICTION_PER_SECOND;
		
		$rawdata = array();
		foreach($this->serverList as $s){
			$pub = $s->getPublicHost();
			array_push($rawdata, array(
				'host'=>$pub->getAddress(),'port'=>$pub->getPort(),
				'usage'=>$s->getUsage($prediction),'name'=>$s->getName()
			));
		}
		//Shuffle servers to balance connections to each one
		if ($shuffle) shuffle($rawdata);
		return json_encode($rawdata);
	}

}
?>

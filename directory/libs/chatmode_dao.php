<?php
require_once(APP_PATH.'/libs/server.php');
require_once(APP_PATH.'/libs/adodb_lite/adodb.inc.php');
require_once(APP_PATH.'/libs/adodb_lite/adodb-exceptions.inc.php');

Class ChatModeDAO {
	
	var $mem;
	var $mode;
	
	public function ChatModeDAO(){
		
		//Initialize access to memcache
		$this->mem = new Memcache();
		$this->mem->addServer(MEMCACHE_SERVER);

		//Try to get the cht mode from mem
		$this->mode = $this->mem->get('chat_mode');

		//If mem is empty, read from file and write to mem
		if($this->mode === false)
		{	
			$this->readChatMode();
		}
		
	}
	
	private function updateCache(){
		$this->mem->set('chat_mode', $this->mode, false, MEMCACHE_EXPIRATION_TIME);
	}
	
	private function readChatMode(){
		try {
			$db = ADONewConnection(CONFIGSDB_DRIVER);
			
			$db->Connect(CONFIGSDB_HOST,CONFIGSDB_USER, CONFIGSDB_PASS, CONFIGSDB_DBNAME);
			$rs = $db->Execute('select config from chat_configs where name = \'blackListEnable\' and server_key = \'*\' limit 1;');
			if ($rs->fields('config') === 'false') {
				$this->mode = 'false';
			} else {
				$this->mode = 'true';			
			}

		} catch (ADODB_Exception $e){
			debug($e);
			// Do something?
		}

		$this->updateCache();
	}
	
	public function getChatModeJSON(){
		$rawdata = array();
		$rawdata['chat_mode']=$this->mode;
		return json_encode($rawdata);

	}
}
?>

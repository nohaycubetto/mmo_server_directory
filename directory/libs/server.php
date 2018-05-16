<?php

require_once(APP_PATH.'/libs/host.php');

Class Server {
	
	private $privateHost;
	private $publicHost;
	private $port;
	private $capacity;
	private $name;
	private $userCount = 0;
	private $up = false;
	
	public function Server($privateHost, $publicHost, $name = 'default', $capacity = 0){

		$this->privateHost = $privateHost;
		$this->publicHost = $publicHost;
		$this->name = $name;
		$this->capacity = $capacity;

	}

	
	public function getPrivateHost(){

		return $this->privateHost;
		
	}

	public function getPublicHost(){

		return $this->publicHost;
		
	}

	public function getName(){

		return $this->name;
		
	}
	
	public function getCapacity(){

		return $this->capacity;
		
	}
	
	public function addUser(){
	
		$this->userCount++;
		
	}
	
	public function removeUser(){
	
		$this->userCount--;
		
	}
	
	public function setUserCount($users){
		
		$this->userCount = $users;
		
	}
	
	public function getUserCount(){

		return $this->userCount;
		
	}
	
	public function getUsage($prediction=false){
			
		//If actual userCount is too expensive to read in real time, 
		//the system can predict how many users joined the server since last read
		
		if ($prediction != false){
			return min(($this->userCount + $prediction) / $this->capacity,1);
		}
		
		return min($this->userCount/$this->capacity,1);
		
	}
	
	public function down(){

		$this->up = false;
		
	}
	
	public function up(){

		$this->up = true;
		
	}
	
	public function isUp(){
	
		return $this->up;
		
	}

}
?>
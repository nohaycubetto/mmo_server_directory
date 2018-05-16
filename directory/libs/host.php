<?php

Class Host {
	
	private $address;
	private $port;
	
	public function Host($address, $port = 9899){

		$this->address = $address;
		$this->port = $port;

	}
	public function getAddress(){

		return $this->address;
		
	}

	public function getPort(){

		return $this->port;
		
	}
	
	public function isValid(){
		return !preg_match('/^[a-z0-9-.]+$/', $address);
	}
}
?>
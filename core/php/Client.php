<?php

class Plex_Client extends Plex_MachineAbstract
{
	private $host;
	private $machineIdentifier;
	private $version;
	private $server;
	private $onlyState=false;
	private $state;
	const DEFAULT_PORT = 3000;
	public function __construct($name, $address, $port){
		$this->name = $name;
		$this->address = $address;
		$this->port = $port ? $port : self::DEFAULT_PORT;
	}
	private function getController($type){
		return Plex_Client_ControllerAbstract::factory(
			$type,
			$this->name,
			$this->address,
			$this->port,
			$this->getServer()
		);
	}
	public function getNavigationController(){
		return $this->getController(
			Plex_Client_ControllerAbstract::TYPE_NAVIGATION
		);
	}
	public function getPlaybackController()	{
		return $this->getController(
			Plex_Client_ControllerAbstract::TYPE_PLAYBACK
		);
	}
	public function getApplicationController()	{
		return $this->getController(
			Plex_Client_ControllerAbstract::TYPE_APPLICATION
		);
	}
	public function getName(){
		return $this->name;
	}
	public function getAddress(){
		return $this->address;
	}
	public function getPort(){
		return $this->port;
	}
	public function getHost(){
		return $this->host;
	}
	public function setHost($host)	{
		$this->host = $host;
	}
	public function getMachineIdentifier()	{
		return $this->machineIdentifier;
	}
	public function setMachineIdentifier($machineIdentifier){
		$this->machineIdentifier = $machineIdentifier;
	}
	public function getVersion(){
		return $this->state;
	}
	public function setVersion($version){
		$this->version = $version;
	}
	protected function getServer(){
		return $this->server;
	}
	public function setServer(Plex_Server $server){
		$this->server = $server;
	}
	public function getOnlyState(){
		return $this->onlyState;
	}	
	public function setOnlyState($onlyState){
		$this->onlyState = $onlyState;
	}
	public function getState(){
		return $this->state;
	}
	public function setState($state){
		$this->state = $state;
	}
}

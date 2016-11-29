<?php
class Plex_Server extends Plex_MachineAbstract
{
	private $machineIdentifier;
	const DEFAULT_PORT = 32400;
	const ENDPOINT_CLIENT = 'clients';
	const ENDPOINT_STATUS = 'status';
	const ENDPOINT_SESSIONS = 'sessions';
	
	public function __construct($name, $address, $port, $token=''){
		$this->name = $name;
		$this->address = $address;
		$this->port = $port ? $port : self::DEFAULT_PORT;
		$this->token = $token;
	}
	public function ServerInforamation(){
		$Server=$this->makeCall($this->getBaseUrl(),true);
		if(isset($Server['machineIdentifier']))
			$this->setMachineIdentifier($Server['machineIdentifier']);
	}
	public function getClients(){
		$url = sprintf(
			'%s/%s',
			$this->getBaseUrl(),
			self::ENDPOINT_CLIENT
		);
		$clients = array();
		$clientArray = $this->makeCall($url);
		
		foreach ($clientArray as $attribute) {
			$client = new Plex_Client(
				$attribute['name'],
				$attribute['address'],
				(int) $attribute['port']
			);
			$client->setHost($attribute['host']);
			$client->setMachineIdentifier($attribute['machineIdentifier']);
			$client->setVersion($attribute['version']);
			$client->setServer($this);
			$clients[$attribute['name']] = $client;
		}
		$this->getPlayerSessions($clients);
		return $clients;
	}
	public function getPlayerSessions($clients){
		$url = sprintf(
			'%s/%s',
			$this->getBaseUrl(),
			self::ENDPOINT_STATUS.'/'.
			self::ENDPOINT_SESSIONS
		);
		$Sessions = array();
		$SessionArray = $this->makeCall($url);
		foreach ($SessionArray as $Session) {			
			foreach ($Session['Player'] as $attribute) {
				if(isset($clients[$attribute['device']]))
					$client=$clients[$attribute['device']];
				else{
					$port = isset($attribute['port']) ? $attribute['port'] : 3000;
					$client = new Plex_Client(
						$attribute['device'],
						$attribute['address'],
						$port
					);
				if(isset($attribute['host']))
					$client->setHost($attribute['host']);
				if(isset($attribute['machineIdentifier']))
					$client->setMachineIdentifier($attribute['machineIdentifier']);
				if(isset($attribute['version']))
					$client->setVersion($attribute['version']);
					$client->setOnlyState(true);
					$client->setServer($this);
				}
				return true;
			}
		}
		return false;
	}

	public function getLibrary(){
		return new Plex_Server_Library(
			$this->name,
			$this->address,
			$this->port
		);
	}
	public function getActiveSession(){
		return new Plex_Server_Session(
			$this->name,
			$this->address,
			$this->port
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
	
	public function getMachineIdentifier()	{
		return $this->machineIdentifier;
	}
	public function setMachineIdentifier($machineIdentifier){
		$this->machineIdentifier = $machineIdentifier;
	}
}

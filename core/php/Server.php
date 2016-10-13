<?php
class Plex_Server extends Plex_MachineAbstract
{
	const DEFAULT_PORT = 32400;
	const ENDPOINT_CLIENT = 'clients';
	const ENDPOINT_STATUS = 'status';
	const ENDPOINT_SESSIONS = 'sessions';
	public function __construct($name, $address, $port, $token){
		$this->name = $name;
		$this->address = $address;
		$this->port = $port ? $port : self::DEFAULT_PORT;
		$this->token = $token;
		$this->ServerInforamation();
	}
	public function ServerInforamation(){
		$this->makeCall($this->getBaseUrl());

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

	/*<Player address="192.168.0.xxx" 
		device="Samsung TV"
		machineIdentifier="u7cl6gdvurels"
		model=""
		platform="Samsung"
		platformVersion=""
		product="Plex for Samsung" 
		profile="Samsung" 
		state="playing" 
		title="TV UE55H6200" 
		vendor="" version="2.005" />
	*/
	public function getPlayerSessions($clients){
		$url = sprintf(
			'%s/%s',
			$this->getBaseUrl(),
			self::ENDPOINT_STATUS.'/'.
			self::ENDPOINT_SESSIONS
		);
		$Sessions = array();
		$SessionArray = $this->makeCall($url);
		if(isset($SessionArray['Player'])){
		foreach ($SessionArray['Player'] as $attribute) {
			log::add('plex','debug',"Status: ".json_encode($attribute));
				if(isset($clients[$attribute['device']]))
					$client=$clients[$attribute['device']];
				else{
					$client = new Plex_Client(
						$attribute['device'],
						$attribute['address'],
						(int) $attribute['port']
					);
					$client->setHost($attribute['host']);
					$client->setMachineIdentifier($attribute['machineIdentifier']);
					$client->setVersion($attribute['version']);
					$client->setOnlyState(true);
					$client->setServer($this);
				}
				if ($attribute['state'] == "playing" )
					$client->setState(true);
				else
					$client->setState(false);
			}
		}
	}

	public function getLibrary(){
		return new Plex_Server_Library(
			$this->name,
			$this->address,
			$this->port
		);
	}
	public function getName()
	{
		return $this->name;
	}
	
	public function getAddress()
	{
		return $this->address;
	}
	public function getPort()
	{
		return $this->port;
	}
}

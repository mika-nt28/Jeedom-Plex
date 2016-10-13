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
		/*<?xml version="1.0" encoding="UTF-8"?> 
			<MediaContainer size="19" 
				allowCameraUpload="0" 
				allowChannelAccess="1" 
				allowSharing="1"
				allowSync="0" 
				backgroundProcessing="1" 
				certificate="1" co
				mpanionProxy="1"
				diagnostics="logs,databases" 
				eventStream ="1" 
				friendlyName="Homeserver" 
				hubSearch="1"
				machineIdentifier="1da10d1286afd8e3c3f85e25bc18c02f235e1ab0" 
				multiuser="1" 
				myPlex="1" 
				myPlexMappingState="mapped" 
				myPlexSigninState="ok" 
				myPlexSubscription="0" 
				platform="Linux" platformVersion="3.16.0-4-686-pae (#1 SMP Debian 3.16.36-1+deb8u1 (2016-09-03))" 
				pluginHost="1" 
				readOnlyLibraries="0"
				requestParametersInCookie="1" 
				sync="1" 
				transcoderActiveVideoSessions="0" 
				transcoderAudio="1" 
				transcoderLyrics="1"
				transcoderPhoto="1" 
				transcoderSubtitles="1" 
				transcoderVideo="1" 
				transcoderVideoBitrates="64,96,208,320,720,1500,2000,3000,4000,8000,10000,12000,20000"
				transcoderVideoQualities="0,1,2,3,4,5,6,7,8,9,10,11,12" 
				transcoderVideoResolutions="128,128,160,240,320,480,768,720,720,1080,1080,1080,1080" 
				updatedAt="1476289283" 
				updater="1" 
				version="1.1.4.2757-24ffd60"> 
				<Directory count="1"
					key="activity" 
					title="activity" />
				<Directory count="1"
					key="butler" 
					title="butler" /> 
				<Directory count="1" key="channels" title="channels" /> 
				<Directory count="1" key="clients" title="clients" /> 
				<Directory count="1" key="diagnostics" title="diagnostics" /> 
				<Directory count="1" key="hubs" title="hubs" />
				<Directory count="1" key="library" title="library" />
				<Directory count="1" key="neighborhood" title="neighborhood" /> 
				<Directory count="1" key="playQueues" title="playQueues" /> 
				<Directory count="1" key="player" title="player" />
				<Directory count="1" key="playlists" title="playlists" /> 
				<Directory count="1" key="resources" title="resources" /> 
				<Directory count="1" key="search" title="search" /> 
				<Directory count="1" key="server" title="server" /> 
				<Directory count="1" key="servers" title="servers" /> 
				<Directory count="1" key="statistics" title="statistics" />
				<Directory count="1" key="system" title="system" /> 
				<Directory count="1" key="transcode" title="transcode" />
				<Directory count="1" key="updater" title="updater" /> 
			</MediaContainer>
*/
		$Server=$this->makeCall($this->getBaseUrl());
		foreach ($Server as $attribute) {
			$this->setMachineIdentifier($attribute['machineIdentifier']);
		}
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

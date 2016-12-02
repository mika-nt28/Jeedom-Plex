<?php
$phpPlexDir = dirname(__FILE__);
// Exception
require_once(sprintf('%s/Exception/ExceptionInterface.php', $phpPlexDir));
require_once(sprintf('%s/Exception/ExceptionAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Exception/Machine.php', $phpPlexDir));
require_once(sprintf('%s/Exception/Server.php', $phpPlexDir));
require_once(sprintf('%s/Exception/Server/Library.php', $phpPlexDir));
// Machine
require_once(sprintf('%s/Machine/MachineInterface.php', $phpPlexDir));
require_once(sprintf('%s/Machine/MachineAbstract.php', $phpPlexDir));
// Server
require_once(sprintf('%s/Server.php', $phpPlexDir));
require_once(sprintf('%s/Server/Session.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/SectionAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Section/Movie.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Section/Show.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Section/Artist.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Section/Photo.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Media/File/FileInterface.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Media/File/File.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Media/MediaInterface.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Media/Media.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/ItemInterface.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/ItemAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/ItemGrandparentAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/ItemParentAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/ItemChildAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Movie.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Show.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Season.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Episode.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Artist.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Album.php', $phpPlexDir));
require_once(sprintf('%s/Server/Library/Item/Track.php', $phpPlexDir));
// Client
require_once(sprintf('%s/Client.php', $phpPlexDir));
require_once(sprintf('%s/Client/ControllerAbstract.php', $phpPlexDir));
require_once(sprintf('%s/Client/Controller/Navigation.php', $phpPlexDir));
require_once(sprintf('%s/Client/Controller/Playback.php', $phpPlexDir));
require_once(sprintf('%s/Client/Controller/Application.php', $phpPlexDir));

class PlexApi
{

	private static $servers = array();
	private static $clients = array();
	private $token = '';
	
	public function getToken($username, $password)
	{
		$host = "https://plex.tv/users/sign_in.json";
		$header = array(
			   'Content-Type: application/xml; charset=utf-8', 
			   'Content-Length: 0', 
			   'X-Plex-Client-Identifier: 8334-8A72-4C28-FDAF-29AB-479E-4069-C3A3',
			   'X-Plex-Product: JeedomPlex',
			   'X-Plex-Version: v1_06',
			   );
		$process = curl_init($host);
		curl_setopt($process, CURLOPT_HTTPHEADER, $header);
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($process);
		log::add('plex','debug','Token: '.$data);
		$curlError = curl_error($process);
		$json = json_decode($data, true);
		$this->token=$json['user']['authentication_token'];
		config::save('PlexToken',$json['user']['authentication_token'], 'plex');
	}
	public function registerServers(array $servers)
	{
		// Register each server.
		foreach ($servers as $name => $server) {
			$port = isset($server['port']) ? $server['port'] : NULL;
			self::$servers[$name] = new Plex_Server(
				$name,
				$server['address'],
				$port, 
				$this->token
			);
			self::$servers[$name]->ServerInforamation();
		}

		// We are going to use the first server in the list to get a list of the
		// availalble clients and register those automatically.
		$serverName = reset(array_keys(self::$servers));
		$this->registerClients(
			$this->getServer($serverName)->getClients()
		);
	}
	
	public function UpdateClientStatus()
	{
		// We are going to use the first server in the list to get a list of the
		// availalble clients and register those automatically.
		$serverName = reset(array_keys(self::$servers));
		$this->getPlayerSessions(
			$this->getServer($serverName)->getClients()
		);
	}
   	private function registerClients(array $clients)
	{
		self::$clients = $clients;
	}
	public function getServer($serverName)
	{
		if (!isset(self::$servers[$serverName])) {
			throw new Plex_Exception_Server(
				'RESOURCE_NOT_FOUND', 
				array($serverName)
			);
		}
		return self::$servers[$serverName];
	}
	public function getClients()
	{
		return self::$clients;
	}
	public function getClient($clientName)
	{
		if(isset(self::$clients[$clientName]))
			return self::$clients[$clientName];
	}
}

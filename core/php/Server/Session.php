<?php
	class Plex_Server_Session extends Plex_Server{
		const ENDPOINT_STATUS = 'status';
		const ENDPOINT_SESSIONS = 'sessions';
		private ActiveSessions=null;
		protected function buildUrl(){
			$url = sprintf(
				'%s/%s',
				$this->getBaseUrl(),
				self::ENDPOINT_STATUS.'/'.
				self::ENDPOINT_SESSIONS
			);
			return $url;
		}
		public function __construct(){
			$this->ActiveSessions = $this->makeCall($this->buildUrl($endpoint));
		}
		public  function getPlayer($clients){
			foreach ($this->ActiveSessions as $Session) {		
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
	}
?>

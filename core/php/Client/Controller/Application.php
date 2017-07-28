<?php
class Plex_Client_Controller_Application extends Plex_Client_ControllerAbstract
{
	
	public function playMedia($key, $viewOffset = NULL)	{
		$params = array(
			'offset' =>0,
			'X-Plex-Client-Identifier'=>$this->getMachineIdentifier(),
			'machineIdentifier'=>$this->getServer()->getMachineIdentifier(),
			'address'=>$this->getServer()->getAddress(),
			'port'=> $this->getServer()->getPort(),
			'protocol'=> 'http',
			'key' => $key,
			'path' => sprintf(
				'%s%s',
				$this->getServer()->getBaseUrl(),
				$key
			)
		);
		if ($viewOffset) {
			$params['viewOffset'] = $viewOffset;
		}
		$this->executeCommand($params);
	}
	
	/**
	 * Sets the volume to the given percentage level.
	 *
	 * @param integer $level The percentage level to which teh voume is to be
	 * set.
	 *
	 * @uses Plex_Client_ControllerAbstract::executeCommand()
	 *
	 * @return void
	 */
	public function setVolume($level)
	{
		$params = array(
			'level' => $level
		);
		
		$this->executeCommand($params);
	}
}

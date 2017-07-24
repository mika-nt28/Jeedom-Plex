<?php
abstract class Plex_MachineAbstract implements Plex_MachineInterface
{
	protected $name;
	protected $address;
	protected $port;
	protected function getBaseUrl()
	{	
		$http="";
		if(strrpos($this->address,"http")=== false)
			$http="http://";
		return sprintf(
			'%s%s:%s',
			$http,
			$this->address,
			$this->port
		);
	}
	protected function xmlMediaContainerAttributesToArray($xml)
	{
		if (!$xml) return false;
		
		$array = array();
		foreach($xml->attributes() as $key => $value) {
			// For abstraction, everything is casted to string. It is the
			// responsibility of the calling method to handle typing.
			$array[$key] = (string) $value[0];
		}
		return $array;
	}
	protected function xmlAttributesToArray($xml, $pass = 0)
	{
		if (!$xml) return false;
		
		$array = array();
		
		// The first level of attributes are attributes about the request. To 
		// date I haven't found an immediate need for them, so on pass 0 we just
		// ignore those attributes and move straight on to the child elements.
		if ($pass > 0) {
			foreach($xml->attributes() as $key => $value) {
				// For abstraction, everything is casted to string. It is the
				// responsibility of the calling method to handle typing.
				$array[$key] = (string) $value[0];
			}
		}
		
		foreach($xml->children() as $element => $child) {
			if ($pass > 0) {
				// If we are on our second pass then we start to cvare about the
				// name of the elements. In this case we index them using it so
				// we can get proper recursion.
				$array[$element][] = $this->xmlAttributesToArray(
					$child,
					($pass+1)
				);
			} else {
				// On our first pass, we don' care about the name of the 
				// element. We only care about the attributes of each individual
				// member of the element, so we just send it right through.
				$array[] = $this->xmlAttributesToArray($child, ($pass+1));
			}
		}
		
		return $array;
	}
	protected function makeCall($url,$MediaContainer=false){
		$token=config::byKey('PlexToken', 'plex');
		if($token !='' && isset($token)){
			if(stripos($url,'?')>0)
				$url.='&';
			else
				$url.='?';
			$url.= 'X-Plex-Token='.$token;
		}
		$ch = curl_init();
      		log::add('plex','debug','Connexion a '. $url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
		
		$response = curl_exec($ch);

      		log::add('plex','debug','response: '. $response);
		if ($response === false) {
      			log::add('plex','debug',curl_strerror(curl_error($ch)));
			throw new Plex_Exception_Machine(
				'CURL_ERROR',
				array(curl_errno($ch), curl_error($ch))
			);
		}
		
		curl_close($ch);
		
		$xml = simplexml_load_string($response);
		if($MediaContainer)
			return $this->xmlMediaContainerAttributesToArray($xml);
		else
			return $this->xmlAttributesToArray($xml);
	}
	protected function getCallingFunction($depth = 2)
	{
		$backtrace = debug_backtrace();

		return $backtrace[$depth]['function'];
	}
}

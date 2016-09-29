<?php

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'Plex', 'php', 'plex');
include_file('core', 'plex', 'config', 'plex');

class plex extends eqLogic {
    /*     * *************************Attributs****************************** */
	public $plex;
	public $server;
	public $client;
	public $onlyState;
	/*     * ***********************Methode static*************************** */
	public static function UpdateStatus() {
		while(true){
			$eqLogics = eqLogic::byType('plex');
			foreach($eqLogics as $plexClient) {
				if ($plexClient->getIsEnable() == 1 && $plexClient->getConfiguration('heartbeat',0) == 1) {
					$plexClient->ConnexionsPlex();
					$plexClient->getCmd(null,'state')->event($this->client->getState());
					//$MediaOffset=$plexClient->getCmd(null,'viewOffset');
					//$MediaOffsetOldValue=$MediaOffset->execCmd();
					$MediaOffset->execute();
					/*$MediaOffsetNewValue=$MediaOffset->execCmd();
					if($MediaOffsetOldValue == $MediaOffsetNewValue){
						$plexClient->getCmd(null,'state')->event(false);
					}
					else{
						$plexClient->getCmd(null,'state')->event(true);
					}*/
				}
				//$plexClient->refreshWidget();
			}
			sleep(1);
		}
	}
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'plex';	
		$return['state'] = 'nok';
		$cron = cron::byClassAndFunction('plex', 'UpdateStatus');
		if(is_object($cron)&& $cron->running())
			$return['state'] = 'ok';
		if(config::byKey('name', 'plex')!=''&&config::byKey('addr', 'plex')!=''&&config::byKey('port', 'plex')!='')
			$return['launchable'] = 'ok';
		else
			$return['launchable'] = 'nok';
		return $return;
	}
	public static function deamon_start($_debug = false) {
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') 
			return;
		log::remove('plex');
		self::deamon_stop();
		
		$cron = cron::byClassAndFunction('plex', 'UpdateStatus');
		if (!is_object($cron)) {
			$cron = new cron();
			$cron->setClass('plex');
			$cron->setFunction('UpdateStatus');
			$cron->setEnable(1);
			$cron->setDeamon(1);
			$cron->setSchedule('* * * * *');
			$cron->setTimeout('999999');
			$cron->save();
		}
		$cron->start();
		$cron->run();
	}
	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('plex', 'UpdateStatus');
		if (is_object($cron)) {
			$cron->stop();
			$cron->remove();
		}
	}
   
	public static function LibraryInforamtion($section){
		if(method_exists($section,'getKey'))
			$return['Key']=$section->getKey();
		if(method_exists($section,'getRatingKey'))
			$return['RatingKey']=$section->getRatingKey();
		if(method_exists($section,'getType'))
			$return['Type']=$section->getType();
		if(method_exists($section,'getTitle'))
			$return['Title']=$section->getTitle();
		if(method_exists($section,'getTitleSort'))
			$return['TitleSort']=$section->getTitleSort();
		if(method_exists($section,'getAgent'))
			$return['Agent']=$section->getAgent();
		if(method_exists($section,'getScanner'))
			$return['Scanner']=$section->getScanner();
		if(method_exists($section,'getLanguage'))
			$return['Language']=$section->getLanguage();
		if(method_exists($section,'getUuid'))
			$return['Uuid']=$section->getUuid();
		if(method_exists($section,'getAddedAt'))
			$return['AddedAt']=$section->getAddedAt();
		if(method_exists($section,'getUpdatedAt'))
			$return['UpdatedAt']=$section->getUpdatedAt();
		if(method_exists($section,'getCreatedAt'))
			$return['CreatedAt']=$section->getCreatedAt();
		return $return;
	}
	public static function filterMedia($section, $Filtre=null,$param){
		$reponse=null;
		switch($Filtre)
		{
			default:	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getAllMovies();
						break;
					case 'show':
						$reponse=$section->getAllShows();
						break;
					case 'artist':
						$reponse=$section->getAllAlbums();
						break;
				}	
			break;
			case 'Unwatched':		
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getUnwatchedMovies();
						break;
					case 'show':		
						$reponse=$section->getUnwatchedShows();
						break;
					case 'artist':
						break;
				}
			break;
			case 'RecentlyReleased':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getRecentlyReleasedMovies();
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}	
			break;
			case 'RecentlyAdded':		
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getRecentlyAddedMovies();
						break;
					case 'show':
						break;
					case 'artist':
						$reponse=$section->getRecentlyAddedAlbums();
						break;
				}
			break;
			case 'RecentlyViewed':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getRecentlyViewedMovies();
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}
			break;
			case 'OnDeck':		
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getOnDeckMovies();
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}
			break;
			case 'ByYear':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByYear($param['Year']);
						break;
					case 'show':
						$reponse=$section->getShowsByYear($param[0]);
						break;
					case 'artist':
						break;
				}
			break;
			case 'ByDecade':		
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByDecade($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}
			break;
			case 'ByContentRating':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByContentRating($param[0]);
						break;
					case 'show':
						$reponse=$section->getShowsByContentRating($param[0]);
						break;
				}
			break;
			case 'ByResolution':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByResolution($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}	
			break;
			case 'ByFirstCharacter':
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByFirstCharacter($param[0]);
						break;
					case 'show':
						$reponse=$section->getShowsByFirstCharacter($param[0]);
						break;
					case 'artist':
						break;
				}
			break;
			case 'ByTitle':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMovie($param['Video']);
						break;
					case 'show':
						$show = $section->getShow($param['Show']);
						if(isset($param['Season'])){
							$Season=$show->getSeason($param['Season']);
							if(isset($param['Episode']))
								$reponse=$Season->getEpisode($param['Episode']);
							else
								$reponse=$Season->getEpisodes();
						}else{
							$reponse=$show->getSeasons();
						}
						break;
					case 'artist':
						if(isset($param['Track']))
							$reponse=$section->getTrack($param['Track']);
						else{
							$Albums=$section->getAllAlbums();
							foreach ($Albums as $Album) {
								if($Album->getTitle() == $param['Album'])
									$reponse=$Album->getTracks();
							}
						}
						break;
				}	
			break;
			case 'ByCollection':
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByCollection($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}		
			break;
			case 'ByGenre':		
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByGenre($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						$reponse=$section->getGenres();
						break;
				}
			break;
			case 'ByDirector':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByDirector($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}	
			break;
			case 'ByActor':		
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->getMoviesByActor($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						break;
				}
			break;
			case 'ByArtists':		
				if ($param[0]=='')
					$reponse=$section->getAllArtists();
				else
					$reponse=$section->getAllArtists($param[0]);
			break;
			case 'ByCollections':		
				if ($param[0]=='')
					$reponse=$section->getCollections();
				else
					$reponse=$section->getCollections($param[0]);
			break;
			case 'ByAlbums':		
				$reponse=$section->getAllAlbums();
			break;
			case 'search':	
				switch($section->getType())
				{
					case 'movie':
						$reponse=$section->searchMovies($param[0]);
						break;
					case 'show':
						break;
					case 'artist':
						$reponse=$section->searchTracks($param[0]);
						break;
				}	
			break;
		}
		return $reponse;
	}
	public static function ListMedia($media){
		$return =array();
		if(method_exists($media,'getKey'))
			$return['Key']=$media->getKey();
		if(method_exists($media,'getType'))
			$return['Type']=$media->getType();
		if(method_exists($media,'getTitle'))
			$return['Title']=$media->getTitle();
		if(method_exists($media,'getThumb'))
			$return['Poster']='http://'.config::byKey('addr', 'plex').':'.config::byKey('port', 'plex').$media->getThumb();
		if(method_exists($media,'getYear'))
			$return['Année']=$media->getYear();
		if(method_exists($media,'getDuration'))
			$return['Duration']=$media->getDuration();
		if(method_exists($media,'getTagline'))
			$return['Tagline']=$media->getTagline();
		if(method_exists($media,'getRatingKey'))
			$return['RatingKey']=$media->getRatingKey();
		//if(method_exists($media,'getThumb'))
			//$return['StudioFlag']=$media->getThumb();
		//if(method_exists($media,'getThumb'))
			//$return['Audio']=$media->getThumb();
		//if(method_exists($media,'getThumb'))
			//$return['Subtitles']=$media->getThumb();
		if(method_exists($media,'getDirectors'))
			$return['Realisateur']=$media->getDirectors();
		//if(method_exists($media,'getThumb())
			//$return['Scenariste']=$media->getThumb();
		if(method_exists($media,'getActors'))
			$return['Acteurs']=$media->getActors();
		if(method_exists($media,'getSummary'))
			$return['Summary']=$media->getSummary();
		if(method_exists($media,'getAddedAt'))
			$return['AddedAt']=$media->getAddedAt();
		if(method_exists($media,'getUpdatedAt'))
			$return['UpdatedAt']=$media->getUpdatedAt();
		if(method_exists($media,'getArtist'))
			$return['Artist']=$media->getArtist();
			$return['Genre']=array('Name'=>'','Href'=>'');
		return $return;
	}
	/*     * *********************Methode d'instance************************* */
   	public function ConnexionsPlex(){
	   	$servers = array(
			config::byKey('name', 'plex') => array(
				'address' => config::byKey('addr', 'plex'),
				'port' => config::byKey('port', 'plex')
			)
		);
		if($this->plex == ""){
			$this->plex = new PlexApi();
			$this->plex->getToken(config::byKey('PlexUser', 'plex'),config::byKey('PlexPassword', 'plex'));
			if($this->server == ""){
				$this->plex->registerServers($servers);
				$this->server=$this->plex->getServer(config::byKey('name', 'plex'));
				$this->client=$this->plex->getClient($this->getLogicalId())
				$this->onlyState=$this->client->getOnlyState();
			}
		}	
	}	
	public function getClients(){
		$this->ConnexionsPlex();	
		$Clients=$this->plex->getClients();
      	log::add('plex','debug', json_encode($Clients));
		return $Clients;
	}
	public function getLibrary(){
		$this->ConnexionsPlex();	
		$sections=$this->server->getLibrary()->getSections();
		$return=array();
		foreach($sections as $section)
			$return[]=self::LibraryInforamtion($section);
		return $return;
	}
	public function getMedia($Filtre=null,$param=''){
		$param=json_decode($param, true);
		$this->ConnexionsPlex();	
		$section=$this->server->getLibrary()->getSection($param['Library']);
		$reponse=self::filterMedia($section, $Filtre,$param);
		$return =array();
		if($reponse != null){
			if(count($reponse)>1){
				foreach($reponse as $media)
				{
					$return['Media'][]=self::ListMedia($media);
				}
			}
			else
				$return['Media']=self::ListMedia($reponse);
		}
		$return['Library']=self::LibraryInforamtion($section);
		return $return;
	}
	
	public function AddCmd($cmdPlex) 	{
		$Commande = $this->getCmd(null,$cmdPlex['configuration']['commande']);
		if (!is_object($Commande))
		{
			$Commande = new plexCmd();
			$Commande->setId(null);
			$Commande->setName($cmdPlex['name']);
			$Commande->setLogicalId($cmdPlex['configuration']['commande']);
			$Commande->setEqLogic_id($this->getId());
		}
		$Commande->setType($cmdPlex['type']);
		$Commande->setSubType($cmdPlex['subType']);
		reset($cmdPlex['configuration']);
		while ($configuration = current($cmdPlex['configuration'])) {
			$Commande->setConfiguration(key($cmdPlex['configuration']),$configuration);
			next($cmdPlex['configuration']);
		}
		if(isset($cmdPlex['display']['icon']))
			$Commande->setDisplay('icon',$cmdPlex['display']['icon']);
		$Commande->save();
		return $Commande;
	}
	public function preUpdate() {
		
		if ($this->getLogicalId() == '') {
            throw new Exception(__('Un client doit etre choisi pour poursuivre',__FILE__));
        }
		if ($this->getConfiguration('volume_inc') != '' && ($this->getConfiguration('volume_inc') <=  0 || $this->getConfiguration('volume_inc') >=  100)) {
            throw new Exception(__('Le volume +/- doit être > 0 et < 100',__FILE__));
        }
    }  
    public function preInsert() {
		$this->setCategory('multimedia', 1);
		$this->setConfiguration('text_color','#BACEC8');
	}    
    public function postSave() {
		if (!$this->getId())
          return;
		global $listCmdPLEX;
		if($this->getLogicalId()!= ""){
			$this->ConnexionsPlex();
			if($this->onlyState()){
				$cmdPlex=	array(
					'name' => 'Etat du player',
					'configuration' => array(
						'categorie' => 'Application',
						'commande' => 'state',
					),
					'type' => 'info',
					'subType' => 'binary',
					'description' => 'Etat du player',
				);
				$this->AddCmd($cmdPlex);
			}else{
				foreach ($listCmdPLEX as $cmdPlex) {
					$this->AddCmd($cmdPlex);
				}
			}
		}
    }	
	public function toHtml($_version = 'dashboard') {
		if ($this->getIsEnable() != 1) {
			return '';
		}
		if (!$this->hasRight('r')) {
			return '';
		}
		$version = jeedom::versionAlias($_version);
		if ($this->getDisplay('hideOn' . $version) == 1) {
			return '';
		}
	/*	$mc = cache::byKey('plexWidget' . jeedom::versionAlias($_version) . $this->getId());
		if ($mc->getValue() != '') {
			return preg_replace("/" . preg_quote(self::UIDDELIMITER) . "(.*?)" . preg_quote(self::UIDDELIMITER) . "/", self::UIDDELIMITER . mt_rand() . self::UIDDELIMITER, $mc->getValue());
		}*/
		$EtatPlayer=is_object($this->getCmd(null, 'state'))?$this->getCmd(null, 'state')->execCmd():0;
		if($EtatPlayer=='')
			$EtatPlayer=0;
		$Media=is_object($this->getCmd(null, 'media'))?str_replace("'","\'",$this->getCmd(null, 'media')->execCmd()):'{}';
		if($Media=='')
			$Media='{}';		
		$replace = array(
			'#id#' => $this->getId(),
			'#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#uid#' => 'plex' . $this->getId()/* . self::UIDDELIMITER . mt_rand() . self::UIDDELIMITER*/,
			'#action#' => (isset($action)) ? $action : '',
			'#background#' => ($this->getConfiguration('has_image_fond')) ? 'background-color: #000000; background-image: url(plugins/plex/core/template/dashboard/plex.png); background-repeat: no-repeat; background-position: center 0px;background-size: contain;#style#' : 'background-color:'.($this->getBackgroundColor($_version)).';#style#\'',
			'#media#' =>$Media,
			'#state#' => $EtatPlayer,
			'#volume#' => is_object($this->getCmd(null, 'volume'))?$this->getCmd(null, 'volume')->execCmd():0,
			'#volume_id#' => is_object($this->getCmd(null, 'setVolume'))?$this->getCmd(null, 'setVolume')->getId():'',
			'#viewOffset#' => is_object($this->getCmd(null, 'viewOffset'))?$this->getCmd(null, 'viewOffset')->execCmd():'',
			'#getDuration#' => is_object($this->getCmd(null, 'getDuration'))?$this->getCmd(null, 'getDuration')->execCmd():'',
			//'#skipNext#' => is_object($this->getCmd(null, 'skipNext'))?$this->getCmd(null, 'skipNext')->execCmd():'',
		);
		//ACTION
		foreach ($this->getCmd('action') as $cmd) {
			if ($cmd->getIsVisible()) {
				$replace['#cmd_' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
			} else {
				$replace['#cmd_' . $cmd->getLogicalId() . '_id#'] = 'No';
			}
		}
		$parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {    
				log::add('plex','debug','Received : ' .$value . ' from ' .$key);
                $replace['#' . $key . '#'] = $value;
            }
        }
        $html = template_replace($replace, getTemplate('core', $_version, 'eqLogic', 'plex'));
        cache::set('plexWidget' . $_version . $this->getId(), $html, 0);
        return $html;
	}  
}

class plexCmd extends cmd {
     public function execute($_options = null) {
		$response='';
		$this->getEqLogic()->ConnexionsPlex();	
		$plex=$this->getEqLogic()->plex;
		$server = $this->getEqLogic()->server;	
		$client = $this->getEqLogic()->client;
		if(is_object($client)){
			switch ($this->getType()) {
				case 'action' :
					switch ($this->getSubType()) {
						case 'slider':    
							$Value = $_options['slider'];
						break;
						case 'color':
							$Value = $_options['color'];
						break;
						case 'message':
							$Value = $_options['message'];
						break;
					}
				break;
			}			
			switch ($this->getConfiguration('categorie'))
			{
				case 'Playback':
					$playback = $client->getPlaybackController();
					switch ($this->getConfiguration('commande'))
					{
						case 'play':
							$response=$playback->play();
						break;
						case 'pause':
							$response=$playback->pause();
						break;
						case 'stop':
							$response=$playback->stop();
						break;
						case 'rewind':
							$response=$playback->rewind();
						break;
						case 'fastForward':
							$response=$playback->fastForward();
						break;
						case 'stepForward':
							$response=$playback->stepForward();
						break;
						case 'bigStepForward':
							$response=$playback->bigStepForward();
						break;
						case 'stepBack':
							$response=$playback->stepBack();
						break;
						case 'bigStepBack':
							$response=$playback->bigStepBack();
						break;
						case 'skipNext':
							$response=$playback->skipNext();
						break;
						case 'skipPrevious':
							$response=$playback->skipPrevious();
						break;
					}
				break;
				case 'Navigation':
					$navigation = $client->getNavigationController();
					switch ($this->getConfiguration('commande'))
					{
						case 'moveUp':
							$response=$navigation->moveUp();
						break;
						case 'moveDown':
							$response=$navigation->moveDown();
						break;
						case 'moveLeft':
							$response=$navigation->moveLeft();
						break;
						case 'moveRight':
							$response=$navigation->moveRight();
						break;
						case 'pageUp':
							$response=$navigation->pageUp();
						break;
						case 'pageDown':
							$response=$navigation->pageDown();
						break;
						case 'nextLetter':
							$response=$navigation->nextLetter();
						break;
						case 'previousLetter':
							$response=$navigation->previousLetter();
						break;
						case 'select':
							$response=$navigation->select();
						break;
						case 'back':
							$response=$navigation->back();
						break;
						case 'contextMenu':
							$response=$navigation->contextMenu();
						break;
						case 'toggleOSD':
							$response=$navigation->toggleOSD();
						break;
					}
				break;
				case 'Application':
					$application = $client->getApplicationController();
					//$navigation = $client->getNavigationController();		
					$mediaInforamtion= json_decode($this->getEqLogic()->getCmd(null,'media')->execCmd(), true);
					$section=$server->getLibrary()->getSection($mediaInforamtion['Library']);
					$media= plex::filterMedia($section,'ByTitle', $mediaInforamtion);
					switch ($this->getConfiguration('commande'))
					{
						case 'viewOffset':
							$response=$media->getViewOffset();
						break;
						case 'playMedia':
							// Play episode from beginning
							$response=$application->playMedia($media);
						break;
						case 'playMediaLastStopped':
							// Play epsiode from where it was last stopped
							//$response=$application->playMedia($episode, $media->getViewOffset());
						break;
						case 'setVolume':
							// Set voume to half
							$response=$application->setVolume($Value);
							//$navigation->toggleOSD();
						break;
					}
				break;
			}
		}
		return $response;	
    }
}
?>

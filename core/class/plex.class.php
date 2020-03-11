<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'Plex', 'php', 'plex');
class plex extends eqLogic {
	public static $_widgetPossibility = array('custom' => array(
	        'visibility' => true,
	        'displayName' => true,
	        'optionalParameters' => true,
	));
	public static $_plex;
	public static $_server;
	public $_client;
	public $_onlyState=false;
	public static function UpdateStatus() {
		while(true){
			$eqLogics = eqLogic::byType('plex');
			if(is_array($eqLogics)){
				foreach($eqLogics as $plexClient) {
					if($plexClient->getIsEnable() && $plexClient->getLogicalId() != '' && $plexClient->getConfiguration('heartbeat',0) == 1)
						$plexClient->StateControl();
				}
			}
			sleep(10);
		}
	}
	public function StateControl() {
		$this->ConnexionsPlex();
		if(isset($this->_client) && is_object($this->_client)){
			$Serveur=self::$_plex->getServer($this->getCmd(null,'serverState')->execCmd());
			if(is_object($Serveur)){
				$session=$Serveur->getActiveSession();
				$Etat=$session->getPlayer(array($this->getLogicalId()));
				$this->checkAndUpdateCmd('state',$Etat);
				$ItemsSession=$session->getItems();
				if (count($ItemsSession)>0){
					$this->checkAndUpdateCmd('type',$ItemsSession[0]->getType());
					log::add('plex','debug','Type de media : '.$ItemsSession[0]->getType());
					$this->checkAndUpdateCmd('media',$ItemsSession[0]->getTitle());		
					cache::set('plex::MediaKey::'.$this->getId(),$ItemsSession[0]->getKey(), 0);
					log::add('plex','debug','Titre de media : '.$ItemsSession[0]->getTitle());
					$this->checkAndUpdateCmd('viewOffset',$ItemsSession[0]->getViewOffset());
					log::add('plex','debug','Temps de lecture : '.$ItemsSession[0]->getViewOffset());
				}
			}
		}
	}
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'plex';	
		$cron = cron::byClassAndFunction('plex', 'UpdateStatus');
		if(is_object($cron) && $cron->running())
			$return['state'] = 'ok';
		else
			$return['state'] = 'nok';
		if(count(config::byKey('configuration', 'plex'))>0)
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
			case 'ByKey':	
				$reponse=null;
				/*if($param['Type'] =='')
					$Type=$section->getType();
				else
					$Type=$param['Type'];*/
				switch($param['Type'])
				{
					case 'movie':
						$reponse=$section->getMovie($param['Key']);
					break;
					case 'track':
						$reponse=$section->getTrack($param['Key']);
					break;
					case 'album':
						$reponse=$section->getTrack($param['Key']);
					break;
					case 'artist':
						if(stripos($param['Key'],'children') === FALSE)
							$reponse=$section->getTrack($param['Key']);
						else{
							$Albums=$section->getAllAlbums();
							foreach ($Albums as $Album) {
								if($Album->getKey() == $param['Key'])
									$reponse=$Album->getTracks();
							}
						}
					break;
					case 'show':
						if(stripos($param['Key'],'children') === FALSE)
							$reponse=$section->getShow($param['Key']);
						else{
							$Shows=$section->getAllShows();
							foreach ($Shows as $Show) {
								if($Show->getKey() == $param['Key'])
									$reponse=$Show->getSeasons();
							}
						}
					break;
					case 'season':
							$Seasons=$section->getSeason($param['Key']);
							$reponse=$Season->getEpisodes();
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
				$reponse=null;
				switch($section->getType())
				{
					case 'movie':
						if(isset($param['Video']))
							$reponse=$section->getMovie($param['Video']);
						break;
					case 'show':
						if(isset($param['Show'])){
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
						}
						break;
					case 'artist':
						if(isset($param['Track']))
							$reponse=$section->getTrack($param['Track']);
						else{
							$Albums=$section->getAllAlbums();
							foreach ($Albums as $Album) {
								if(isset($param['Album'])){
									if($Album->getTitle() == $param['Album'])
										$reponse=$Album->getTracks();
								}
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
	public function ListMedia($media){
		$return =array();
		if(method_exists($media,'getKey'))
			$return['Key']=$media->getKey();
		if(method_exists($media,'getType'))
			$return['Type']=$media->getType();
		if(method_exists($media,'getTitle'))
			$return['Title']=$media->getTitle();
		if(method_exists($media,'getThumb')){
			$Serveur=self::$_plex->getServer($this->getCmd(null,'serverState')->execCmd());
			if(is_object($Serveur)){
				$return['Poster']='http://'.$Serveur->getAddress().':'.$Serveur->getPort().$media->getThumb();
			}
		}
		if(method_exists($media,'getYear'))
			$return['Année']=$media->getYear();
		if(method_exists($media,'getDuration'))
			$return['Duration']=$media->getDuration();
		if(method_exists($media,'getTagline'))
			$return['Tagline']=$media->getTagline();
		if(method_exists($media,'getRatingKey'))
			$return['RatingKey']=$media->getRatingKey();
		if(method_exists($media,'studio'))
			$return['StudioFlag']=$media->getStudio();
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
		if(method_exists($media,'getGenre'))
			$return['Genre']=array('Name'=>'','Href'=>'');
		return $return;
	}
	/*     * *********************Methode d'instance************************* */
   	public function ConnexionsPlex(){
		if(!is_object(self::$_plex)){
			self::$_plex = new PlexApi();
			if(config::byKey('PlexUser', 'plex') != '' && config::byKey('PlexPassword', 'plex') != '')
				self::$_plex->getToken(config::byKey('PlexUser', 'plex'),config::byKey('PlexPassword', 'plex'));
		//}	
		//if(!is_object(self::$_server)){
			$Serveurs=config::byKey('configuration','plex');
			self::$_plex->registerServers($Serveurs);
			$Serveur=$this->getCmd(null,'serverState')->execCmd();
			if($Serveur == ''){
				foreach($Serveurs as $name => $param){
					$Serveur=$name;
					break;
				}
			}
			self::$_server=self::$_plex->getServer($Serveur);
		}
		if(!is_object($this->_client)){
			$this->_client=self::$_plex->getClient($this->getLogicalId());
			if(is_object($this->_client)){
				if($this->getConfiguration('HostUpdate') !="")
					$this->_client->setHost($this->getConfiguration('HostUpdate'));
				$this->_onlyState=$this->_client->getOnlyState();
			}else
				log::add('plex','debug','Impossible de trouver le client '.$this->getLogicalId());
		}
	}	
	public function getClients(){
		$this->ConnexionsPlex();	
		$Clients=self::$_plex->getClients();
		return $Clients;
	}
	public function getLibrary(){
		$this->ConnexionsPlex();	
		$sections=self::$_server->getLibrary()->getSections();
		$return=array();
		foreach($sections as $section)
			$return[]=self::LibraryInforamtion($section);
		return $return;
	}
	public function getMedia($Filtre=null,$param=''){
		$param=json_decode($param, true);
		$this->ConnexionsPlex();
		if(stripos($param['Key'],'library') === FALSE){
			$section=self::$_server->getLibrary()->getSectionByKey($param['Key']);
			$reponse=self::filterMedia($section, $Filtre,$param);
		}else
			$reponse=self::$_server->getLibrary()->byMediaKey($param['Key']);
		$return =array();
		if($reponse != null){
			if(count($reponse)>1){
				foreach($reponse as $media)
				{
					$return['Media'][]=$this->ListMedia($media);
				}
			}else{
				$media=$reponse[0];
				$return['Media']=$this->ListMedia($media);
				$param['Key']="/library/metadata/".$media->getParentRatingKey()."/children";
				if($param['Key']==''){
					$param['Key']=$media->getLibrarySectionId();
				}
				$Parent=$this->getMedia('ByKey',json_encode($param));
				$return['Parent']=$Parent['Media'];
				
			}
		}
		return $return;
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
		if(!is_array(config::byKey('configuration','plex')))
			return;
		if (!$this->getId())
			return;
		$etat=$this->AddCommande('Serveur séléctioné','serverState',"info","string");
		$server=$this->AddCommande('Choix du Serveur','server',"action","select");
		$server->setValue($etat->getId());
		$list='';
		foreach(config::byKey('configuration','plex') as $name => $param){
			if($list!='')
				$list.=';';
			else
				$server->event($name);
			$list.=$name.'|'.$name;
		}
		$server->setConfiguration('listValue',$list);
		$server->save();
		if($this->getLogicalId()!= ""){
			$this->AddCommande('Etat du player','state',"info","string","Application","Plex_State");
			$this->checkAndUpdateCmd('state','stop');
			$this->AddCommande('Type de media lue','type',"info","string","Application");
			$this->AddCommande('Media en cours','media',"info","string","Application","Plex_media");
			$this->ConnexionsPlex();
			if(!$this->_onlyState){
				$this->AddCommande('Bitrate','getBitrate',"info","string","Media");
				$this->AddCommande('Duration','getDuration',"info","string","Media","Plex_Duration");
				$this->AddCommande('View Media Offset','viewOffset',"info","string","Application","Plex_Duration");
				$this->AddCommande('Volume','setVolume',"action","slider","Application");
				$this->AddCommande('Play Media','playMedia',"action","other","Application","Plex_telecommande",'<i class="fa fa-play"></i>');
				$this->AddCommande('Play Media Last Stopped','playMediaLastStopped',"action","other","Application","Plex_telecommande",'<i class="fa fa-play"></i>');
				$this->AddCommande('Back','back',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-reply"></i>');
				$this->AddCommande('Up','moveUp',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-arrow-up"></i>');
				$this->AddCommande('Left','moveLeft',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-left"></i>');
				$this->AddCommande('Right','moveRight',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-right"></i>');
				$this->AddCommande('Down','moveDown',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-down"></i>');
				$this->AddCommande('Page Up','pageUp',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-chevron-up"></i>');
				$this->AddCommande('Page Down','pageDown',"action","other","","Plex_telecommande",'<i class="fa fa-chevron-down"></i>');
				$this->AddCommande('Next Letter','nextLetter',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-caret-square-o-up"></i>');
				$this->AddCommande('Previous Letter','previousLetter',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-caret-square-o-down"></i>');
				$this->AddCommande('Select','select',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-check"></i>');
				$this->AddCommande('Context Menu','contextMenu',"action","other","Navigation","Plex_telecommande",'<i class="fa fa-home"></i>');
				$this->AddCommande('Toggle OSD','toggleOSD',"action","other","Navigation","Plex_telecommande",'<i class="icon techno-television4"></i>');
				$this->AddCommande('Rewind','rewind',"action","other","Playback","Plex_telecommande",'<i class="fa fa-backward"></i>');
				$this->AddCommande('Fast Forward','fastForward',"action","other","Playback","Plex_telecommande",'<i class="fa fa-forward"></i>');
				$this->AddCommande('Step Backward','stepBack',"action","other","Playback","Plex_telecommande",'<i class="fa fa-step-backward"></i>');
				$this->AddCommande('Big Step Forward','bigStepForward',"action","other","Playback","Plex_telecommande",'<i class="fa fa-fast-forward"></i>');
				$this->AddCommande('Play','play',"action","other","Playback","Plex_telecommande",'<i class="fa fa-play"></i>');
				$this->AddCommande('Pause','pause',"action","other","Playback","Plex_telecommande",'<i class="fa fa-pause"></i>');
				$this->AddCommande('Stop','stop',"action","other","Playback","Plex_telecommande",'<i class="fa fa-stop"></i>');
				$this->AddCommande('Step Forward','stepForward',"action","other","Playback","Plex_telecommande",'<i class="fa fa-step-forward"></i>');
				$this->AddCommande('Big Step Back','bigStepBack',"action","other","Playback","Plex_telecommande",'<i class="fa fa-fast-backward"></i>');
				$this->AddCommande('Skip Next','skipNext',"action","other","Playback","Plex_telecommande",'<i class="fa fa-arrow-right"></i>');
				$this->AddCommande('Skip Previous','skipPrevious',"action","other","Playback","Plex_telecommande",'<i class="fa fa-arrow-left"></i>');
			}
		}
    	}	
	public function AddCommande($Name,$_logicalId,$Type="info", $SubType='binary',$categorie='',$Template='',$icon='',$generic_type='') {
		$Commande = $this->getCmd(null,$_logicalId);
		if (!is_object($Commande))
		{
			$Commande = new plexCmd();
			$Commande->setId(null);
			$Commande->setName($Name);
			$Commande->setIsVisible(1);
			$Commande->setLogicalId($_logicalId);
			$Commande->setEqLogic_id($this->getId());
		}
		$Commande->setType($Type);
		$Commande->setSubType($SubType);
   		$Commande->setTemplate('dashboard',$Template );
		$Commande->setTemplate('mobile', $Template);
		$Commande->setDisplay('icon', $icon);
		$Commande->setDisplay('generic_type', $generic_type);
		$Commande->setConfiguration('categorie',$categorie);
		$Commande->save();
		return $Commande;
	}
	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) 
			return $replace;
		$version = jeedom::versionAlias($_version);
		if ($this->getDisplay('hideOn' . $version) == 1) 
			return '';
		foreach ($this->getCmd() as $cmd) {
			$replace['#' . $cmd->getLogicalId() . '#'] = '';
			if ($cmd->getIsVisible()){
				$replace['#'. $cmd->getLogicalId() . '#'] = $cmd->toHtml($_version);
			}
		}
        	return template_replace($replace, getTemplate('core', $_version, 'eqLogic', 'plex'));
	}  
}
class plexCmd extends cmd {
     public function execute($_options = null) {
	     	if($this->getLogicalId()=='server'){
			$this->getEqLogic()->checkAndUpdateCmd('serverState',$_options['select']);
			return;
		}
		$response='';
		$this->getEqLogic()->ConnexionsPlex();	
		$plex= plex::$_plex;
		$server = plex::$_server;	
		$client = $this->getEqLogic()->_client;
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
					switch ($this->getLogicalId()){
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
					switch ($this->getLogicalId()){
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
					$navigation = $client->getNavigationController();
					switch ($this->getLogicalId()){
						/*case 'viewOffset':
							$response=0;
							if(method_exists($media,'getViewOffset'))
								$response=$media->getViewOffset();
							
						break;*/
						case 'playMedia':
							// Play episode from beginning
							if(method_exists($application,'playMedia')){
								log::add('plex','debug','Execution de playMedia');
								$cache = cache::byKey('plex::MediaKey::'.$this->getEqLogic()->getId());
								$response=$application->playMedia($cache->getValue(''));
							}
						break;
						case 'setVolume':
							// Set voume to half
							if(method_exists($application,'setVolume'))
								$response=$application->setVolume($Value);
							$navigation->toggleOSD();
						break;
					}
				break;
			}
		}	
    	}
}
?>

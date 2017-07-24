<?php
global $listCmdPLEX;
$listCmdPLEX = array(
	array(
		'name' => 'Etat du player',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'state'

		),
		'type' => 'info',
		'subType' => 'binary',
		'display' => array(
			'template' => 'Plex_State'
        	),
		'description' => 'Etat du player'
  	 ),
	array(
		'name' => 'Type de media lue',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'type'

		),
		'type' => 'info',
		'subType' => 'string',
		'description' => 'Type de media lue'
  	 ),
	array(
		'name' => 'Media en cours',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'media'
		),
		'type' => 'info',
		'subType' => 'string',
		'display' => array(
			'template' => 'Plex_media'
        	),
		'description' => 'Media en cours'
    	),
	array(
		'name' => 'Set Volume',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'setVolume',
		    'minValue' => -60,
		    'maxValue' => 0

		),
		'type' => 'action',
		'subType' => 'slider',
		'description' => 'Change le volume'
    	),
	array(
		'name' => 'Play Media',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'playMedia',
		),
		'type' => 'action',
		'subType' => 'other',
		'display' => array(
			'icon' => '<i class="fa fa-play"></i>',
			'template' => 'Plex_telecommande'
        	),
		'description' => 'Lire le media défini'
    	),
	array(
		'name' => 'Play Media Last Stopped',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'playMediaLastStopped'
		),
		'type' => 'action',
		'subType' => 'other',
		'display' => array(
			'icon' => '<i class="fa fa-play"></i>',
			'template' => 'Plex_telecommande'
        	),
		'description' => 'Reprendre le media défini ou on s\était arreté'
    	),
	array(
		'name' => 'View Media Offset',
		'configuration' => array(
		    'categorie' => 'Application',
		    'commande' => 'viewOffset'
		),
		'type' => 'info',
		'subType' => 'string',
		'description' => 'Affiche la position de lecture du media',
		'display' => array(
				'template' => 'Plex_Duration'
		)
    	),
	array(
		'name' => 'Back',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'back'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers précédent',
		'display' => array(
			'icon' => '<i class="fa fa-reply"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Up',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'moveUp'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers le haut',
		'display' => array(
			'icon' => '<i class="fa fa-arrow-up"></i>',
			'template' => 'Plex_telecommande'
       		)
    	),
	array(
		'name' => 'Left',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'moveLeft'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers la gauche',
		'display' => array(
			'icon' => '<i class="fa fa-arrow-left"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Right',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'moveRight'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers la droite',
		'display' => array(
			'icon' => '<i class="fa fa-arrow-right"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Down',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'moveDown'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers le bas',
		'display' => array(
			'icon' => '<i class="fa fa-arrow-down"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Page Up',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'pageUp'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation Page Haut',
		'display' => array(	
			'icon' => '<i class="fa fa-chevron-up"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Page Down',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'pageDown'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation Page bas',
		'display' => array(
			'icon' => '<i class="fa fa-chevron-down"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Next Letter',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'nextLetter'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation passe a la lettre suivante',
		'display' => array(
			'icon' => '<i class="fa fa-caret-square-o-up"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Previous Letter',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'previousLetter'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation passe a la lettre précédente',
		'display' => array(
			'icon' => '<i class="fa fa-caret-square-o-down"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Select',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'select'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Valider',
		'display' => array(
			'icon' => '<i class="fa fa-check"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Context Menu',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'contextMenu'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers le menu',
		'display' => array(	
			'icon' => '<i class="fa fa-home"></i>',
			'template' => 'Plex_telecommande'
       		)
    	),
	array(
		'name' => 'Toggle OSD',
		'configuration' => array(
		    'categorie' => 'Navigation',
		    'commande' => 'toggleOSD'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Navigation vers l\'OSD',
		'display' => array(
			'icon' => '<i class="icon techno-television4"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Rewind',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'rewind'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Recule',
		'display' => array(
			'icon' => '<i class="fa fa-backward"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Fast Forward',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'fastForward'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Avance rapide',
		'display' => array(
			'icon' => '<i class="fa fa-forward"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Step Backward',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'stepBack'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Recule de 15 secondes',
		'display' => array(
			'icon' => '<i class="fa fa-step-backward"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Big Step Forward',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'bigStepForward'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Avance de 10 minute',
		'display' => array(
			'icon' => '<i class="fa fa-fast-forward"></i>',
			'template' => 'Plex_telecommande'
       		)
    	),
	array(
		'name' => 'Play',
		'configuration' => array(
		'categorie' => 'Playback',
			'commande' => 'play'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Met en pause ou lecture',
		'display' => array(
			'icon' => '<i class="fa fa-play"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Pause',
		'configuration' => array(
		'categorie' => 'Playback',
			'commande' => 'pause'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Met en pause ou lecture',
		'display' => array(
			'icon' => '<i class="fa fa-pause"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Stop',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'stop'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Stop la lecture',
		'display' => array(
			'icon' => '<i class="fa fa-stop"></i>',
			'template' => 'Plex_telecommande'
       	 )
    	),
	array(
		'name' => 'Step Forward',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'stepForward'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Avance de 30 secondes',
		'display' => array(
			'icon' => '<i class="fa fa-step-forward"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Big Step Back',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'bigStepBack'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Recule de 10 minute',
		'display' => array(
			'icon' => '<i class="fa fa-fast-backward"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Skip Next',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'skipNext'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Chapitre suivant',
		'display' => array(
			'icon' => '<i class="fa fa-arrow-right"></i>',
			'template' => 'Plex_telecommande'
      	  	)
    	),
	array(
		'name' => 'Skip Previous',
		'configuration' => array(
		    'categorie' => 'Playback',
		    'commande' => 'skipPrevious'
		),
		'type' => 'action',
		'subType' => 'other',
		'description' => 'Chapitre précédent',
		'display' => array(
			'icon' => '<i class="fa fa-arrow-left"></i>',
			'template' => 'Plex_telecommande'
        	)
    	),
	array(
		'name' => 'Duration',
		'configuration' => array(
		    'categorie' => 'Media',
		    'commande' => 'getDuration'
		),
		'type' => 'info',
		'subType' => 'string',
		'description' => 'Durée du media',
		'display' => array(
			'template' => 'Plex_Duration'
		)
    	),
	array(
		'name' => 'Bitrate',
		'configuration' => array(
		    'categorie' => 'Media',
		    'commande' => 'getBitrate'
		),
		'type' => 'info',
		'subType' => 'string',
		'description' => 'Bitrate du media'
   	)
);
?>

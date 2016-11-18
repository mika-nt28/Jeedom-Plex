			//EQUALIZER//
var dots = $('.dot');
var TIMEFRAME = 1;
var MAX_HEIGHT = 11;
var MIN_HEIGHT = 1;
var MAX_COLOR = 5;
var MIN_COLOR = 5;
//var COLOR = '189,195,199'; // RGB
var COLOR = '67,74,84';

dots.each(function (i) {
  $(this).css({ 'margin-left': (i * 0.2) + 'em'});
});

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * MAX_HEIGHT) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/2 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * TIMEFRAME);

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 2) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/1.5 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (TIMEFRAME/5)));

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 5) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/1.5 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (2*TIMEFRAME/5)));

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 2) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/1.5 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (3*TIMEFRAME/5)));

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 8) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/4 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (4*TIMEFRAME/5)));

var Media#uid#=jQuery.parseJSON('#media#');
var Init=true;
function telecommande(){
	$(".plex[data-eqLogic_uid=#uid#] .telecommande_plex_widget").animate({right:"0px"});
	$(".plex[data-eqLogic_uid=#uid#] .telecommande_plex_widget").attr("class","telecommande_plex_widget shadow_fenetre_plex");
	if ($(".plex[data-eqLogic_uid=#uid#] .media_panel").css("left") == "0px")
		listefermer();
}
function telecommandeferme(){
	$(".plex[data-eqLogic_uid=#uid#] .telecommande_plex_widget").animate({right:"-200px"});
	$(".plex[data-eqLogic_uid=#uid#] .telecommande_plex_widget").attr("class","telecommande_plex_widget");
}
// Partie liste ///
function UpdateDetail(Data){
	//Mise a jours des etoiles		
	$('.plex[data-eqLogic_uid=#uid#] .item-summary').text(Data.Media.Summary);
	//$('.item-duration').text(SecondToDuration(Data.Media.Duration));

	if (typeof(Data.Media.Duration) != "undefined")
		$('.item-duration').text(SecondToDuration(Data.Media.Duration)).show();
	else
		$('.item-duration').hide();
	for (var i in Data.Media.Genre) {
		$('.plex[data-eqLogic_uid=#uid#] .item-genre .metadata-tags')
			.append($('<a class="pivot-link">')
			.attr('href',Data.Media.Genre[i].Href)
			.text(Data.Media.Genre[i].Name));
	}
	for (var i in Data.Media.Realisateur) {
		$('.plex[data-eqLogic_uid=#uid#] .item-director .metadata-tags')
			.append($('<span class="metadata-tag-list">')
			.append($('<a class="pivot-link">')
			.attr('href',Data.Media.Realisateur[i].Href)
			.text(Data.Media.Realisateur[i].Name)));
	}
	for (var i in Data.Media.Scenariste) {
		$('.plex[data-eqLogic_uid=#uid#] .item-writer .metadata-tags')
			.append($('<span class="metadata-tag-list">')
			.append($('<a class="pivot-link">')
			.attr('href',Data.Media.Scenariste[i].Href)
			.text(Data.Media.Scenariste[i].Name)));
	}
	for (var i in Data.Media.Acteurs) {
		$('.plex[data-eqLogic_uid=#uid#] .item-cast .metadata-tags')
			.append($('<span class="metadata-tag-list">')
			.append($('<a class="pivot-link">')
			.attr('href',Data.Media.Acteurs[i].Href)
			.text(Data.Media.Acteurs[i].Name)));
	}
	$('.plex[data-eqLogic_uid=#uid#] #audio-dropdown').text(Data.Media.Audio);
	$('.plex[data-eqLogic_uid=#uid#] #subtitles-dropdown').text(Data.Media.Subtitles);
	$('.plex[data-eqLogic_uid=#uid#] .studio-flag').attr('src',Data.Media.StudioFlag);
}
function UpdateList(Data){
	$('.plex[data-eqLogic_uid=#uid#] #ListeMedia ul').html('');
	$('.plex[data-eqLogic_uid=#uid#] .jaquette_plex').html('');
	for (var i in Data.Media) {
		$('.plex[data-eqLogic_uid=#uid#] #ListeMedia ul')
			.append($('<li>')
				.addClass('btm-media-plex')
				.attr('data-type_media',Data.Media[i].Type)
				.attr('data-id_media',i)//Data.Media[i].RatingKey)
				.text(Data.Media[i].Title));
		var ThumbMenu=$("<img>")
			.addClass("affiche")
			.attr('src','plugins/plex/core/php/Poster.php?url='+Data.Media[i].Poster)
			.attr('data-type_media',Data.Media[i].Type)
			.attr('title',Data.Media[i].Title)
			.hide();
		if(i==0){
			if (typeof(Data.Media[i].Duration) != "undefined")
				$('.plex[data-eqLogic_uid=#uid#] .time_haut_plex_widget').text(SecondToDuration(Data.Media[i].Duration)).show();
			else
				$('.plex[data-eqLogic_uid=#uid#] .time_haut_plex_widget').hide();
			$('.plex[data-eqLogic_uid=#uid#] .gros_titre_plex').attr('title',Data.Media[i].Title).text(Data.Media[i].Title);
			$('.plex[data-eqLogic_uid=#uid#] .petit_titre_plex').attr('title',Data.Media[i].Tagline).text(Data.Media[i].Tagline);
			ThumbMenu.addClass("affiche_premiere_plex").show();
		}
		if(i==1)
			ThumbMenu.addClass("affiche_next_plex").show();
		$('.plex[data-eqLogic_uid=#uid#] .jaquette_plex').append(ThumbMenu);
	}
}
$('.plex[data-eqLogic_uid=#uid#]').on('click',".affiche", function() {
	$('.plex[data-eqLogic_uid=#uid#] .affiche').hide();
	$('.plex[data-eqLogic_uid=#uid#] .affiche').removeClass("affiche_premiere_plex");
	$('.plex[data-eqLogic_uid=#uid#] .affiche').removeClass("affiche_next_plex");
	$('.plex[data-eqLogic_uid=#uid#] .affiche').removeClass("affiche_prev_plex");
	$(this).addClass("affiche_premiere_plex").show();
	$(this).next().addClass("affiche_next_plex").show();
	$(this).prev().addClass("affiche_prev_plex").show();
	switch($('.plex[data-eqLogic_uid=#uid#] .btm-library-plex[data-lib_title="'+Media#uid#.Library+'"]').data('lib_type')){
		default:
			Media#uid#.Video=$(this).attr("title");
		break;
		case 'show':
			switch($(this).data('type_media')){
				case 'show':
					Media#uid#.Show=$(this).attr("title");
				break;
				case 'season':
					Media#uid#.Season=$(this).attr("title");
				break;
				case 'episode':
					Media#uid#.Episode=$(this).attr("title");
				break;
			}	
		break;
		case 'artist':
			if($(this).data('type_media')== 'album')
				Media#uid#.Album=$(this).attr("title");
			else	
				Media#uid#.Tarck=$(this).attr("title");
		break;
	}
	UpdateCmd('media', JSON.stringify(Media#uid#));
	UpdateInforamtion(Media#uid#);
});
function liste){
	$(".plex[data-eqLogic_uid=#uid#] .media_panel").animate({left:"0px"});
	$(".plex[data-eqLogic_uid=#uid#] .media_panel").attr("class","media_panel shadow_fenetre_plex");
	if ($(".plex[data-eqLogic_uid=#uid#] .media_panel").css("right") == "0px")
		telecommandefermer();
}
function listefermer(){
	$(".plex[data-eqLogic_uid=#uid#] .media_panel").animate({left:"-200px"});
	$(".plex[data-eqLogic_uid=#uid#] .media_panel").attr("class","media_panel");
}

// fonction Pause et Play //
var state="#state#";
if(state == 0){
	$(".eqLogic[data-eqLogic_uid=#uid#] .pause_in_cercle").hide();
	$(".eqLogic[data-eqLogic_uid=#uid#] .play_in_cercle").show();
}else{
	$(".eqLogic[data-eqLogic_uid=#uid#] .pause_in_cercle").show();
	$(".eqLogic[data-eqLogic_uid=#uid#] .play_in_cercle").hide();
}

// fonction Musique ou Video ///
$.ajax({
	type: "POST",
	timeout:8000, 
	url: "plugins/plex/core/ajax/plex.ajax.php",
	data: {
		Id:'#id#',
		action: "getLibrary",
	},
	dataType: 'json',
	error: function(request, status, error) {
		handleAjaxError(request, status, error);
	},
	success: function(data) { 
		if (data.state != 'ok') {
			$('#div_alert').showAlert({message: data.result, level: 'danger'});
			return;
		}
		if (data.result!=false){
			for (var i in data.result) {
				switch(data.result[i].Type){
					default:
					break;
					case 'movie':
					$('.plex[data-eqLogic_uid=#uid#] .media_Library').append($('<img class="btm-library-plex cursor noRefresh tooltips" src="plugins/plex/core/template/dashboard/images/cinema.png" data-lib_type="'+data.result[i].Type+'" data-lib_title="'+data.result[i].Title+'" title="'+data.result[i].Title+'" />'));
					break;
					case 'show':
					$('.plex[data-eqLogic_uid=#uid#] .media_Library').append($('<img class="btm-library-plex cursor noRefresh tooltips" src="plugins/plex/core/template/dashboard/images/tv.png" data-lib_type="'+data.result[i].Type+'" data-lib_title="'+data.result[i].Title+'" title="'+data.result[i].Title+'" />'));
					break;
					case 'artist':
					$('.plex[data-eqLogic_uid=#uid#] .media_Library').append($('<img class="btm-library-plex cursor noRefresh tooltips" src="plugins/plex/core/template/dashboard/images/music.png" data-lib_type="'+data.result[i].Type+'" data-lib_title="'+data.result[i].Title+'" title="'+data.result[i].Title+'" />'));
					break;
				}					
			}
			if (typeof(Media#uid#.Library) != "undefined" && Init)
				$('.plex[data-eqLogic_uid=#uid#] .btm-library-plex').first().trigger('click');
			else
				Init=false;
		}
	}
});
$('.plex[data-eqLogic_uid=#uid#]').on('click',".btm-library-plex", function() {				
	$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').trigger('click');		
	$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').show();
	$('.plex[data-eqLogic_uid=#uid#] .Filtre').show();
	$('.plex[data-eqLogic_uid=#uid#] .Detail').hide();
	$('.plex[data-eqLogic_uid=#uid#] .Playback').hide();
	$('.plex[data-eqLogic_uid=#uid#] .Cast').hide();
	$('.plex[data-eqLogic_uid=#uid#] .Poster').hide();
	if(!Init){
		Media#uid# = {};
		Media#uid#.Library=$(this).data('lib_title');
	}
	if (Media#uid#.Library!=''){
		$.ajax({
			type: "POST",
			timeout:8000, 
			url: "plugins/plex/core/ajax/plex.ajax.php",
			data: {
				Id:'#id#',
				action: "SearchMedia",
				Filtre:'',
				param:JSON.stringify(Media#uid#),
			},
			dataType: 'json',
			error: function(request, status, error) {
				handleAjaxError(request, status, error);
			},
			success: function(data) { 
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				if (data.result!=false){		
					UpdateList(data.result);
					if (typeof(Media#uid#.Video) != "undefined" || typeof(Media#uid#.Show) != "undefined" || typeof(Media#uid#.Album) != "undefined" && Init)
						$('.plex[data-eqLogic_uid=#uid#] .btm-media-plex').first().trigger('click');
					else{
						Init=false;
						UpdateCmd('media', JSON.stringify(Media#uid#));
					}
				}
			}
		});
	}
});
$('.plex[data-eqLogic_uid=#uid#]').on('click',".btm-media-plex", function() {
	if (Init){
		var InitMedia = JSON.parse(JSON.stringify(Media#uid#));
		delete InitMedia.Episode;
		delete InitMedia.Tarck;
		UpdateInforamtion(InitMedia);
	}else{
		switch($('.plex[data-eqLogic_uid=#uid#] .btm-library-plex[data-lib_title="'+Media#uid#.Library+'"]').data('lib_type')){
			default:
				Media#uid#.Video=$(this).text();
			break;
			case 'show':
				switch($(this).data('type_media')){
					case 'show':
						Media#uid#.Show=$(this).text();
					break;
					case 'season':
						Media#uid#.Season=$(this).text();
					break;
					case 'episode':
						Media#uid#.Episode=$(this).text();
					break;
				}	
			break;
			case 'artist':
				if($(this).data('type_media')== 'album')
					Media#uid#.Album=$(this).text();
				else
					Media#uid#.Tarck=$(this).text();
			break;
		}
	UpdateCmd('media', JSON.stringify(Media#uid#));
	}
	UpdateInforamtion(Media#uid#);
	Init=false;
});
$('.plex[data-eqLogic_uid=#uid#]').on('click',".menu_plex", function() {
	if (!Init){
		switch($(this).text()){
			case Media#uid#.Library:
				Media#uid#='{}';
				Media#uid#.Library=$(this).text();
			break;
			case Media#uid#.Video:
				Media#uid#.Video=$(this).text();
			break;
			case Media#uid#.Show:
				Media#uid#.Show=$(this).text();
				delete Media#uid#.Season;
				delete Media#uid#.Episode;
			break;
			case Media#uid#.Season:
				Media#uid#.Season=$(this).text();
				delete Media#uid#.Episode;
			break;
			case Media#uid#.Episode:
				Media#uid#.Episode=$(this).text();
			break;
			case Media#uid#.Album:
				delete Media#uid#.Episode;
			Media#uid#.Album=$(this).text();
				delete Media#uid#.Tarck;
			break;
			case Media#uid#.Tarck:
				Media#uid#.Tarck=$(this).text();
			break;
		}
		UpdateCmd('media', JSON.stringify(Media#uid#));
		UpdateInforamtion(Media#uid#);
	}
});
function UpdateInforamtion(Media){
	if (Media!=''){
		$('.plex[data-eqLogic_uid=#uid#] .media_Title').html('');
		$.each(Media, function( key, value ) {
			if(key!="Library")
				$('.plex[data-eqLogic_uid=#uid#] .media_Title').append($('<a class="menu_plex">').text(value));
		});
		$.ajax({
			type: "POST",
			timeout:8000, 
			url: "plugins/plex/core/ajax/plex.ajax.php",
			data: {
				Id:'#id#',
				action: "SearchMedia",
				Filtre: 'ByTitle',
				param: JSON.stringify(Media),
			},
			dataType: 'json',
			error: function(request, status, error) {
				handleAjaxError(request, status, error);
			},
			success: function(data) { 
				if (data.state != 'ok') {
					$('#div_alert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				if (data.result!=false){	
					$('.plex[data-eqLogic_uid=#uid#] .tab-content').removeClass('active');	
					switch($('.plex[data-eqLogic_uid=#uid#] .btm-library-plex[data-lib_title="'+Media.Library+'"]').data('lib_type')){
						case 'movie':			
							$('.plex[data-eqLogic_uid=#uid#] .Detail').trigger('click');			
							$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').hide();
							$('.plex[data-eqLogic_uid=#uid#] .Filtre').hide();
							$('.plex[data-eqLogic_uid=#uid#] .Detail').show();
							$('.plex[data-eqLogic_uid=#uid#] .Playback').show();
							$('.plex[data-eqLogic_uid=#uid#] .Cast').show();
							$('.plex[data-eqLogic_uid=#uid#] .Poster').show();
							UpdateDetail(data.result);
						break;
						case 'show':
							if (Media.Season == '' || typeof(Media.Season) == "undefined"){
								UpdateList(data.result);
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').trigger('click');		
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').show();
								$('.plex[data-eqLogic_uid=#uid#] .Filtre').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Detail').show();
								$('.plex[data-eqLogic_uid=#uid#] .Playback').show();
								$('.plex[data-eqLogic_uid=#uid#] .Cast').show();
								$('.plex[data-eqLogic_uid=#uid#] .Poster').show();
							}else if (Media.Episode == '' || typeof(Media.Episode) == "undefined"){
								UpdateList(data.result);
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').trigger('click');			
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').show();
								$('.plex[data-eqLogic_uid=#uid#] .Filtre').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Detail').show();
								$('.plex[data-eqLogic_uid=#uid#] .Playback').show();
								$('.plex[data-eqLogic_uid=#uid#] .Cast').show();
								$('.plex[data-eqLogic_uid=#uid#] .Poster').show();
							}else{
								UpdateDetail(data.result);
								$('.plex[data-eqLogic_uid=#uid#] .Detail').trigger('click');		
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').show();
								$('.plex[data-eqLogic_uid=#uid#] .Filtre').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Detail').show();
								$('.plex[data-eqLogic_uid=#uid#] .Playback').show();
								$('.plex[data-eqLogic_uid=#uid#] .Cast').show();
								$('.plex[data-eqLogic_uid=#uid#] .Poster').show();
							}
						break;
						case 'artist':
							if (Media.Tarck == '' || typeof(Media.Tarck) == "undefined"){
								UpdateList(data.result);
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').trigger('click');			
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').show();
								$('.plex[data-eqLogic_uid=#uid#] .Filtre').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Detail').show();
								$('.plex[data-eqLogic_uid=#uid#] .Playback').show();
								$('.plex[data-eqLogic_uid=#uid#] .Cast').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Poster').show();
							}
							else{
								UpdateDetail(data.result);
								$('.plex[data-eqLogic_uid=#uid#] .Detail').trigger('click');			
								$('.plex[data-eqLogic_uid=#uid#] .ListeMedia').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Filtre').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Detail').show();
								$('.plex[data-eqLogic_uid=#uid#] .Playback').show();
								$('.plex[data-eqLogic_uid=#uid#] .Cast').hide();
								$('.plex[data-eqLogic_uid=#uid#] .Poster').show();
							}
						break;
					}
				}
			}
		});
	}
}
function SecondToDuration(timeCount){	
	var heure=Math.floor(((timeCount/1000)/60)/60);
	var minute=Math.floor(((timeCount/1000)/60))-heure*60;
	var duration=heure+' h '+minute+' min ';
	return duration
}	
$(".plex[data-eqLogic_uid=#uid#] .slider.slider-horizontal").css('z-index',1);
function volume_plex(bouton){
	if(bouton == 'bouton'){
		$(".plex[data-eqLogic_uid=#uid#] .bas_plex_widget_volume").css("display", "block");
		$(".plex[data-eqLogic_uid=#uid#] .bas_plex_widget").css("display", "none");
	}else if(bouton == 'volume'){
		$(".plex[data-eqLogic_uid=#uid#] .bas_plex_widget_volume").css("display", "none");
		$(".plex[data-eqLogic_uid=#uid#] .bas_plex_widget").css("display", "block");
	}
}
function UpdateCmd(logicalId, Valeur){
	$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/plex/core/ajax/plex.ajax.php",
		data: {
			action: "UpdateCommande",
			EqId:'#id#',
			CmdId:logicalId,
			value:Valeur,
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if (data.result!=false)
			{			
			}
		}
	});
}
$('.plex[data-eqLogic_uid=#uid#]').on( 'click','.bt_PlexGroupFiltre', function() {		
	$('.bt_PlexGroupFiltre').removeClass('btn-primary');
	GroupFiltre=$(this).attr("data-filtre");
	$(this).addClass('btn-primary');
});
$('.plex[data-eqLogic_uid=#uid#]').on( 'click','.bt_PlexFilter', function() {		
	$('.plex[data-eqLogic_uid=#uid#] .btn-primary').removeClass('btn-primary');
	Filtre=$(this).attr("data-filtre");
	$(this).addClass('btn-primary');
	switch(Filtre)
	{
		case 'Unwatched':
			$('.plex[data-eqLogic_uid=#uid#] #in_search').hide();
		break;case 'RecentlyReleased':
			$('.plex[data-eqLogic_uid=#uid#] #in_search').hide();
		break;
		case 'RecentlyAdded':
			$('.plex[data-eqLogic_uid=#uid#] #in_search').hide();
		break;
		case 'RecentlyViewed':
			$('.plex[data-eqLogic_uid=#uid#] #in_search').hide();
		break;
		case 'OnDeck':
			$('.plex[data-eqLogic_uid=#uid#] #in_search').hide();
		break;
		case 'UnwatchedShows':
			$('.plex[data-eqLogic_uid=#uid#] #in_search').hide();
		default:
			$('.plex[data-eqLogic_uid=#uid#] #in_search').show();
		break;
	}
});

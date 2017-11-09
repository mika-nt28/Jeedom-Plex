<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<div class="col-sm-6">
	<form class="form-horizontal">
		<legend>Serveur plex</legend>
		<fieldset>
			<table id="table_server" class="table table-bordered table-condensed tablesorter">
				<thead>
					<tr>
						<th>{{Nom}}</th>
						<th>{{Connexion au server}}</th>
						<th>{{}}</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</fieldset>
	</form>
</div>			
<div class="col-sm-6">
	<form class="form-horizontal">
		<legend>Compte PlexPass<a class="btn btn-success btn-xs pull-right cursor" id="bt_AddServer"><i class="fa fa-check"></i> {{Ajouter}}</a></legend>
		<fieldset>
			<div class="form-group">
				<label class="col-lg-2 control-label">{{Nom utilisateur plex.tv}}</label>
				<div class="col-lg-2">
					<input type="text" class="configKey form-control" data-l1key="PlexUser" placeholder="{{Nom utilisateur plex.tv}}"/>
				</div>
				<label class="col-lg-1 control-label">{{Mots de pass utilisateur plex.tv}}</label>
				<div class="col-lg-2">
					<input type="password" class="configKey form-control" data-l1key="PlexPassword" placeholder="{{Token du serveur}}"/>
				</div>
			</div>
		</fieldset>
	</form>
</div>	
<script>	
$.ajax({
	type: "POST",
	timeout:8000,
	url: "core/ajax/config.ajax.php",
	data: {
		action:'getKey',
		key:'{"configuration":""}',
		plugin:'plex'
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
		if (data.result['configuration']!=''){
			var Server= new Object();
			$.each(data.result['configuration'], function(param,valeur){
				switch(typeof(valeur)){
					case 'object':
						$.each(valeur, function(key,value ){
							if (typeof(Server[key]) === 'undefined')
								Server[key]= new Object();
							if (typeof(Server[key]['configuration']) === 'undefined')
								Server[key]['configuration']= new Object();
							Server[key]['configuration'][param]=value;
						});
					break;
					case 'string':
						if (typeof(Server[0]) === 'undefined')
							Server[0]= new Object();
						if (typeof(Server[0]['configuration']) === 'undefined')
							Server[0]['configuration']= new Object();
						Server[0]['configuration'][param]=valeur;
					break;
				}
			});
			$.each(Server, function(id,data){
				AddServer($('#table_server tbody'),data);
			});
		}
	}
});
$('body').on('click','.bt_removecamera', function() {
	$(this).closest('tr').remove();
});
$('body').on('click','#bt_AddServer', function() {
	AddServer($('#table_server tbody'),'');
});
function AddServer(_el,data){
	var id= $('.configKey[data-l1key=configuration][data-l2key=cameraUrl]').length +1;
	var tr=$('<tr>');
	tr.append($('<td>')
		.append($('<input class="configKey form-control input-sm "data-l1key="configuration" data-l2key="name" placeholder="{{Nom du serveur}}">')));
	tr.append($('<td>')
		.append($('<input type="text" class="configKey form-control" data-l1key="configuration" data-l2key="addr" placeholder="{{Adresse ou IP de plex}}"/>'))
		.append($('<input type="text" class="configKey form-control" data-l1key="configuration" data-l2key="port" placeholder="{{Port de plex}}"/>')));
	tr.append($('<td>')
		.append($('<input type="hidden" class="configKey" data-l1key="configuration" data-l2key="id">'))
		.append($('<span class="input-group-btn">')
			.append($('<a class="btn btn-default btn-sm bt_removecamera">')
				.append($('<i class="fa fa-minus-circle">')))));
	_el.append(tr);
	_el.find('tr:last').setValues(data, '.configKey');
	_el.find('tr:last').find('.configKey[data-l1key=configuration][data-l2key=id]').val(id);
} 
</script>

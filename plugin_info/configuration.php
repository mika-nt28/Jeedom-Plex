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
			$.each(data.result['configuration'], function(id,data){
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
	var name="new";
	if(data.length >0)
		name= data.name;
	var tr=$('<tr>');
	tr.append($('<td>')
		.append($('<input class="configKey form-control input-sm "data-l1key="configuration" data-l2key="name" placeholder="{{Nom du serveur}}">')));
	tr.append($('<td>')
		.append($('<input type="text" class="configKey form-control" data-l1key="configuration" data-l2key="'+name+'" data-l3key="address" placeholder="{{Adresse ou IP de plex}}"/>'))
		.append($('<input type="text" class="configKey form-control" data-l1key="configuration" data-l2key="'+name+'" data-l3key="port" placeholder="{{Port de plex}}"/>')));
	tr.append($('<td>')
		.append($('<span class="input-group-btn">')
			.append($('<a class="btn btn-default btn-sm bt_removecamera">')
				.append($('<i class="fa fa-minus-circle">')))));
	_el.append(tr);
	_el.find('tr:last').setValues(data, '.configKey');
	$('.configKey[data-l1key=configuration][data-l2key=name]').off().on('keyup',function() {
		$(this).closest('tr').find('.configKey[data-l1key=configuration][data-l3key=address]').attr('data-l2key',$(this).val());
		$(this).closest('tr').find('.configKey[data-l1key=configuration][data-l3key=port]').attr('data-l2key',$(this).val());
	});
} 
</script>

<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>


<form class="form-horizontal">
<fieldset>
	 <legend><i class="fa fa-wrench"></i>  {{Configuration}}</legend>
		<div class="form-group">
			<label class="col-lg-2 control-label">{{Nom du serveur}}</label>
			<div class="col-lg-2">
				<input type="text" class="configKey form-control" data-l1key="name" placeholder="{{Nom du serveur}}"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label">{{IP}}</label>
			<div class="col-lg-2">
				<input type="text" class="configKey form-control" data-l1key="addr" placeholder="{{Adresse ou IP de plex}}"/>
			</div>
			<label class="col-lg-1 control-label">{{Port}}</label>
			<div class="col-lg-2">
				<input type="text" class="configKey form-control" data-l1key="port" placeholder="{{Port de plex}}"/>
			</div>
		</div>
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

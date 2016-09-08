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
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div id='div_updateplexAlert' style="display: none;"></div>
<a class="btn btn-warning pull-right" data-state="1" id="bt_plexLogStopStart"><i class="fa fa-pause"></i> {{Pause}}</a>
<input class="form-control pull-right" id="in_plexLogSearch" style="width : 300px;" placeholder="{{Rechercher}}" />
<br/><br/><br/>
<pre id='pre_plexupdate' style='overflow: auto; height: 90%;with:90%;'></pre>

<script>
	$.ajax({
		type: 'POST',
		url: 'plugins/plex/core/ajax/plex.ajax.php',
		data: {
			action: 'updateplex',
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error, $('#div_updateplexAlert'));
		},
		success: function () {
			 jeedom.log.autoupdate({
               log : 'plex_update',
               display : $('#pre_plexupdate'),
               search : $('#in_plexLogSearch'),
               control : $('#bt_plexLogStopStart'),
           });
		}
	});
</script>
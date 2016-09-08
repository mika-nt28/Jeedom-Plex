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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
	include_file('core', 'Plex', 'php', 'plex');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
	if (init('action') == 'getLibrary') {
		ajax::success(plex::getLibrary());
    }
	if (init('action') == 'SearchMedia') {
		ajax::success(plex::getMedia(init('Filtre'),init('param')));
	}
	if (init('action') == 'UpdateCommande') {
		$Commande=plex::byId(init('EqId'))->getCmd(null,init('CmdId'));
		if(is_object($Commande)){
			$Commande->setCollectDate('');
			$Commande->event(init('value'));
			$Commande->save();
			ajax::success(true);
		}
		ajax::success(false);
	}
    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
<?php
try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
	include_file('core', 'Plex', 'php', 'plex');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
	if (init('action') == 'getLibrary') {
		$equipement=eqLogic::byType('plex')[0];
		if(is_object($equipement))
			ajax::success($equipement->getLibrary());
		else
			ajax::success(false);
    }
	if (init('action') == 'SearchMedia') {	
		$equipement=eqLogic::byType('plex')[0];
		if(is_object($equipement))
			ajax::success($equipement->getMedia(init('Filtre'),init('param')));
		else
			ajax::success(false);
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

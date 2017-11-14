<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function plex_update() {
    if(isset(config::byKey('name', 'plex')){
        $value[config::byKey('name', 'plex')]=array('address'=>config::byKey('addr', 'plex'),'port'=>config::byKey('port', 'plex'));
        config::save('configuration',$value,'plex');
        config::remove('name', 'plex');
        config::remove('addr', 'plex');
        config::remove('port', 'plex');
    }
    foreach (eqLogic::byType('plex') as $plex) {
        $plex->save();
    }
}
?>

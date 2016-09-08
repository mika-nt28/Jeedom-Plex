<?php
header('Content-Type: image/jpeg');
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
$url=$_REQUEST['url'];
$url.='?X-Plex-Token='.config::byKey('PlexToken', 'plex');

readfile($url);
?>
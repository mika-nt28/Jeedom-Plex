<?php
  class Plex_Server_Session extends Plex_Server{
    const ENDPOINT_STATUS = 'status';
    const ENDPOINT_SESSIONS = 'sessions';
    private ActiveSession=null;
    protected function buildUrl(){
      $url = sprintf(
        '%s/%s',
        $this->getBaseUrl(),
        self::ENDPOINT_STATUS.'/'.
        self::ENDPOINT_SESSIONS
      );
      return $url;
    }
    protected function getActiveSession(){
     $this->ActiveSession = $this->makeCall($this->buildUrl($endpoint));
    }
    /* foreach ($SessionArray as $Session) {			
    foreach ($Session['Player'] as $attribute) {
    if(isset($clients[$attribute['device']]))
    $client=$clients[$attribute['device']];
    else{
    $port = isset($attribute['port']) ? $attribute['port'] : 3000;
    $client = new Plex_Client(
    $attribute['device'],
    $attribute['address'],
    $port
    );
    if(isset($attribute['host']))
    $client->setHost($attribute['host']);
    if(isset($attribute['machineIdentifier']))
    $client->setMachineIdentifier($attribute['machineIdentifier']);
    if(isset($attribute['version']))
    $client->setVersion($attribute['version']);
    $client->setOnlyState(true);
    $client->setServer($this);
    }
    return true;
    }
    }
    return false;
    }*/
    protected function getItems($endpoint){
    $items = array();
    foreach ($this->ActiveSession as $attribute) {
    if (isset($attribute['type'])) {
    $item = Plex_Server_Library_ItemAbstract::factory(
     $attribute['type'],
      $this->name,
      $this->address,
      $this->port
     );
    $item->setAttributes($attribute);
    $items[] = $item;
    }
    }
    return $items;
    }
  }
?>

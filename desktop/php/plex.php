<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}
	$plugin = plugin::byId('plex');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">    
   	<div class="col-xs-12 eqLogicThumbnailDisplay">
  		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
      			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
      				<i class="fas fa-wrench"></i>
    				<br>
    				<span>{{Configuration}}</span>
  			</div>
  		</div>
  		<legend><i class="fas fa-table"></i> {{Mes clients}}</legend>
	   	<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
    		<?php
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
		?>
		</div>
	</div>
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure">
					<i class="fa fa-cogs"></i>
					 {{Configuration avancée}}
				</a>
				<a class="btn btn-default btn-sm eqLogicAction" data-action="copy">
					<i class="fas fa-copy"></i>
					 {{Dupliquer}}
				</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save">
					<i class="fas fa-check-circle"></i>
					 {{Sauvegarder}}
				</a>
				<a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove">
					<i class="fas fa-minus-circle"></i>
					 {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
    			<li role="presentation">
				<a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay">
					<i class="fa fa-arrow-circle-left"></i>
				</a>
			</li>
    			<li role="presentation" class="active">
				<a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab">
				<i class="fa fa-tachometer"></i> 
					{{Equipement}}
				</a>
			</li>
    			<li role="presentation">
				<a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab">
					<i class="fa fa-list-alt"></i> 
					{{Commandes}}
				</a>
			</li>
  		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
      				<br/>
				<div class="col-sm-6">
	    				<form class="form-horizontal">
						<fieldset>
							<div class="form-group ">
	                					<label class="col-sm-3 control-label">
									{{Nom de l'équipement Plex}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Indiquer le nom de votre client}}"></i>
									</sup>
								</label>
								<div class="col-sm-3">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
	                    						<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement template}}"/>
								</div>
							</div>
							<div class="form-group">
	                					<label class="col-sm-3 control-label" >
									{{Objet parent}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Indiquer l'objet dans lequel le widget de cette zone apparaîtra sur le Dashboard}}"></i>
									</sup>
								</label>
								<div class="col-sm-3">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
											foreach (jeeObject::all() as $object) 
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
	                					<label class="col-sm-3 control-label">
									{{Catégorie}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Choisir une catégorie. Cette information n'est pas obligatoire mais peut être utile pour filtrer les widgets}}"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<?php
										foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
											echo '<label class="checkbox-inline">';
											echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
											echo '</label>';
										}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">
									{{Etat du widget}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Choisissez les options de visibilité et d'activation
										Si l'equipement n'est pas activé il ne sera pas utilisable dans jeedom, mais visible sur le dashboard
										Si l'equipement n'est pas visible il ne sera caché sur le dashbord, mais utilisable dans jeedom}}"></i>
									</sup>
								</label>
								<div class="col-sm-9">
									<label class="checkbox-inline">
										<input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
										{{Activer}}
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
										{{Visible}}
									</label>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div class="col-sm-6">
					<form class="form-horizontal">
						<legend>Paramètre du clients</legend>
						<fieldset>
	       						<div class="form-group">
	        						<label class="col-sm-3 control-label">
									{{Heartbeat}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Permet de vérifier toutes les minutes si le plex est toujours actif. Si il n'est plus actif, cela le mettra comme arrêté dans Jeedom. Utile pour les plexs qui sont sur des machines qui s'éteignent sans forcément arrêter plex}}"></i>
									</sup>
								</label>
							        <div class="col-sm-3">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="heartbeat" checked/>
							        </div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">
									{{Choisir le client}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Permet de choisir un client reconnue par le systeme. Attention les clients doivent etres actif pour etre reconnue}}"></i>
									</sup>
								</label>
								<div class="col-sm-8">
									<input type="hidden" class="eqLogicAttr form-control" data-l1key="logicalId"/>
									<?php	
										if(count($eqLogics)>0){
											foreach($eqLogics[0]->getClients() as $Client){
												if ($Client->getName()!='')
													echo '<a class="btn btn-default bt_PlexClient">'.$Client->getName().'</a>';
											}
										}
									?>
								</div>
							</div>
							<div class="form-group">								
								<label class="col-sm-3 control-label">
									{{Changer IP}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Dans le cas ou le client est sur la meme machine que le serveur, alors jeedom recevera une ip local. Ici nous pouvons luis saisir sont ip reseau}}"></i>
									</sup>
								</label>
								<div class="col-sm-8">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="HostUpdate" placeholder="{{Modifier son address}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">
									{{Volume +/-}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Permet de choisir un increment du volume}}"></i>
									</sup>
								</label>
								<div class="col-sm-8">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="volume_inc" placeholder="{{Volume incrément}}"/>
								</div>
							</div>						
						</fieldset> 
					</form>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">	
				<!--a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;">
					<i class="fa fa-plus-circle"></i> 
					{{Commandes}}
				</a-->
				<br/><br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>Nom</th>
							<th>Type</th>
							<th>Options</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>	
		</div>
	</div>
</div>

<?php 
include_file('desktop', 'plex', 'js', 'plex');
include_file('core', 'plugin.template', 'js'); 
?>

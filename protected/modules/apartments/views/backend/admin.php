<?php

// for modal applay paid service
if(issetModule('paidservices')){
	$cs = Yii::app()->clientScript;
	$cs->registerCoreScript('jquery.ui');
	$cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
	$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
}

$this->breadcrumbs=array(
	tt('Manage apartments'),
);

$this->menu = array(
	array('label'=>tt('Add apartment'), 'url'=>array('create')),
);
$this->adminTitle = tt('Manage apartments');

if(Yii::app()->user->hasFlash('mesIecsv')){
	echo "<div class='flash-success'>".Yii::app()->user->getFlash('mesIecsv')."</div>";
}

if (param('useUserads', 1)) {
	Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('editable_select', "
		function ajaxSetModerationStatus(elem, id, id_elem, owner_id, items){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("activate")."', {
				data   : items,
				type   : 'select',
				cancel : '".tc('Cancel')."',
				submit : '".tc('Ok')."',
				style  : 'inherit',
				submitdata : function() {
					return {id : id_elem};
				}
			});
		}
	",
	CClientScript::POS_HEAD);

}

$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '2',
		'htmlOptions' => array(
			'class'=>'center',
		),
	),
	array(
		'name' => 'id',
		'htmlOptions' => array(
			'class'=>'apartments_id_column',
		),
		'sortable' => false,
	),
	array(
		'name' => 'active',
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnControllerStatusHtml($data, "apartments-grid", 1)',
		'htmlOptions' => array(
			//'style' => 'width: 150px;',
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Apartment::getModerationStatusArray(),
	),
	array(
		'name' => 'owner_active',
		'type' => 'raw',
		'value' => 'Apartment::getApartmentsStatus($data->owner_active)',
		'htmlOptions' => array(
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Apartment::getApartmentsStatusArray(),
	),
	array(
		'name' => 'type',
		'type' => 'raw',
		'value' => 'Apartment::getNameByType($data->type)',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => Apartment::getTypesArray(),//CHtml::dropDownList('Apartment[type_filter]', $currentType, Apartment::getTypesArray(true)),
		'sortable' => false,
	),
	array(
		'name' => 'obj_type_id',
		'type' => 'raw',
		'value' => '(isset($data->objType) && $data->objType) ? $data->objType->name : ""',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => Apartment::getObjTypesArray(),
		'sortable' => false,
	),
);
if (issetModule('location') && param('useLocation', 1)) {
	$columns[]=array(
		'name' => 'loc_country',
		'value' => '($data->loc_country && isset($data->locCountry)) ? $data->locCountry->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => Country::getCountriesArray(0, 1),
	);
	$columns[]=array(
		'name' => 'loc_region',
		'value' => '($data->loc_region && isset($data->locRegion)) ? $data->locRegion->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => Region::getRegionsArray($model->loc_country, 0, 1),
	);
	$columns[]=array(
		'name' => 'loc_city',
		'value' => '($data->loc_city && isset($data->locCity)) ? $data->locCity->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => City::getCitiesArray($model->loc_region, 0, 1),
	);
} else {
	$columns[]=array(
		'name' => 'city_id',
		'value' => '($data->city_id && isset($data->city)) ? $data->city->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => ApartmentCity::getAllCity(),
	);
}

$columns[]=array(
	'name' => 'ownerEmail',
	'htmlOptions' => array(
		'style' => 'width: 150px;',
	),
	'type' => 'raw',
	'value' => '(isset($data->user) && $data->user->id != 1) ? CHtml::link(CHtml::encode($data->user->email), array("/users/backend/main/view","id" => $data->user->id)) : tt("administrator", "common")',
);

//$columns[]=array(
//    'name' => 'ownerUsername',
//    'htmlOptions' => array(
//        'style' => 'width: 150px;',
//    ),
//    'value' => 'isset($data->user->username) ? $data->user->username : ""'
//);


$columns[]=array(
	'header' => tc('Name'),
	'name' => 'title_'.Yii::app()->language,
	'type' => 'raw',
	'value' => 'CHtml::encode($data->{"title_".Yii::app()->language})',
	'sortable' => false,
);

if(issetModule('paidservices')){
	$columns[] = array(
		'header'=>tc('Paid services'),
		'value'=>'$data->getPaidHtml(true, true)',
		'type'=>'raw',
		'htmlOptions' => array(
			'style' => 'width: 200px;',
		),
	);
}

$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',

	'template'=>'{up}{down}{view}{update}{delete}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'htmlOptions' => array('class'=>'width120'),
	'buttons' => array(
		'view' => array(
			'url'=>'$data->getUrl()',
			'options'=>array('target'=>'_blank'),
		),
		'up' => array(
			'label' => tc('Move an item up'),
			'imageUrl' => $url = Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
			),
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid" => "0"))',
			'options' => array('class'=>'infopages_arrow_image_up'),

			'visible' => '$data->sorter < "'.$maxSorter.'"',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
		'down' => array(
			'label' => tc('Move an item down'),
			'imageUrl' => $url = Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
			),
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid" => "0"))',
			'options' => array('class'=>'infopages_arrow_image_down'),
			'visible' => '$data->sorter > "'.$minSorter.'"',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
	),
);

$this->widget('CustomGridView', array(
	'id'=>'apartments-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable();}',
	'rowCssClassExpression'=>'"items[]_{$data->id}"',
	'columns'=>$columns
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartments/backend/main/itemsSelected',
	'id' => 'apartments-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));
?>

<?php

$csrf_token_name = Yii::app()->request->csrfTokenName;
$csrf_token = Yii::app()->request->csrfToken;

$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');

$str_js = "
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		function reInstallSortable(id, data) {
			installSortable();
		}

		function updateGrid() {
			$.fn.yiiGridView.update('apartments-grid');
		}

		function installSortable() {
			$('#apartments-grid table.items tbody').sortable({
				forcePlaceholderSize: true,
				forceHelperSize: true,
				items: 'tr',
				update : function () {
					serial = $('#apartments-grid table.items tbody').sortable('serialize', {key: 'items[]', attribute: 'class'}) + '&{$csrf_token_name}={$csrf_token}';
					$.ajax({
						'url': '" . $this->createUrl('/apartments/backend/main/sortitems') . "',
						'type': 'post',
						'data': serial,
						'success': function(data){
							updateGrid();
						},
						'error': function(request, status, error){
							alert('We are unable to set the sort order at this time.  Please try again in a few minutes.');
						}
					});
				},
				helper: fixHelper
			}).disableSelection();
		}

		installSortable();
";

$cs->registerScript('sortable-project', $str_js);
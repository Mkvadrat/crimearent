<?php
$this->breadcrumbs=array(
	tt('Manage themes'),
);

$this->adminTitle = tt('Manage themes');

$this->widget('CustomGridView', array(
	'id'=>'themes-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>array(
		array(
			'name' => 'is_default',
			'type' => 'raw',
			'value' => '$data->getIsDefaultHtml()',
			'filter' => false,
			'sortable' => false,
			'htmlOptions' => array(
				'class'=>'width100 center',
			),
		),
		array(
			'name' => 'title',
			'filter' => false,
			'sortable' => false,
		)
	),
));

Yii::app()->clientScript->registerScript('setDefThemes', "
	function changeDefault(id){
		$.ajax({
			type: 'POST',
			url: '".Yii::app()->request->baseUrl."/themes/backend/main/setDefault',
			data: { 'id' : id },
			success: function(msg){
				$('#currency-grid').yiiGridView.update('themes-grid');
			}
		});
		return;
	}",
	CClientScript::POS_END);
?>
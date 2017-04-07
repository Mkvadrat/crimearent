<?php

// for modal applay paid service
if(issetModule('paidservices')){
    $cs = Yii::app()->clientScript;
    $cs->registerCoreScript('jquery.ui');
    $cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
    $cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
}

$this->breadcrumbs=array(
    tt('Manage subscriptions'),
);

$this->adminTitle = tt('Manage subscriptions');

if(Yii::app()->user->hasFlash('mesIecsv')){
    echo "<div class='flash-success'>".Yii::app()->user->getFlash('mesIecsv')."</div>";
}

$columns = array(
    /*
    array(
        'class'=>'CCheckBoxColumn',
        'id'=>'itemsSelected',
        'selectableRows' => '2',
        'htmlOptions' => array(
            'class'=>'center',
        ),
    ),
    */
    array(
        'name' => 'id',
        'htmlOptions' => array(
            'class'=>'apartments_id_column',
        ),
        'sortable' => false,
    )
);

$columns[]=array(
    'header' => tc('Name'),
    'name' => 'name',
    'type' => 'raw',
    'value' => 'CHtml::encode($data->name)',
    'sortable' => false,
);

$columns[]=array(
    'header' => tc('Email'),
    'name' => 'email',
    'type' => 'raw',
    'value' => 'CHtml::encode($data->email)',
    'sortable' => false,
);

$columns[]=array(
    'header' => tc('Phone'),
    'name' => 'phone',
    'type' => 'raw',
    'value' => 'CHtml::encode($data->phone)',
    'sortable' => false,
);

$columns[]=array(
    'header' => tc('IP'),
    'name' => 'ip',
    'type' => 'raw',
    'value' => 'CHtml::encode($data->ip)',
    'sortable' => false,
);

$columns[]=array(
    'header' => tc('Date'),
    'name' => 'date_added',
    'type' => 'raw',
    'value' => 'CHtml::encode($data->date_added)',
    'sortable' => false,
);

$columns[] = array(
    'class'=>'bootstrap.widgets.TbButtonColumn',
    'template'=>'{delete}',
    'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
    'htmlOptions' => array('class'=>'width120'),
    'buttons' => array(
        'delete' => array(
            'url'=>'"/apartments/backend/main/subscriptionremove?id=".$data->id',
            'options'=>array('target'=>'_blank'),
        ),
    ),
);

$this->widget('CustomGridView', array(
    'id'=>'subscriptions-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable();}',
    'rowCssClassExpression'=>'"items[]_{$data->id}"',
    'columns'=>$columns
));

?>

<?php

$csrf_token_name = Yii::app()->request->csrfTokenName;
$csrf_token = Yii::app()->request->csrfToken;

$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');

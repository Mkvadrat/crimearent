<?php
Yii::app()->params['ignoreSlider'] = true;

$isInner=false;
$divClass = 'header-form-line';

$invisible_fields = array(
    'berths_max',
    'with_photo',
    'term',
    'ap_type',
    'square',
    'floor',
    'by_id',
    'owner_type'
);

$visible_fields = array(
    'location',
    'obj_type',
    'subtype',
    'price',
    'sea',
    'rooms',
    'berths_max_with_photo'
);

$i = 1;

foreach($visible_fields as $field) {
    if (!Apartment::ENABLE_SUBTYPE && $field == 'subtype') continue;
    if (in_array($field, $invisible_fields)) continue;

    $this->renderPartial('//site/_search_field_'.$field, array(
        'divClass' => $divClass,
        'textClass' => 'width-auto',
        'fieldClass' => 'width120 search-input-new',
        'minWidth' => '120',
        'isInner' => $isInner,
    ));

    $i++;

    SearchForm::increaseJsCounter();
}


$searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => SearchFormModel::OBJ_TYPE_ID_DEFAULT), array('group' => 'field'));


foreach($searchFields as $search){
    if (in_array($search->field, $invisible_fields)) continue;
    if (in_array($search->field, $visible_fields)) continue;

    if($search->status <= SearchFormModel::STATUS_NOT_REMOVE){
        $this->renderPartial('//site/_search_field_' . $search->field, array(
            'divClass' => $divClass,
            'textClass' => 'width-auto',
            'fieldClass' => 'width120 search-input-new',
            'minWidth' => '120',
            'isInner' => $isInner,
        ));
    } else {
        $this->renderPartial('//site/_search_new_field', array(
            'divClass' => $divClass,
            'textClass' => 'width-auto',
            'fieldClass' => 'width120 search-input-new',
            'minWidth' => '120',
            'search' => $search,
            'isInner' => $isInner,
        ));
    }

    $i++;

    SearchForm::increaseJsCounter();
}

echo CHtml::hiddenField('type', Apartment::TYPE_RENT);
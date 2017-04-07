<?php
Yii::app()->params['ignoreSlider'] = true;

$isInner=true;
$divClass = 'small-header-form-line inner-search-item';

$invisible_fields = array(
    'with_photo',
    'term',
    'ap_type'
);

$visible_fields = array(
    'location',
    'obj_type',
    'rooms',
    'berths_max',
    'subtype',
    'price',
    'sea',
    'square',
    'floor',
    'with_photo_more'
);

$extra_fields = array(
    'street'
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

echo '<div id="inner-search-hidden-fields">';

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

foreach($extra_fields as $field) {
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

echo '</div>';
echo CHtml::hiddenField('type', isset($this->type) ? intval($this->type) : Apartment::TYPE_RENT);
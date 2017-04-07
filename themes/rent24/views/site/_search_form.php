<?php
$compact = isset($compact) ? $compact : 0;
$isInner = isset($isInner) ? $isInner : 0;

if(isset($this->objType) && $this->objType){
    $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => $this->objType), array('group' => 'field'));
    if(!$searchFields){
        $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => SearchFormModel::OBJ_TYPE_ID_DEFAULT), array('group' => 'field'));
    }
} else {
    $searchFields = SearchFormModel::model()->sort()->findAllByAttributes(array('obj_type_id' => SearchFormModel::OBJ_TYPE_ID_DEFAULT), array('group' => 'field'));
}

$i = 1;
foreach($searchFields as $search){
    if ($search->field == 'term') continue;
    if ($search->field == 'ap_type') continue;

    if($isInner){
        $divClass = 'small-header-form-line inner-search-item';
    }else{
        $divClass = 'header-form-line';
    }

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
?>

<div class="<?php echo $divClass; ?>">
    <span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo tt('Rent Type', 'apartments'); ?>:</div> </span>
    <span class="search float-r">
    <?php
    echo CHtml::dropDownList(
        'subtype',
        isset($this->subtype) ? $this->subtype : 0,
            Apartment::getSubTypesArray(),
        array('class' => $fieldClass . ' searchField'.' filter-select')
    );
    Yii::app()->clientScript->registerScript('subtype', '
		focusSubmit($("select#subtype"));
	', CClientScript::POS_READY);
    ?>
    </span>
</div>
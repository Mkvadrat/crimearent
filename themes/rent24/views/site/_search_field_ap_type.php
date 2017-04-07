<div class="<?php echo $divClass; ?>">
    <span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo tt('Search in section', 'common'); ?>:</div></span>
	<span class="search float-r">
		<?php
        $data = SearchForm::apTypes();

        echo CHtml::dropDownList(
            'apType',
            isset($this->apType) ? CHtml::encode($this->apType) : '',
            $data['propertyType'],
            array('class' => $fieldClass . ' searchField'.' filter-select')
        );

        Yii::app()->clientScript->registerScript('currency-name-init', '
				focusSubmit($("select#apType"));
			', CClientScript::POS_READY);
        ?>
	</span>
</div>

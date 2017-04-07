<div class="<?php echo $divClass; ?>">
	<!--<span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Apartment ID'); ?>:</div> </span>-->
    <span class="search float-l"><div class="<?php echo $textClass; ?>">№ объекта:</div> </span>
	<span class="search float-r">
    <?php
	echo CHtml::textField('sApId', (isset($this->sApId) && $this->sApId) ? CHtml::encode($this->sApId) : '', array(
        'class' => 'width115 search-input-new cell-input-big',
        'onChange' => 'changeSearch();',
    ));
	Yii::app()->clientScript->registerScript('sApId', '
		focusSubmit($("input#sApId"));
	', CClientScript::POS_READY);
	?>
     </span>
</div>
<div class="<?php echo $divClass; ?>">
    <span class="search float-l"><div class="<?php echo $textClass; ?>">Количество мест:</div> </span>
	<span class="search float-r">
    <?php
    echo CHtml::textField('berths_max', (isset($this->berths_max) && $this->berths_max) ? CHtml::encode($this->berths_max) : '', array(
        'class' => 'width115 search-input-new cell-input',
        'onChange' => 'changeSearch();',
    ));
    Yii::app()->clientScript->registerScript('berths_max', '
		focusSubmit($("input#berths_max"));
	', CClientScript::POS_READY);
    ?>
     </span>
</div>
<div class="<?php echo $divClass; ?>">
    <span class="search float-l"><div class="<?php echo $textClass; ?>">Улица, переулок:</div> </span>
	<span class="search float-r">
    <?php
    echo CHtml::textField('street', (isset($this->street) && $this->street) ? CHtml::encode($this->street) : '', array(
        'class' => 'width115 search-input-new cell-input-big',
        'onChange' => 'changeSearch();',
    ));
    Yii::app()->clientScript->registerScript('street', '
		focusSubmit($("input#street"));
	', CClientScript::POS_READY);
    ?>
     </span>
</div>
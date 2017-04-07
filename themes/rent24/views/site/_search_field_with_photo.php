<div class="<?php echo $divClass; ?>">
	<span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Only with photo'); ?>:</div> </span>
	<span class="search float-r">
    <?php
        /**
		echo CHtml::checkBox('wp', (isset($this->wp) && $this->wp) ? CHtml::encode($this->wp) : '', array(
			'class' => 'search-input-new',
			'id' => 'search_with_photo'
		));
        **/
	?>
        <div class="buttonset">
            <input type="checkbox" name="wp" id="search_with_photo" <?php if (isset($this->wp) && $this->wp) echo 'checked="checked"' ?> value="1" />
            <label for="search_with_photo" class="checkbox-image"></label>
        </div>
    </span>
</div>

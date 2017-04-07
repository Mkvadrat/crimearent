<div class="<?php echo $divClass; ?> slider-filter-big-row">
    <span class="search float-l">Количество мест:</span>
    <div class="slider-input slider-input-wide float-r">
        <div class="slider-filter-cell-inline">
            <?php
                echo CHtml::textField('berths_max', (isset($this->berths_max) && $this->berths_max) ? CHtml::encode($this->berths_max) : '', array(
                    'class' => 'cell-input',
                    'onChange' => 'changeSearch();',
                    'maxlength' => 1
                ));
                Yii::app()->clientScript->registerScript('berths_max', '
                    focusSubmit($("input#berths_max"));
                ', CClientScript::POS_READY);
            ?>
        </div>
        <div class="slider-filter-cell-inline">
            <div class="buttonset">
                <input type="checkbox" name="wp" id="search_with_photo" <?php if (isset($this->wp) && $this->wp) echo 'checked="checked"' ?> value="1" />
                <label for="search_with_photo" class="checkbox-image"></label>
            </div>
        </div>
        <div class="slider-filter-cell-inline">
            <span class="grey-text">только с фото</span>
        </div>
    </div>
</div>
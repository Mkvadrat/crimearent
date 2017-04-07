<?php
$extraClass = '';
if (!$isInner && !param('ignoreSlider')) $extraClass = ' slider-filter-big-row';
?>

<div class="<?php echo $divClass.$extraClass; ?>">
    <?php
    if (issetModule('selecttoslider') && param('useFloorSlider') == 1 && !param('ignoreSlider')) {
    ?>
    <span class="search float-l">
        <div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Floor range'); ?>:</div>
    </span>
    <span class="search float-r">
        <div class="right-suffix grey-text">эт.</div>
        <?php
        $floorItems = array_merge(
            range(0, param('moduleApartments_maxFloor', 30))
        );
        $floorMin = isset($this->floorCountMin) ? CHtml::encode($this->floorCountMin) : 0;
        $floorMax = isset($this->floorCountMax) ? CHtml::encode($this->floorCountMax) : max($floorItems);

        SearchForm::renderSliderRange(array(
            'field' => 'floor',
            'min' => 0,
            'max' => param('moduleApartments_maxFloor', 30),
            'min_sel' => $floorMin,
            'max_sel' => $floorMax,
            'step' => 1,
            'class' => 'floor-search-select',
        ));

    echo '</span>';
        } else if (param('ignoreSlider')) {
            $floorItems = array_merge(
                range(0, param('moduleApartments_maxFloor', 30))
            );
            $floorMin = isset($this->floorCountMin) ? CHtml::encode($this->floorCountMin) : 0;
            $floorMax = isset($this->floorCountMax) ? CHtml::encode($this->floorCountMax) : max($floorItems);

            ?>
                <span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Floor range'); ?>:</div> </span>
                <span class="search float-r">
                    <div class="right-suffix grey-text right-suffix-small">э.</div>
                    <input type="text" id="floor_min" name="floor_min" class="search-input-new cell-input cell-input-inline" value="<?php echo isset($floorMin) ? CHtml::encode($floorMin) : ""; ?>"/>
                    <span class="inline-mdash">&mdash;</span>
                    <input type="text" id="floor_max" name="floor_max" class="search-input-new cell-input cell-input-inline" value="<?php echo isset($floorMax) ? CHtml::encode($floorMax) : ""; ?>"/>
                </span>
            <?php
        } else {
            ?>
            <span class="search"><div
                    class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Flat on floor'); ?>:
                </div> </span>
            <?php
            $floorItems = array_merge(
                array(0 => tc('Any')),
                range(1, param('moduleApartments_maxFloor', 30))
            );

            echo CHtml::dropDownList('floor', isset($this->floorCount) ? CHtml::encode($this->floorCount) : 0, $floorItems, array('class' => $fieldClass . ' searchField'));

            Yii::app()->clientScript->registerScript('floor', '
        focusSubmit($("select#floor"));
    ', CClientScript::POS_READY);
        }
        ?>
</div>

    <?php if (!param('ignoreSlider')) { ?>
    <div class="<?php echo $divClass; ?> slider-filter-big-row">
    <span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo tt('Sea distance', 'apartments'); ?>:</div> </span>
		<span class="search float-r">
            <div class="right-suffix grey-text">м.</div>
            <?php
            if(isset($this->objType) && $this->objType){
                $seaAll = Apartment::getSeaDistanceMinMax($this->objType);
            }else{
                $seaAll = Apartment::getSeaDistanceMinMax(1, true);
            }

            $seaAll['sea_min'] = isset($seaAll['sea_min']) ? $seaAll['sea_min'] : 0;
            $seaAll['sea_max'] = isset($seaAll['sea_max']) ? $seaAll['sea_max'] : 1000;

            $diff = $seaAll['sea_max'] - $seaAll['sea_min'];
            $step = SearchForm::getSliderStep($diff);

            $seaMinSel = (isset($this->seaSlider) && isset($this->seaSlider["min"]) && $this->seaSlider["min"] >= $seaAll["sea_min"] && $this->seaSlider["min"] <= $seaAll["sea_max"])
                ? $this->seaSlider["min"] : $seaAll["sea_min"];
            $seaMaxSel = (isset($this->seaSlider) && isset($this->seaSlider["max"]) && $this->seaSlider["max"] <= $seaAll["sea_max"] && $this->seaSlider["max"] >= $seaAll["sea_min"])
                ? $this->seaSlider["max"] : $seaAll["sea_max"];

            SearchForm::renderSliderRange(array(
                'field' => 'sea',
                'min' => $seaAll['sea_min'],
                'max' => $seaAll['sea_max'],
                'min_sel' => $seaMinSel,
                'max_sel' => $seaMaxSel,
                'step' => $step,
                'class' => 'sea-search-select',
            ));

            echo '</span>';
            echo '</div>';
       } else if (param('ignoreSlider')) {
                if(isset($this->objType) && $this->objType){
                    $seaAll = Apartment::getSeaDistanceMinMax($this->objType);
                }else{
                    $seaAll = Apartment::getSeaDistanceMinMax(1, true);
                }

                $seaAll['sea_min'] = isset($seaAll['sea_min']) ? $seaAll['sea_min'] : 0;
                $seaAll['sea_max'] = isset($seaAll['sea_max']) ? $seaAll['sea_max'] : 1000;

                $diff = $seaAll['sea_max'] - $seaAll['sea_min'];
                $step = SearchForm::getSliderStep($diff);

                $seaMinSel = (isset($this->seaSlider) && isset($this->seaSlider["min"]) && $this->seaSlider["min"] >= $seaAll["sea_min"] && $this->seaSlider["min"] <= $seaAll["sea_max"])
                    ? $this->seaSlider["min"] : $seaAll["sea_min"];
                $seaMaxSel = (isset($this->seaSlider) && isset($this->seaSlider["max"]) && $this->seaSlider["max"] <= $seaAll["sea_max"] && $this->seaSlider["max"] >= $seaAll["sea_min"])
                    ? $this->seaSlider["max"] : $seaAll["sea_max"];

                ?>
                <div class="<?php echo $divClass; ?>">
                <span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo tt('Sea distance', 'apartments'); ?>:</div> </span>
                <span class="search float-r">
                    <div class="right-suffix grey-text right-suffix-small">м.</div>
                    <input type="text" id="sea_min" name="sea_min" class="search-input-new cell-input cell-input-inline" value="<?php echo isset($seaMinSel) ? CHtml::encode(round($seaMinSel)) : ""; ?>"/>
                    <span class="inline-mdash">&mdash;</span>
                    <input type="text" id="sea_max" name="sea_max" class="search-input-new cell-input cell-input-inline" value="<?php echo isset($seaMaxSel) ? CHtml::encode(round($seaMaxSel)) : ""; ?>"/>
                </span>
                </div>
            <?php
        }
        ?>

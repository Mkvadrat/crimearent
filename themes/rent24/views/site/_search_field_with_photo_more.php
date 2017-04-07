<div class="<?php echo $divClass ?>">
    <div class="slider-filter-cell-inline">
        <div class="buttonset">
            <input type="checkbox" name="wp" value="1" id="filter-photo" <?php if (isset($this->wp) && $this->wp) echo 'checked="checked"' ?> />
            <label for="filter-photo" class="checkbox-image"></label>
        </div>
    </div>
    <div class="slider-filter-cell-inline">
        <span class="grey-text">только с фото</span>
    </div>
    <label class="float-r"><a href="javascript:void(0)" id="more-link-inner">Больше параметров</a></label>
</div>
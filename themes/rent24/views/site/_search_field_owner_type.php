<div class="<?php echo $divClass; ?>">
    <span class="search float-l"><div class="<?php echo $textClass; ?>"><?php echo tt('Listing from', 'common'); ?>:</div></span>
	<span class="search float-r">
		<?php
        $list = array(
            1 => tc('Private person'),
            2 => tc('Company'),
        );

        echo CHtml::dropDownList(
            'ot',
            isset($this->ot) ? CHtml::encode($this->ot) : '',
            $list,
            array(
                'empty' => tt('All', 'common'),
                'class' => $fieldClass . ' searchField'.' filter-select'
            )
        );

        ?>
	</span>
</div>

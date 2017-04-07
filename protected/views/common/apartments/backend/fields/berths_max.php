<?php if($model->canShowInForm('berths_max')){ ?>
    <div class="rowold">
        <?php echo CHtml::activeLabelEx($model, 'berths_max'); ?>
        <?php echo Apartment::getTip('berths_max');?>
        <?php echo CHtml::activeTextField($model, 'berths_max', array('class' => 'width150', 'maxlength' => 4)); ?>
        <?php echo CHtml::error($model, 'berths_max'); ?>
    </div>
<?php } ?>
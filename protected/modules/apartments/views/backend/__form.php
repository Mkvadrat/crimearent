<?php $this->renderPartial('//../modules/apartments/views/backend/__form_general', array('model' => $model, 'form' => $form));?>

<div class="tab-pane" id="tab-extended">
	<?php
	if ($model->is_free_to == '0000-00-00') {
		$model->is_free_to = '';
	}

    if (Yii::app()->user->getState('isAdmin')) { ?>
	<div class="rowold">
		<?php echo $form->checkboxRow($model, 'is_special_offer'); ?>
	</div>
	<?php
    }

    if (Yii::app()->user->getState('isAdmin')) { ?>
	<div class="special-calendar">
		<?php echo $form->labelEx($model, 'is_free_to', array('class' => 'noblock')); ?><br/>
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'is_free_to',
			'language' => Yii::app()->language,

			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'minDate' => 'new Date()',
			),
			'htmlOptions' => array(
				'class' => 'width100 eval_period'
			),
		));
		?>
		<?php echo $form->error($model, 'is_free_to'); ?>
	</div>

	<?php
    }

	if (!isset($element)) {
		$element = 0;
	}

	if (issetModule('bookingcalendar') && $model->active != Apartment::STATUS_DRAFT) {
		$this->renderPartial('//modules/bookingcalendar/views/_form', array('apartment' => $model, 'element' => $element));
	}

    $rows = HFormEditor::getExtendedFields();
    HFormEditor::renderFormRows($rows, $model);

    ?>

</div>

	<?php

	/*if ($model->isNewRecord) {
		echo '<p>' . tt('After pressing the button "Create", you will be able to load photos for the listing and to mark the property on the map.', 'apartments') . '</p>';
	}*/

	if (Yii::app()->user->getState('isAdmin')) {
		$this->widget('bootstrap.widgets.TbButton',
			array('buttonType' => 'submit',
				'type' => 'primary',
				'icon' => 'ok white',
				'label' => $model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'),
				'htmlOptions' => array(
					'onclick' => "$('#Apartment-form').submit(); return false;",
				)
			));
	} else {
        echo '<div class="row save">Поля, отмеченные <span class="required">*</span>, являются обязательными для заполнения</div>';
		echo '<div class="row buttons save">';
        echo '<div id="next-submit-wrapper" style="display: inline-block">';
        echo CHtml::button('Далее', array(
            'onclick' => " return false;", 'class' => 'big_button button-blue',
            'style' => 'display: inline-block',
            'id' => 'next-submit-btn'
        ));

        echo '<span style="display: inline-block">&nbsp;или&nbsp;</span>';
        echo '</div>';

		echo CHtml::button($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), array(
			'onclick' => "$('#Apartment-form').submit(); return false;", 'class' => 'big_button button-blue',
            'style' => 'display: inline-block'
		));
		echo '</div>';

?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#next-submit-btn').click(function(){
                    var active = $('#tabs ul.ui-tabs-nav').find('li.ui-state-active');
                    if ($(active).length) {
                        $(active).next('li').children('a').trigger('click');

                        if (!$(active).next('li').next('li').length) {
                            $('#next-submit-wrapper').hide();
                        }

                        $('html, body').animate({'scrollTop': $('#tabs').offset().top}, 1000);
                    }
                });

                $('#tabs').on('tabsactivate',function(){
                    var active = $('#tabs ul.ui-tabs-nav').find('li.ui-state-active');
                    if (!$(active).next('li').length) {
                        $('#next-submit-wrapper').hide();
                    } else {
                        $('#next-submit-wrapper').show();
                    }
                });
            });
        </script>
<?php
	}
?>




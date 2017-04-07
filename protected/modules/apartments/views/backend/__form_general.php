<div class="tab-pane active" id="tab-main">
<div class="rowold">
    <?php $typesArray = Apartment::getTypesArray(); ?>
    <?php if (!Yii::app()->user->getState('isAdmin') && isset($typesArray[5])) unset($typesArray[5]); ?>
    <?php echo $form->labelEx($model, 'type'); ?>
    <?php echo $form->dropDownList($model, 'type', $typesArray, array('class' => 'width240', 'id' => 'ap_type')); ?>
    <?php echo $form->error($model, 'type'); ?>
</div>

<?php if (Apartment::ENABLE_SUBTYPE && ($model->type == Apartment::TYPE_RENT || $model->type == Apartment::TYPE_RENTING)): ?>
<div class="rowold">
    <?php echo $form->labelEx($model, 'rent_type'); ?>
    <?php echo $form->dropDownList($model, 'subtype', Apartment::getSubTypesArray(), array('class' => 'width240', 'id' => 'ap_type')); ?>
    <?php echo $form->error($model, 'subtype'); ?>
</div>
<?php endif; ?>

<div class="rowold">
    <?php echo $form->labelEx($model, 'obj_type_id'); ?>
    <?php echo $form->dropDownList($model, 'obj_type_id', Apartment::getObjTypesArray(), array('class' => 'width240', 'id' => 'obj_type')); ?>
    <?php echo $form->error($model, 'obj_type_id'); ?>
</div>

<?php if (issetModule('location') && param('useLocation', 1)): ?>
    <?php $countries = Country::getCountriesArray();?>
    <div class="rowold">
        <?php echo $form->labelEx($model,'loc_country'); ?>
        <?php echo $form->dropDownList($model,'loc_country',$countries,
            array(
                'id'=>'ap_country',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/location/main/getRegions'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"country="+$("#ap_country").val()',
                    'success'=>'function(result){
                                $("#ap_region").html(result);
                                $("#ap_region").change();
                            }'
                    //leave out the data key to pass all form values through
                ),
                'class' => 'width240'
            )
        ); ?>
        <?php echo $form->error($model,'loc_country'); ?>
    </div>

    <?php
    //при создании города узнаём id первой в дропдауне страны
    if ($model->loc_country) {
        $country = $model->loc_country;
    } else {
        $country_keys = array_keys($countries);
        $country = isset($country_keys[0]) ? $country_keys[0] : 0;
    }

    $regions=Region::getRegionsArray($country);

    if ($model->loc_region) {
        $region = $model->loc_region;
    } else {
        $region_keys = array_keys($regions);
        $region = isset($region_keys[0]) ? $region_keys[0] : 0;
    }

    $cities = City::getCitiesArray($region);

    if ($model->loc_city) {
        $city = $model->loc_city;
    } else {
        $city_keys = array_keys($cities);
        $city = isset($city_keys[0]) ? $city_keys[0] : 0;
    }
    ?>

    <div class="rowold">
        <?php echo $form->labelEx($model,'loc_region'); ?>
        <?php echo $form->dropDownList($model,'loc_region',$regions,
            array('id'=>'ap_region',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/location/main/getCities'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"region="+$("#ap_region").val()',
                    'success'=>'function(result){
								$("#ap_city").html(result);
						}'

                ),
                'class' => 'width240'
            )
        ); ?>
        <?php echo $form->error($model,'loc_region'); ?>
    </div>

    <div class="rowold">
        <?php echo $form->labelEx($model,'loc_city'); ?>
        <?php echo $form->dropDownList($model,'loc_city',$cities,array('id'=>'ap_city', 'class' => 'width240')); ?>
        <?php echo $form->error($model,'loc_city'); ?>
    </div>

<?php else: ?>

    <div class="rowold">
        <?php echo $form->labelEx($model, 'city_id'); ?>
        <?php echo $form->dropDownList($model, 'city_id', Apartment::getCityArray(), array('class' => 'width240')); ?>
        <?php echo $form->error($model, 'city_id'); ?>
    </div>

<?php endif; ?>

<div class="rowold">
    <?php echo $form->labelEx($model, 'sea_distance'); ?>
    <?php echo $form->textField($model, 'sea_distance', array('class' => 'width100')); ?>
    <?php echo 'м.'; ?>
    <?php echo $form->error($model, 'sea_distance'); ?>
</div>

<div class="rowold no-mrg">
    <?php
    echo $form->label($model, 'price', array('required' => true));
    ?>

    <?php echo $form->checkbox($model, 'is_price_poa'); ?>
    <?php echo $form->labelEx($model, 'is_price_poa', array('class' => 'noblock')); ?>
    <?php echo $form->error($model, 'is_price_poa'); ?>

    <div id="price_fields">
        <?php
        echo CHtml::hiddenField('is_update', 0);

//        if (issetModule('currency')) {
//            echo '<div class="padding-bottom10"><small>' . tt('Price will be saved (converted) in the default currency on the site', 'apartments') . ' - ' . Currency::getDefaultCurrencyModel()->name . '</small></div>';
//        }

        if ($model->isPriceFromTo()) {
            echo tc('price_from') . ' ' . $form->textField($model, 'price', array('class' => 'width100 noblock'));
            echo ' ' .tc('price_to') . ' ' . $form->textField($model, 'price_to', array('class' => 'width100'));
        } else {
            echo $form->textField($model, 'price', array('class' => 'width100'));
        }

        if(issetModule('currency')){
            // Даем вводить ценую только в дефолтной валюте
            echo '&nbsp;' . Currency::getDefaultCurrencyName();
            $model->in_currency = Currency::getDefaultCurrencyModel()->char_code;
            echo $form->hiddenField($model, 'in_currency');
            // $form->dropDownList($model, 'in_currency', Currency::getActiveCurrencyArray(2), array('class' => 'width120'))
        } else {
            echo '&nbsp;'.param('siteCurrency', '$');
        }

        if($model->type == Apartment::TYPE_RENT){
            $priceArray = Apartment::getPriceArray($model->type);
            if(!in_array($model->price_type, array_keys($priceArray))){
                $model->price_type = Apartment::PRICE_PER_MONTH;
            }
            echo '&nbsp;'.$form->dropDownList($model, 'price_type', Apartment::getPriceArray($model->type), array('class' => 'width150'));
        }
        ?>
    </div>

    <?php echo $form->error($model, 'price'); ?>
</div>
<div class="clear"></div>
<?php if ($model->type == Apartment::TYPE_RENT) {
?>
<div class="rowold no-mrg">
    <label>Добавить цену за период</label>
    <div id="price_fields">
        <div id="dateprice-wrapper">
        <?php foreach(ApartmentPrice::getApartmentPrices($model->id) as $row): ?>
            <div class="dateprice-item" rel="dateprice-item-<?php echo $row->id ?>"><span>с</span>&nbsp;<span class="dateprice-date"><?php echo ApartmentPrice::returnDate(strtotime($row->date_start)) ?></span>&nbsp;<span>по</span>&nbsp;<span class="dateprice-date"><?php echo ApartmentPrice::returnDate(strtotime($row->date_end)) ?></span>&nbsp;&nbsp;<strong><?php echo $row->price ?></strong> руб.&nbsp;&nbsp;<a href="javascript:void(0)" onclick="removeDatePrice(<?php echo $row->id ?>)">[x]</a></div>
        <?php endforeach; ?>
        </div>
        <span>с</span>
        <input type="text" name="date_start" class="width100" id="datepicker-start" value="<?php echo date('d.m.Y') ?>" />
        <span>по</span>
        <input type="text" name="date_end" class="width100" id="datepicker-end" value="<?php echo date('d.m.Y') ?>" />
        &nbsp;&nbsp;
        <span>цена:</span>
        <input type="text" name="date_price" class="width100" value="0" />
        <span><?php echo param('siteCurrency', '$') ?></span>
        &nbsp;&nbsp;
        <input type="button" value="Добавить" onclick="saveDatePrice()" />
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $.datepicker.regional['ru'] = {
            closeText: 'Закрыть',
            prevText: '&#x3c;Пред',
            nextText: 'След&#x3e;',
            currentText: 'Сегодня',
            monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
            monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                'Июл','Авг','Сен','Окт','Ноя','Дек'],
            dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
            dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
            dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
            dateFormat: 'dd.mm.yy',
            firstDay: 1,
            isRTL: false
        };
        $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );

        $('#datepicker-start').datepicker($.datepicker.regional[ "ru" ]);
        $('#datepicker-end').datepicker($.datepicker.regional[ "ru" ]);
    });

    function saveDatePrice() {
        var date_start = $('input[name=date_start]').val();
        var date_end = $('input[name=date_end]').val();
        var price = $('input[name=date_price]').val();

        if (price.length == 0 || parseInt(price) <= 0) {
            alert('Укажите цену');
            return;
        }
        if (date_start.length == 0 && date_end.length == 0) {
            alert('Выберите дату');
            return;
        }
        if (date_start.length > 0 && !date_start.match(/^[\d]{2}[.][\d]{2}[.][\d]{4}$/)) {
            alert('Неверная дата: '+date_start);
            return;
        }
        if (date_end.length > 0 && !date_end.match(/^[\d]{2}[.][\d]{2}[.][\d]{4}$/)) {
            alert('Неверная дата: '+date_end);
            return;
        }

        $.post(
            '/apartments/main/datepriceadd',
            {
                'apartment_id': <?php echo $model->id ?>,
                'date_start': date_start,
                'date_end': date_end,
                'price': price
            },
            function(response){
                if (response) {
                    if (response.error.length > 0) {
                        alert(response.error);
                    } else if (response.data) {
                        $('#dateprice-wrapper').append('<div class="dateprice-item" rel="dateprice-item-'+response.data.id+'"><span>с</span>&nbsp;<span class="dateprice-date">'+response.data.date_start+'</span>&nbsp;<span>по</span>&nbsp;<span class="dateprice-date">'+response.data.date_end+'</span>&nbsp;&nbsp;<strong>'+response.data.price+'</strong> <?php echo param('siteCurrency', '$') ?>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="removeDatePrice('+response.data.id+')">[x]</a></div>');
                    }
                }
            },
            'json'
        );
    }

    function removeDatePrice(id) {
        $.post(
            '/apartments/main/datepriceremove',
            {
                'apartment_id': <?php echo $model->id ?>,
                'id': id
            },
            function(response){
                if (response) {
                    if (response.error.length > 0) {
                        alert(response.error);
                    } else {
                        $('div[rel=dateprice-item-'+id+']').remove();
                    }
                }
            },
            'json'
        );

    }
</script>
<?php
}
?>
<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
    'model' => $model,
    'field' => 'title',
    'type' => 'string'
));

echo '<br/>';

if ($model->type == Apartment::TYPE_CHANGE) {
    echo '<div class="clear">&nbsp;</div>';
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'exchange_to',
        'type' => 'text'
    ));
}

$rows = HFormEditor::getGeneralFields();
$_rows = array();
foreach ($rows as $row) {
    if (!Yii::app()->user->getState('isAdmin') && $row['field']=='square') continue;
    if (!Yii::app()->user->getState('isAdmin') && $row['field']=='window_to') continue;
    $_rows[] = $row;
}
HFormEditor::renderFormRows($_rows, $model);

$canSet = $model->canSetPeriodActivity() ? 1 : 0;

echo '<div class="rowold" id="set_period" ' . ( !$canSet ? 'style="display: none;"' : '' ) . '>';
echo $form->labelEx($model, 'period_activity');
echo $form->dropDownList($model, 'period_activity', Apartment::getPeriodActivityList());
echo CHtml::hiddenField('set_period_activity', $canSet);
echo $form->error($model, 'period_activity');
echo '</div>';

if(!$canSet) {
    echo '<div id="date_end_activity"><b>'.Yii::t('common', 'The listing will be active till {DATE}', array('{DATE}' => $model->getDateEndActivityLongFormat())).'</b>';
    echo '&nbsp;' . CHtml::link(tc('Change'), 'javascript:;', array(
            'onclick' => '$("#date_end_activity").hide(); $("#set_period_activity").val(1); $("#set_period").show();',
        ));
    echo '</div>';
}

?>

<?php if(Yii::app()->user->getState('isAdmin') && $model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) : ?>
<div class="rowold" style="padding-bottom: 10px">
    <?php echo CHtml::label(tt('Show in slider'),'slider_position'); ?>
    <?php echo CHtml::dropDownList('slider_position', ApartmentBanner::getApartmentBannerPosition($model->id), ApartmentBanner::getSliderPositionsArray()); ?>
    <?php echo $form->error($model, 'slider_position'); ?>
</div>
<?php endif; ?>
</div>
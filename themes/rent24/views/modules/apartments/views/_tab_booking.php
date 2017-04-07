<div id="datepicker-prev" class="datepicker"></div>
<div id="datepicker-next" class="datepicker"></div>
<div class="datepicker-info">
    <div class="datepicker-info-today"><span></span>Сегодня</div>
    <div class="datepicker-info-available"><span></span>Свободно</div>
    <div class="datepicker-info-reserved"><span></span>Забронировано</div>
</div>
<?php $rows = ApartmentPrice::getApartmentPrices($apartment->id); ?>
<?php if ($rows): ?>
<div class="price-ranges">
    <strong>Цены:</strong>
    <?php foreach($rows as $row): ?>
    <div class="price-ranges-item">
        <span class="price-range-dates">
            <span>с</span>
            <span class="price-range-date"><?php echo ApartmentPrice::returnDate(strtotime($row->date_start)) ?></span>
            &nbsp;
            <span>по</span>
            <span class="price-range-date"><?php echo ApartmentPrice::returnDate(strtotime($row->date_end)) ?></span>
        </span>
        &nbsp;&nbsp;
        <span class="price-range-prices">
            <span class="price-range-price"><?php echo CHtml::encode($row->price) ?></span>
            &nbsp;<?php echo param('siteCurrency', '$') ?>
        </span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script type="text/javascript">
    (function($) {
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
                dateFormat: 'yy-mm-dd',
                firstDay: 1,
                isRTL: false
            };
            $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );

            <?php
            $reseved_dates = array();

            foreach($bookings as $booking) {
                $date_start = strtotime($booking->date_start);
                $date_end = strtotime($booking->date_end);

                for ($d=$date_start; $d<=$date_end; $d+=86400) {
                    $reseved_dates[] = 'new Date('.date('Y',$d).','.(intval(date('n',$d))-1).','.date('j',$d).')';
                }
            }

            echo 'var reservedDates = ['.implode(',',$reseved_dates).'];';

            ?>

            var tooltipText = 'Забронировано';

            var datepicker_prev=$('#datepicker-prev').datepicker({
                beforeShowDay: function (date) {
                    for (var i=0; i<reservedDates.length; i++) {
                        if (date.getTime() == reservedDates[i].getTime()) {
                            return [false, 'ui-state-reserved', tooltipText];
                        }
                    }
                    return [true, '', ''];
                },
                onSelect: bookDate
            });

            var datepicker_next=$('#datepicker-next').datepicker({
                beforeShowDay: function (date) {
                    for (var i=0; i<reservedDates.length; i++) {
                        if (date.getTime() == reservedDates[i].getTime()) {
                            return [false, 'ui-state-reserved', tooltipText];
                        }
                    }
                    return [true, '', ''];
                },
                onSelect: bookDate
            });

            $.datepicker._adjustDate(datepicker_next,1,'M');
        });
    })(jQuery);


    function bookDate(date) {
        if (typeof(date)=="undefined" || !date.length) return;

        var href='/booking/add?id=<?php echo $apartment->id ?>&date_start='+date;
        window.location.href= href;
    }
</script>
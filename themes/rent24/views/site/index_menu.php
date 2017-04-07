<div id="menu-left" class="panel-left panel-item panel-small shadow-center">
    <ul>
        <li><a href="/page/4"><span>Апартаменты</span></a></li>
        <li><a href="/page/5"><span>Базы отдыха</span></a></li>
        <li><a href="/page/6"><span>Гостиницы</span></a></li>
        <li><a href="/page/7"><span>Квартиры</span></a></li>
        <li><a href="/page/21"><span>Дома</span></a></li>
        <li><a href="/page/8"><span>Комнаты</span></a></li>
        <li><a href="/page/9"><span>Коттеджи</span></a></li>
        <li><a href="/page/10"><span>Миниотели</span></a></li>
        <li><a href="/page/11"><span>Отели</span></a></li>
        <li><a href="/page/12"><span>Пансионаты</span></a></li>
        <li><a href="/page/13"><span>Услуги</span></a></li>
        <li><a href="/page/3"><span>Продажа</span></a></li>
    </ul>
    <?php
    /*
    $this->widget('CustomMenu',array(
        'id' => 'menu-l',
        'items' => $this->aData['topMenuItems'],
        'htmlOptions' => array('class' => 'menu-l'),
        'encodeLabel' => false,
        'linkLabelWrapper' => 'span'
    ));
    */

    Yii::app()->clientScript->registerScript('initizlize-left-menu','
        $("#menu-left ul li").hover(
            function() {
                $("#menu-left ul li.active").removeClass("active").addClass("active-state");
                $(this).addClass("blue-item");
            },
            function() {
                $("#menu-left ul li.active-state").removeClass("active-state").addClass("active");
                $(this).removeClass("blue-item");
            }
        );
        ', CClientScript::POS_READY);
    ?>
</div><!--/menu-left-->
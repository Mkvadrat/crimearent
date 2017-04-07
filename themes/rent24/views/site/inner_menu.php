<div id="wide-menu" class="panel-wrapper shadow-down">
    <div class="panel">
        <ul class="panel-row">
            <?php
            $wide_menu = array(
                '/page/4' => 'Апартаменты',
                '/page/5' => 'Базы отдыха',
                '/page/6' => 'Гостиницы',
                '/page/7' => 'Квартиры',
                '/page/21' => 'Дома',
                '/page/8' => 'Комнаты',
                '/page/9' => 'Коттеджи',
                '/page/10' => 'Миниотели',
                '/page/11' => 'Отели',
                '/page/12' => 'Пансионаты'
            );

            foreach ($wide_menu as $href=>$title) {
                $class='';
                if (Yii::app()->request->url == $href) $class=' active';
                echo '<li class="panel-item'.$class.'">';
                echo '<a href="'.$href.'" class="panel-item-link'.$class.'">'.$title.'</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</div><!--/wide-menu-->
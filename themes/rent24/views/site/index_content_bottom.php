<?php if (!Yii::app()->request->isAjaxRequest): ?>
<div id="content-bottom">
<div class="container">
    <div id="appartments-list-wrapper">
<?php endif; ?>

<?php $modeListShow = User::getModeListShow(); ?>
<?php if ($modeListShow == 'block') : ?>

<div class="panel-wrapper"><div class="panel"><div class="panel-row">
    <div id="bottom-grey-list-left" class="bottom-grey-list-wrapper panel-middle panel-item  no-shadow">
        <?php $this->widget('ApartmentsWidgetType', array(
            'button_active_registration' => 'Сниму жилье',//мой код
            'type' => 3,
            'widgetTitle' => 'Хотят снять жильё',
            'button_text' => 'Смотреть весь спрос'
        )); ?>
    </div><!--/bottom-grey-list-left-->
    <div class="panel-item panel-space panel-space-big"></div>
    <div id="bottom-grey-list-right" class="bottom-grey-list-wrapper panel-middle panel-item  no-shadow">
        <?php $this->widget('ApartmentsWidgetType', array(
            'button_active_registration' => 'Сдам жилье',//мой код
            'type' => 1,
            'widgetTitle' => 'Хотят сдать жильё',
            'button_text' => 'Смотреть все предложения'
        )); ?>
    </div><!--/bottom-grey-list-right-->
</div></div></div><!--/panel-wrapper-->


<?php else: ?>
<div id="bottom-grey-grid" class="bottom-grey-list-wrapper no-shadow">
    <?php $this->widget('ApartmentsWidgetType', array(
        'button_active_registration' => 'Сниму жилье',//мой код
        'type' => 3,
        'widgetTitle' => 'Хотят снять жильё',
        'button_text' => 'Смотреть весь спрос'
    )); ?>
</div>
<div id="bottom-grey-grid" class="bottom-grey-list-wrapper no-shadow">
    <?php $this->widget('ApartmentsWidgetType', array(
        'button_active_registration' => 'Сдам жилье',//мой код
        'type' => 1,
        'widgetTitle' => 'Хотят сдать жильё',
        'button_text' => 'Смотреть все предложения'
    )); ?>
</div>
<?php endif; ?>


<?php
if($page){
    if (isset($page->page)) {

        if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_TOP){
            echo '<div>';
            Yii::import('application.modules.'.$page->page->widget.'.components.*');
            if($page->page->widget == 'contactform'){
                $this->widget('ContactformWidget', array('page' => 'index'));
            } else {
                $this->widget(ucfirst($page->page->widget).'Widget');
            }
            echo '</div><div class="clear"></div>';
        }



        if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_BOTTOM){
            echo '<div class="clear"></div><div>';
            Yii::import('application.modules.'.$page->page->widget.'.components.*');
            if($page->page->widget == 'contactform'){
                $this->widget('ContactformWidget', array('page' => 'index'));
            } else {
                $this->widget(ucfirst($page->page->widget).'Widget');
            }
            echo '</div>';
        }
    }
}

?>

<?php if (!Yii::app()->request->isAjaxRequest): ?>
    </div><!--/appartments-list-wrapper-->
</div><!--/container-->
</div><!--/content-bottom-->
<?php endif; ?>
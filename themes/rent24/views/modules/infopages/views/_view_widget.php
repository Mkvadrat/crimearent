<?php if (!Yii::app()->request->isAjaxRequest): ?>
    <div class="clear" style="height:30px"></div>
    <div id="appartments-list-wrapper">
<?php endif; ?>
<?php

//echo '<div class="clear"></div><div>';
Yii::import('application.modules.'.$model->widget.'.components.*');
$widgetData = array();

switch($model->widget){
    case 'contactform':
        $widgetData = array('page' => 'index');
        break;

    case 'apartments':
        $widgetData = array('criteria' => $model->getCriteriaForAdList());
        break;
}
//$this->widget(ucfirst($model->widget).'Widget', $widgetData);

//echo '</div>';
//echo '<div class="clear"></div>';
?>





<?php $modeListShow = User::getModeListShow(); ?>
<?php if ($modeListShow == 'block') : ?>

    <div class="panel-wrapper"><div class="panel"><div class="panel-row">
                <div id="bottom-grey-list-left" class="bottom-grey-list-wrapper panel-middle panel-item  no-shadow">
                    <?php if ($_SERVER['REQUEST_URI']=='/page/3' || strpos($_SERVER['REQUEST_URI'],'/page/3?')===0) {
                        $this->widget('ApartmentsWidgetType', array(
                            'type' => 2,
                            'widgetTitle' => 'Хотят продать жильё',
                            'button_text' => 'Смотреть весь спрос',
                            //'criteria' => $widgetData['criteria']
                        ));
                    } else {
                        $this->widget('ApartmentsWidgetType', array(
                            'type' => 3,
                            'widgetTitle' => 'Хотят снять жильё',
                            'button_text' => 'Смотреть весь спрос',
                            'criteria' => $widgetData['criteria']
                        ));
                    }
                ?>
                </div><!--/bottom-grey-list-left-->
                <div class="panel-item panel-space panel-space-big"></div>
                <div id="bottom-grey-list-right" class="bottom-grey-list-wrapper panel-middle panel-item  no-shadow">
                    <?php if ($_SERVER['REQUEST_URI']=='/page/3' || strpos($_SERVER['REQUEST_URI'],'/page/3?')===0) {
                        $this->widget('ApartmentsWidgetType', array(
                            'type' => 4,
                            'widgetTitle' => 'Хотят купить жильё',
                            'button_text' => 'Смотреть все предложения',
                            //'criteria' => $widgetData['criteria']
                        ));
                    } else {
                        $this->widget('ApartmentsWidgetType', array(
                            'type' => 1,
                            'widgetTitle' => 'Хотят сдать жильё',
                            'button_text' => 'Смотреть все предложения',
                            'criteria' => $widgetData['criteria']
                        ));
                    } ?>
                </div><!--/bottom-grey-list-right-->
            </div></div></div><!--/panel-wrapper-->


<?php else: ?>
    <div id="bottom-grey-grid" class="bottom-grey-list-wrapper no-shadow">
        <?php if ($_SERVER['REQUEST_URI']=='/page/3' || strpos($_SERVER['REQUEST_URI'],'/page/3?')===0) {
            $this->widget('ApartmentsWidgetType', array(
                'type' => 2,
                'widgetTitle' => 'Хотят продать жильё',
                'button_text' => 'Смотреть весь спрос',
                //'criteria' => $widgetData['criteria']
            ));
        } else {
            $this->widget('ApartmentsWidgetType', array(
                'type' => 3,
                'widgetTitle' => 'Хотят снять жильё',
                'button_text' => 'Смотреть весь спрос',
                'criteria' => $widgetData['criteria']
            )); } ?>
    </div>
    <div id="bottom-grey-grid" class="bottom-grey-list-wrapper no-shadow">
        <?php if ($_SERVER['REQUEST_URI']=='/page/3' || strpos($_SERVER['REQUEST_URI'],'/page/3?')===0) {
            $this->widget('ApartmentsWidgetType', array(
                'type' => 4,
                'widgetTitle' => 'Хотят купить жильё',
                'button_text' => 'Смотреть все предложения',
                //'criteria' => $widgetData['criteria']
            ));
        } else {
            $this->widget('ApartmentsWidgetType', array(
                'type' => 1,
                'widgetTitle' => 'Хотят сдать жильё',
                'button_text' => 'Смотреть все предложения',
                'criteria' => $widgetData['criteria']
            ));
        } ?>
    </div>
<?php endif; ?>

<?php if (!Yii::app()->request->isAjaxRequest): ?>
    </div><!--/appartments-list-wrapper-->
<?php endif; ?>
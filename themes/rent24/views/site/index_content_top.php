<div id="content-top" class="shadowed">
    <div class="container">
        <?php $this->renderPartial('//site/logo_panel'); ?>
        <div class="panel-wrapper"><div class="panel"><div class="panel-row">
                    <div class="panel-left panel-item no-shadow"><div id="slider-left" class="slider shadow-down">
                            <?php Yii::app()->controller->renderPartial('//site/slider-left'); ?>
                        </div></div><!--/slider-left-->
                    <div class="panel-item panel-space"></div>
                    <div id="slider-filter" class="panel-middle panel-item shadow-down">
                        <?php Yii::app()->controller->renderPartial('//site/index-search'); ?>
                    </div><!--/slider-filter-->
                    <div class="panel-item panel-space"></div>
                    <div class="panel-right panel-item no-shadow"><div id="slider-right" class="slider shadow-down">
                            <?php Yii::app()->controller->renderPartial('//site/slider-right'); ?>
                        </div></div><!--/slider-right-->
                </div></div></div><!--/panel-wrapper-->
    </div><!--/container-->
</div><!--/content-top-->

<?php
    Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/js/slider/themes/default/default.css');
    Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/js/slider/nivo-slider.css');

    Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/slider/jquery.nivo.slider.pack.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerScript('slider', '
				$(".nivoSlider").nivoSlider({effect: "random", randomStart: true, pauseTime: 10000});
			', CClientScript::POS_READY);
?>
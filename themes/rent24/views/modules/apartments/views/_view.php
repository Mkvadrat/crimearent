
	<div class="clear"></div>

    <div class="panel-wrapper"><div class="panel"><div class="panel-row">
    <div id="product-tabs-wrapper" class="panel-middle panel-item  shadow-down">
		<?php

			$generalContent = $this->renderPartial('//modules/apartments/views/_tab_general', array(
				'data'=>$data,
			), true);

			if($generalContent){
				$items[tc('General')] = array(
					'content' => $generalContent,
					'id' => 'tab_1',
				);
			}

			if(!param('useBootstrap')){
				Yii::app()->clientScript->scriptMap=array(
					'jquery-ui.css'=>false,
				);
			}

			if(issetModule('bookingcalendar') && $data->type == Apartment::TYPE_RENT){
				Bookingcalendar::publishAssets();

				$items[tt('The periods of booking apartment', 'bookingcalendar')] = array(
					'content' => $this->renderPartial('//modules/bookingcalendar/views/calendar', array(
						'apartment'=>$data,
					), true),
					'id' => 'tab_2',
				);
			}

            if(($data->owner_id != Yii::app()->user->getId()) && $data->type == Apartment::TYPE_RENT){
                $items[tt('Booking periods', 'apartments')] = array(
                    'content' => $this->renderPartial('//modules/apartments/views/_tab_booking', array(
                            'apartment'=>$data,
                            'bookings' => $bookings
                        ), true),
                    'id' => 'tab_2',
                );
            }

            $data->references = $data->getFullInformation($data->id, $data->type);

            $additionFields = HFormEditor::getExtendedFields();
            $existValue = HFormEditor::existValueInRows($additionFields, $data);

            if($existValue){
                $items[tc('Additional info')] = array(
                    'content' => $this->renderPartial('//modules/apartments/views/_tab_addition', array(
                        'data'=>$data,
                        'additionFields' =>$additionFields
                    ), true),
                    'id' => 'tab_3',
                );
            }

			if ($data->panorama){
				$items[tc('Panorama')] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_panorama', array(
						'data'=>$data,
					), true),
					'id' => 'tab_7',
				);
			}

			if (isset($data->video) && $data->video){
				$items[tc('Videos for listing')] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_video', array(
						'data'=>$data,
					), true),
					'id' => 'tab_4',
				);
			}


			/*if(!Yii::app()->user->hasState('isAdmin') && (Yii::app()->user->hasFlash('newComment') || $comment->getErrors())){
				Yii::app()->clientScript->registerScript('comments','
				setTimeout(function(){
					$("a[href=#tab_5]").click();
				}, 0);
				scrollto("comments");
			',CClientScript::POS_READY);
			}*/




			if ($data->type != Apartment::TYPE_BUY && $data->type != Apartment::TYPE_RENTING) {
				if($data->lat && $data->lng){
					if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
						$items[tc('Map')] = array(
							'content' => $this->renderPartial('//modules/apartments/views/_tab_map', array(
								'data' => $data,
							), true),
							'id' => 'tab_5',
						);
					}
				}
			}

            if(param('enableCommentsForApartments', 1)){
                if(!isset($comment)){
                    $comment = null;
                }

                $items[/*Yii::t('module_comments','Comments')*/'Отзывы'.' ('.Comment::countForModel('Apartment', $data->id).')'] = array(
                    'content' => $this->renderPartial('//modules/apartments/views/_tab_comments', array(
                            'model' => $data,
                        ), true),
                    'id' => 'tab_6',
                );
            }

			$this->widget('zii.widgets.jui.CJuiTabs', array(
				'tabs' => $items,
				'htmlOptions' => array('class' => 'info-tabs', 'id' => 'tabs'),
				'headerTemplate' => '<li><a href="{url}" title="{title}" onclick="reInitMap(this);">{title}</a></li>',
				'options' => array(
				),
			));
		?>
    </div><!--/product-tabs-wrapper-->
    <div class="panel-item panel-space panel-space-big"></div>
    <div id="product-info-right" class="panel-right panel-item  no-shadow">
        <div class="reserve-block">
            <?php
            if(($data->owner_id != Yii::app()->user->getId()) && $data->type == Apartment::TYPE_RENT){
                echo CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $data->id));
            }
            ?>

            <div class="compare-block">
            <?php if (issetModule('comparisonList')):?>
                <?php
                $inComparisonList = false;
                if (in_array($data->id, Yii::app()->controller->apInComparison))
                    $inComparisonList = true;
                ?>
                <div class="compare-check-control view-apartment" id="compare_check_control_<?php echo $data->id; ?>">
                    <?php
                    $checkedControl = '';

                    if ($inComparisonList)
                        $checkedControl = ' checked = checked ';
                    ?>
                    <input type="checkbox" class="compare-check" name="compare<?php echo $data->id; ?>" id="compare_check<?php echo $data->id; ?>" <?php echo $checkedControl;?>>

                    <a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $data->id; ?>" class="compare-label">
                        <?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
                    </a>
                </div>
            <?php endif;?>
            </div>
        </div>
        <?php if(param('useShowUserInfo')): ?>
        <div class="product-user-block shadow-down">
            <h2>Объявление опубликовал</h2>
            <?php $this->renderPartial('//modules/apartments/views/_user_info', array('data' => $data)) ?>
        </div>
        <?php endif; ?>

        </div><!--/product-info-right-->
    </div></div></div><!--/panel-wrapper-->

    <div class="clear">&nbsp;</div>
	<?php
		if(!Yii::app()->user->getState('isAdmin')) {
			if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
				Yii::import('application.modules.similarads.components.SimilarAdsWidget');
				$ads = new SimilarAdsWidget;
				$ads->viewSimilarAds($data);
			}
		}

		Yii::app()->clientScript->registerScript('reInitMap', '
			var useYandexMap = '.param('useYandexMap', 1).';
			var useGoogleMap = '.param('useGoogleMap', 1).';
			var useOSMap = '.param('useOSMMap', 1).';

			function reInitMap(elem) {
				if($(elem).attr("href") == "#tab_5"){
					// place code to end of queue
					if(useGoogleMap){
						setTimeout(function(){
							var tmpGmapCenter = mapGMap.getCenter();

							google.maps.event.trigger($("#googleMap")[0], "resize");
							mapGMap.setCenter(tmpGmapCenter);

							if (($("#gmap-panorama").length > 0)) {
								initializeGmapPanorama();
							}
						}, 0);
					}

					if(useYandexMap){
						setTimeout(function(){
							ymaps.ready(function () {
								globalYMap.container.fitToViewport();
								globalYMap.setCenter(globalYMap.getCenter());
							});
						}, 0);
					}

					if(useOSMap){
						setTimeout(function(){
							L.Util.requestAnimFrame(mapOSMap.invalidateSize,mapOSMap,!1,mapOSMap._container);
						}, 0);
					}
				}
			}
		',
		CClientScript::POS_END);
	?>
<br />

<div class="panel-wrapper similar-wrapper"><div class="panel"><div class="panel-row">
<?php

if (is_array($ads) && count($ads) > 0) {
	echo '<div class="similar-ads" id="similar-ads">';
		echo '<h2>'.tt('Similar ads', 'similarads').'</h2>';
		echo '<ul id="mycarousel" class="jcarousel-skin-tango">';
			foreach ($ads as $item) {
				echo '<li>';
					echo '<a href="'.$item->getUrl().'" class="shadow-down">';
						$res = Images::getMainThumb(240, 135, $item->images, null , Yii::app()->theme->baseUrl.'/images/blank.gif');
						echo CHtml::image($res['thumbUrl'], '', array(
							'title' => $item->{'title_'.Yii::app()->language},
							'width' => 240,
							'height' => 135,
						));
					echo '</a>';
					if($item->getStrByLang('title')){
						echo '<p>'.truncateText(CHtml::encode($item->getStrByLang('title')), 6).'</p>';
					}
                    ?>
                    <div class="product-attributes">
                        <label>Цена:</label>  <?php echo $item->getPrettyPrice() ?>
                    </div>
                    <?php
					//echo '<div class="similar-price">'.tt('Price from', 'apartments').': '.$item->getPrettyPrice().'</div>';
				echo '</li>';
			}
		echo '</ul>';
	echo '</div>';

	if (count($ads) > 5) {
		Yii::app()->clientScript->registerScript('similar-ads-slider', '
			$("#mycarousel").jcarousel({ visible: 5});
		', CClientScript::POS_READY);
	}
	else {
		Yii::app()->clientScript->registerScript('similar-ads-slider', '
			$("#mycarousel").jcarousel({ visible: 5, buttonNextHTML: null, buttonPrevHTML: null});
		', CClientScript::POS_READY);
	}
}
?>
</div></div></div><!--/panel-wrapper-->
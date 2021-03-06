<?php
$owner = $data->user;

?>

<div class="user-avatar">
    <?php echo $owner->renderAva(); ?>
</div>
<div class="user-contacts">
    <span class="username"><?php echo $owner->getNameForType(); ?></span>

        <?php
    if($data->canShowInView('phone')){
        echo '<span class="phone" id="user-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>';
        if (!empty($data->phone2)) {
            echo '<span class="phone" id="user-phone2">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone2();')) . '</span>';
        }
    }
    ?>
    <!--<span class="email"><?php //echo $owner->email ?></span>-->
</div>
<div class="product-user-bottom">
    <div class="allproducts-wrapper"><?php echo $owner->getLinkToAllListings() ?></div>
    <?php
    if (param('use_module_request_property') && $data->owner_id != Yii::app()->user->id){
        echo CHtml::link(tt('request_for_property'), $data->getUrlSendEmail(), array('class'=>'fancy email'));
    }
    ?>
</div>

<?php
	//echo '<h3>'.tc('Listing provided by').'</h3>';

/*
	echo '<div class="user-info-ava">';
		echo $owner->renderAva();
		echo $owner->getNameForType();
	echo '</div>';

	echo '<div class="user-info-right">';
		echo '<ul class="user-info-ul">';
			if($data->canShowInView('phone')){
				$icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/phone-16.png');
				echo '<li>' . $icon . ' <span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</li>';
			}

			if (issetModule('messages') && $data->owner_id != Yii::app()->user->id && !Yii::app()->user->isGuest){
				$icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/email-16.png') . ' ';
				echo '<li>' . $icon . CHtml::link(tt('Send message', 'messages'), Yii::app()->createUrl('/messages/main/read', array('id' => $owner->id, 'apId' => $data->id))) . '</li>';
			}
			elseif (param('use_module_request_property') && $data->owner_id != Yii::app()->user->id){
				$icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/email-16.png') . ' ';
				echo '<li>' . $icon . CHtml::link(tt('request_for_property'), $data->getUrlSendEmail(), array('class'=>'fancy')) . '</li>';
			}

			$icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/ads-16.png') . ' ';
			echo '<li>' . $icon . $owner->getLinkToAllListings() . '</li>';
		echo '</ul>';
	echo '</div>';
	echo '<div class="clear"></div>';

	if($data->canShowInView('phone')) {
		echo '<div class="flash-notice phone-show-alert" style="display: none;">'.Yii::t('common', 'Please tell the seller that you have found this listing here {n}', '<strong>'.str_replace(array('http://', 'www.'), '', Yii::app()->getRequest()->getHostInfo()).'</strong>').'</div>';
	}

	$additionalInfo = 'additional_info_'.Yii::app()->language;
	if (isset($data->user->$additionalInfo) && !empty($data->user->$additionalInfo)){
		echo '<div class="clear"></div>';
		echo CHtml::encode(truncateText($data->user->$additionalInfo, 20));
	}
*/
	Yii::app()->clientScript->registerScript('generate-phone', '
		function generatePhone(){
			$("span#user-phone").html(\'<img src="'.Yii::app()->controller->createUrl('/apartments/main/generatephone', array('id' => $data->id)).'" />\');
			//$(".phone-show-alert").show();
		}
		function generatePhone2(){
			$("span#user-phone2").html(\'<img src="'.Yii::app()->controller->createUrl('/apartments/main/generatephone2', array('id' => $data->id)).'" />\');
			//$(".phone-show-alert").show();
		}
	', CClientScript::POS_END);
?>
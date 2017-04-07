<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *	version				:	%TAG%
 *	copyright			:	(c) %YEAR% Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

class MainController extends Controller {
    public $canUseDirectUrl = false;

	public function actionAjaxSave() {
		if(isset($_POST['SeoFriendlyUrl'])){
            $this->canUseDirectUrl = (int) Yii::app()->request->getPost('canUseDirectUrl');

			$friendlyUrl = SeoFriendlyUrl::model()->findByPk($_POST['SeoFriendlyUrl']['id']);

			if(!$friendlyUrl){
				$friendlyUrl = new SeoFriendlyUrl();
			}

			$friendlyUrl->attributes = $_POST['SeoFriendlyUrl'];

			if($friendlyUrl->save()){
				echo CJSON::encode(array(
					'status' => 'ok',
					'html' => $this->renderPartial('//modules/seo/views/_form', array('friendlyUrl' => $friendlyUrl), true)
				));
				Yii::app()->end();
			}else{
				echo CJSON::encode(array(
					'status' => 'err',
					'html' => $this->renderPartial('//modules/seo/views/_form', array('friendlyUrl' => $friendlyUrl), true)
				));
				Yii::app()->end();
			}
		}
		throw404();
	}
}

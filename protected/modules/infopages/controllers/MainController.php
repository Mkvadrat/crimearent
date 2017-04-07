<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.10.0
*	copyright			:	(c) 2015 Monoray
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

class MainController extends ModuleUserController{
	public $modelName = 'InfoPages';
	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array(
				'allow',
				'actions' => array('view', 'captcha'),
				'users'=>array('*'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}

	public function actionView($id = 0, $url = ''){
		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}

		$model = $this->loadModel($id, 1);
		
		$modelUrl = $model->getUrl(false);
		

		$rUrl = Yii::app()->getRequest()->getHostInfo().Yii::app()->request->url;
		
		if(issetModule('seo') && strpos($rUrl, $modelUrl) !== 0){
			$this->redirect($modelUrl, true, 301);
		}

		if (!$model->active)
			throw404();

		if(isset($_GET['is_ajax'])) {
			$this->renderPartial('view', array(
				'model'=>$model,
			));
		}
		else {
			$this->render('view',array(
				'model'=>$model,
			));
		}
	}

}
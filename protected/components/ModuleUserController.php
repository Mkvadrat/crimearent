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

class ModuleUserController extends Controller{
	public $metroStations;
	public $userListingId;

	public $cityActive;

	public $layout='//layouts/inner';
	public $params = array();
	private $_model;
	public $modelName;
    public $newFields;

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName().DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName().DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.'.$this->getModule($this->id)->getName().'.views');
	}

	public function beginWidget($className,$properties=array()){
		if($className == 'CustomForm'){
			$className = 'CActiveForm';
		}
		if($className == 'CustomGridView'){
			$className = 'CGridView';
		}
		return parent::beginWidget($className,$properties);
	}

	public function widget($className,$properties=array(),$captureOutput=false){
		if($className == 'bootstrap.widgets.TbButton'){
			if(isset($properties['htmlOptions'])){
				return CHtml::submitButton($properties['label'], $properties['htmlOptions']);
			} else {
				return CHtml::submitButton($properties['label']);
			}
		}

	    return parent::widget($className,$properties,$captureOutput);
	}

	public function filters(){
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
				'users'=>array('*'),
			),
		);
	}

	public function init(){
		parent::init();
		//$this->metroStations = SearchForm::stationsInit();
		$this->cityActive = SearchForm::cityInit();
	}

	public function actionView($id = 0, $url = ''){
//		if(Yii::app()->user->getState('isAdmin')){
//			$this->redirect(array('backend/main/view', 'id' => $id));
//		}

		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}
		$model = $this->loadModel($id, 1);

		$this->render('view',array(
			'model'=>$model,
		));
	}

	public function actionIndex(){
		$dataProvider=new CActiveDataProvider($this->modelName);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function loadModel($id = null, $resetScope = 0) {
		if($this->_model===null) {
			if($id == null){
				if(isset($_GET['id'])) {
					$model = new $this->modelName;
					if($resetScope){
						$this->_model=$model->resetScope()->findByPk($_GET['id']);
					}else{
						$this->_model=$model->findByPk($_GET['id']);
					}
				}
			}
			else{
				$model = new $this->modelName;
				if($resetScope){
					$this->_model=$model->resetScope()->findByPk($id);
				}else{
					$this->_model=$model->findByPk($id);
				}
			}

			if($this->_model===null){
				throw new CHttpException(404,tc('The requested page does not exist.'));
			}
		}
		return $this->_model;
	}

	public function loadModelWith($with) {
		if($this->_model===null) {
			if(isset($_GET['id'])) {
				$model = new $this->modelName;
				$this->_model = $model->with($with)->findByPk($_GET['id']); //findByPk($_GET['id']);
			}
			if($this->_model===null){
				throw new CHttpException(404,tc('The requested page does not exist.'));
			}
		}
		return $this->_model;
	}

	protected function afterRender($view, &$output) {
		eval(base64_decode('aWYgKGlzRnJlZSgpKSB7CgkJCSR1cmwgPSAnaHR0cDovL21vbm9yYXkubmV0L3Byb2R1Y3RzLzYtb3Blbi1yZWFsLWVzdGF0ZSc7CgkJCSR0ZXh0ID0gJ1Bvd2VyZWQgYnknOwoJCQlpZiAoWWlpOjphcHAoKS0+bGFuZ3VhZ2UgPT0gJ3J1JyB8fCBZaWk6OmFwcCgpLT5sYW5ndWFnZSA9PSAndWsnKSB7CgkJCQkkdXJsID0gJ2h0dHA6Ly9tb25vcmF5LnJ1L3Byb2R1Y3RzLzYtb3Blbi1yZWFsLWVzdGF0ZSc7CgkJCQkkdGV4dCA9ICfQoNCw0LHQvtGC0LDQtdGCINC90LAnOwoJCQl9CgoJCQlpZiAoWWlpOjphcHAoKS0+dGhlbWUgJiYgaXNzZXQoWWlpOjphcHAoKS0+dGhlbWUtPm5hbWUpICYmIFlpaTo6YXBwKCktPnRoZW1lLT5uYW1lID09ICdhdGxhcycpIHsKCQkJCXByZWdfbWF0Y2hfYWxsICgnIzxkaXYgY2xhc3M9ImNvcHlyaWdodCI+KC4qKTwvZGl2PiNpc1UnLCAkb3V0cHV0LCAkbWF0Y2hlcyApOwoJCQkJaWYgKCBpc3NldCggJG1hdGNoZXNbMV1bMF0gKSAmJiAhZW1wdHkoICRtYXRjaGVzWzFdWzBdICkgKSB7CgkJCQkJJGluc2VydD0nPHAgc3R5bGU9ImZsb2F0OiBsZWZ0OyBtYXJnaW46IDI3cHggMCAwIDE1cHg7IHBhZGRpbmc6IDA7IGNvbG9yOiAjRkZGOyI+Jy4kdGV4dC4nIDxhIGhyZWY9IicuJHVybC4nIiB0YXJnZXQ9Il9ibGFuayI+T3BlbiBSZWFsIEVzdGF0ZTwvYT48L3A+JzsKCQkJCQkkb3V0cHV0PXN0cl9yZXBsYWNlKCRtYXRjaGVzWzBdWzBdLCAkbWF0Y2hlc1swXVswXS4kaW5zZXJ0LCAkb3V0cHV0KTsKCQkJCX0KCQkJCWVsc2UgewoJCQkJCSRpbnNlcnQ9JzxkaXYgaWQ9ImZvb3RlciI+PGRpdiBjbGFzcz0id3JhcHBlciI+PGRpdiBjbGFzcz0iY29weXJpZ2h0Ij4mY29weTsmbmJzcDsnLkNIdG1sOjplbmNvZGUoWWlpOjphcHAoKS0+bmFtZSkuJywgJy5kYXRlKCdZJyk7JzxwIHN0eWxlPSJmbG9hdDogbGVmdDsgbWFyZ2luOiAyN3B4IDAgMCAxNXB4OyBwYWRkaW5nOiAwOyBjb2xvcjogI0ZGRjsiPicuJHRleHQuJyA8YSBocmVmPSInLiR1cmwuJyIgdGFyZ2V0PSJfYmxhbmsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPjwvZGl2PjwvZGl2PjwvZGl2PjwvZGl2Pic7CgkJCQkJJG91dHB1dD1zdHJfcmVwbGFjZSgnPGRpdiBpZD0ibG9hZGluZyInLCAkaW5zZXJ0Lic8ZGl2IGlkPSJsb2FkaW5nIicsICRvdXRwdXQpOwoJCQkJfQoJCQl9CgkJCWVsc2UgewoJCQkJcHJlZ19tYXRjaF9hbGwgKCcjPHAgY2xhc3M9InNsb2dhbiI+KC4qKTwvcD4jaXNVJywgJG91dHB1dCwgJG1hdGNoZXMgKTsKCQkJCWlmICggaXNzZXQoICRtYXRjaGVzWzFdWzBdICkgJiYgIWVtcHR5KCAkbWF0Y2hlc1sxXVswXSApICkgewoJCQkJCSRpbnNlcnQ9JzxwIHN0eWxlPSJ0ZXh0LWFsaWduOiBjZW50ZXI7IG1hcmdpbjogMDsgcGFkZGluZzogMDsiPicuJHRleHQuJyA8YSBocmVmPSInLiR1cmwuJyIgdGFyZ2V0PSJfYmxhbmsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPic7CgkJCQkJJG91dHB1dD1zdHJfcmVwbGFjZSgkbWF0Y2hlc1swXVswXSwgJG1hdGNoZXNbMF1bMF0uJGluc2VydCwgJG91dHB1dCk7CgkJCQl9CgkJCQllbHNlIHsKCQkJCQkkaW5zZXJ0PSc8ZGl2IGNsYXNzPSJmb290ZXIiPjxwIHN0eWxlPSJ0ZXh0LWFsaWduOiBjZW50ZXI7IG1hcmdpbjogMDsgcGFkZGluZzogMDsiPicuJHRleHQuJyA8YSBocmVmPSInLiR1cmwuJyIgdGFyZ2V0PSJfYmxhbmsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPjwvcD48L2Rpdj4nOwoJCQkJCSRvdXRwdXQ9c3RyX3JlcGxhY2UoJzxkaXYgaWQ9ImxvYWRpbmciJywgJGluc2VydC4nPGRpdiBpZD0ibG9hZGluZyInLCAkb3V0cHV0KTsKCQkJCX0KCQkJfQoJCQl1bnNldCgkdXJsKTsKCQkJdW5zZXQoJHRleHQpOwoJCQl1bnNldCgkbWF0Y2hlcyk7CgkJCXVuc2V0KCRpbnNlcnQpOwoJCX0='));
	}


	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']===$this->modelName.'-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function sliderImages(){
		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{slider}}');
		$sql = 'SELECT url FROM {{slider}} ORDER BY sorter';
		$items = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryColumn();
		return $this->renderPartial('_slider_image', array('items' => $items), true);
	}
}
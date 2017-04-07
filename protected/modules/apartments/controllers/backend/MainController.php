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

class MainController extends ModuleAdminController {

	public $modelName = 'Apartment';

	public function actionView($id = 0) {
		//$this->layout='//layouts/inner';

		Yii::app()->bootstrap->plugins['tooltip'] = array(
			'selector'=>' ', // bind the plugin tooltip to anchor tags with the 'tooltip' class
			'options'=>array(
				'placement'=>'top', // place the tooltips below instead
			),
		);

		$model = $this->loadModelWith(array('windowTo', 'objType', 'city'));

		if (!in_array($model->type, Apartment::availableApTypesIds())) {
			throw404();
		}

		$this->render('view', array(
			'model' => $model,
			'statistics' => Apartment::getApartmentVisitCount($model),
		));
	}

    public function actionAdmin(){

        $countNewsProduct = NewsProduct::getCountNoShow();
        if($countNewsProduct > 0){
            Yii::app()->user->setFlash('info', Yii::t('common', 'There are new product news') . ': '
                . CHtml::link(Yii::t('common', '{n} news', $countNewsProduct), array('/news/backend/main/product')));
        }

		$this->rememberPage();

		$this->getMaxSorter();
		$this->getMinSorter();

		$model = new Apartment('search');

        $model->setRememberScenario('ads_remember');

		$model = $model->with(array('user'));

		$this->render('admin',array_merge(array('model'=>$model), $this->params));


    }

	public function actionUpdate($id){
        $this->_model = $this->loadModel($id);


        if(!$this->_model){
            throw404();
        }

        $oldStatus = $this->_model->active;

        if(issetModule('bookingcalendar')) {
			$this->_model = $this->_model->with(array('bookingCalendar'));
		}

        if(isset($_GET['type'])){
            $this->_model->type = HApartment::getRequestType();
        }

		if(isset($_POST[$this->modelName])){
			$this->_model->attributes = $_POST[$this->modelName];

			if ($this->_model->type != Apartment::TYPE_BUY && $this->_model->type != Apartment::TYPE_RENTING) {
				// video, panorama, lat, lon
                HApartment::saveOther($this->_model);
			}

            if (Apartment::ENABLE_SUBTYPE && $this->_model->type != Apartment::TYPE_RENT && $this->_model->type != Apartment::TYPE_RENTING) {
                $this->_model->subtype = 0;
            }

            // slider
            if (Yii::app()->user->getState('isAdmin') && !empty($_POST['slider_position'])) {
                ApartmentBanner::savePosition($this->_model->id, $_POST['slider_position']);
            }

            $this->_model->scenario = 'savecat';

            $isUpdate = Yii::app()->request->getPost('is_update');

			if($isUpdate){
				$this->_model->active = $oldStatus;
				$this->_model->save(false);
			} elseif($this->_model->validate()) {
				$this->_model->save(false);
				Yii::app()->user->setFlash('success', tc('Success'));
				$this->redirect(array('update','id'=>$this->_model->id));
			}
		}

        HApartment::getCategoriesForUpdate($this->_model);

        if($this->_model->active == Apartment::STATUS_DRAFT){
			Yii::app()->user->setState('menu_active', 'apartments.create');
			$this->render('create', array(
				'model' => $this->_model,
				'supportvideoext' => ApartmentVideo::model()->supportExt,
				'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
			));
			return;
		}

		$this->render('update', array(
			'model' => $this->_model,
			'supportvideoext' => ApartmentVideo::model()->supportExt,
			'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
		));
	}


	public function actionCreate(){
		$model = new $this->modelName;
		$model->active = Apartment::STATUS_DRAFT;
		$model->owner_active = Apartment::STATUS_ACTIVE;
        $model->setDefaultType();
		$model->save(false);

		$this->redirect(array('update', 'id' => $model->id));
	}

	public function getWindowTo(){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as title FROM {{apartment_window_to}}';
		$results = Yii::app()->db->createCommand($sql)->queryAll();
		$return = array();
		$return[0] = '';
		if($results){
			foreach($results as $result){
				$return[$result['id']] = $result['title'];
			}
		}
		return $return;
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
			$apartment = $this->loadModel($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = floatval($_POST['lat']);
				$apartment->lng = floatval($_POST['lng']);
				$apartment->update(array('lat', 'lng'));
			}
			Yii::app()->end();
		}
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true), true);

		if($result){
			return $this->renderPartial('_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){

		if($model === null){
			$model = $this->loadModel($id);
		}

		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}

	public function actionOSmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomOSMap::actionOSmap($id, $model, $this->renderPartial('_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('_osmap', $result, true);
		}
		return '';
	}

	public function actionSortItems() {
		if (isset($_POST['items']) && is_array($_POST['items'])) {
			//$thisModel = call_user_func($this->modelName, 'model');

			//$cur_items = $thisModel::model()->findAllByPk($_POST['items'], array('order'=>'sorter'));
			$cur_items = CActiveRecord::model($this->modelName)->findAllByPk($_POST['items'], array('order'=>'sorter DESC'));

			for ($i = 0; $i < count($_POST['items']); $i++) {
				//$item = $thisModel::model()->findByPk($_POST['items'][$i]);

				$item = CActiveRecord::model($this->modelName)->findByPk($_POST['items'][$i]);

				if ($item->sorter != $cur_items[$i]->sorter) {
					$item->sorter = $cur_items[$i]->sorter;
					$item->save(false);
				}
			}
		}
	}

    public function actionSubscriptions(){

        Yii::app()->user->setState('menu_active', 'apartments.subscriptions');

        $this->rememberPage();

        $this->getMaxSorter();
        $this->getMinSorter();

        $params = Yii::app()->request->getParam('ApartmentSubscription');

        $model = new ApartmentSubscription('search');

        if (!empty($params['id'])) $model->id = $params['id'];
        if (!empty($params['name'])) $model->name = $params['name'];
        if (!empty($params['email'])) $model->email = $params['email'];
        if (!empty($params['phone'])) $model->phone = $params['phone'];
        if (!empty($params['ip'])) $model->ip = $params['ip'];
        if (!empty($params['date_added'])) $model->date_added = $params['date_added'];

        $this->render('subscription',array_merge(array('model'=>$model), $this->params));
    }

    public function actionSubscriptionremove(){
        $id = intval(Yii::app()->request->getParam('id'));

        if ($id) {
            $model = ApartmentSubscription::model()->findByPk($id);
            if ($model) {
                $model->delete();
            }
        }
    }
}
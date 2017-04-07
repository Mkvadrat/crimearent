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

class MainController extends ModuleUserController {

	public $modelName = 'Apartment';

	public function actions() {
		return array(
			'captcha' => array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
		);
	}

	public function actionIndex(){
		throw new CHttpException(404,tc('The requested page does not exist.'));
	}

    public function actionDatepriceadd() {


        if (!Yii::app()->request->isPostRequest) return;

        $apartment_id = intval(Yii::app()->request->getPost('apartment_id'));
        $date_start = Yii::app()->request->getPost('date_start');
        $date_end = Yii::app()->request->getPost('date_end');
        $price = intval(Yii::app()->request->getPost('price'));

        $error = '';
        $message = '';
        $data = array();

        try {
            if (empty($apartment_id) || empty($price) || $price<0) {
                throw new Exception('Неверные данные');
            }
            if (empty($date_start) && empty($date_end)) {
                throw new Exception('Неверные даты');
            }
            if (!empty($date_start) && !preg_match('/^([\d]{2})[.]([\d]{2})[.]([\d]{4})$/',$date_start,$m)) {
                throw new Exception('Неверная начальная дата');
            }
            if (!empty($date_start)){
                $date_start = $m[3].'-'.$m[2].'-'.$m[1];
            } else {
                $date_start = ApartmentPrice::DATE_START_EMPTY;
            }
            if (!empty($date_end) && !preg_match('/^([\d]{2})[.]([\d]{2})[.]([\d]{4})$/',$date_end,$m)) {
                throw new Exception('Неверная конечная дата');
            }
            if (!empty($date_end)) {
                $date_end = $m[3].'-'.$m[2].'-'.$m[1];
            } else {
                $date_end = ApartmentPrice::DATE_END_EMPTY;
            }

            $date_start_time = strtotime($date_start);
            $date_end_time = strtotime($date_end);

            if ($date_start_time > $date_end_time) {
                throw new Exception('Конечная дата должна быть больше или равна начальной');
            }

            $existing_prices = ApartmentPrice::getApartmentPrices($apartment_id);
            if ($existing_prices) {
                foreach ($existing_prices as $row) {
                    $existing_date_start_time = strtotime($row->date_start);
                    $existing_date_end_time = strtotime($row->date_end);
                    if (
                        ($date_start_time <= $existing_date_end_time && $date_end_time >= $existing_date_start_time) ||
                        ($existing_date_start_time <= $date_end_time && $existing_date_end_time >= $date_start_time)
                    ) {
                        $date_start_str = ApartmentPrice::returnDate($existing_date_start_time, false);
                        $date_end_str = ApartmentPrice::returnDate($existing_date_end_time, false);
                        throw new Exception('Указанный период времени уже используется: "'.$date_start_str.'" - "'.$date_end_str.'"');
                    }
                }
            }

            if (!Yii::app()->user->getState('isAdmin')) {
                $criteria = new CDbCriteria();
                $criteria->addCondition('owner_id = :user_id');
                $criteria->addCondition('id = :id');
                $criteria->params[':user_id'] = Yii::app()->user->id;
                $criteria->params[':id'] = $apartment_id;

                $apartment = Apartment::model()->find($criteria);
            } else {
                $apartment = Apartment::model()->findByPk($apartment_id);
            }
            if (!$apartment) {
                throw new Exception('Объект не найден');
            }

            $apartmentPrice = new ApartmentPrice();
            $apartmentPrice->setAttribute('apartment_id', $apartment_id);
            $apartmentPrice->setAttribute('date_start', date('Y-m-d',$date_start_time));
            $apartmentPrice->setAttribute('date_end', date('Y-m-d',$date_end_time));
            $apartmentPrice->setAttribute('price', $price);

            $apartmentPrice->validate();

            if (!$apartmentPrice->hasErrors()) {
                $apartmentPrice->save();

                $message = 'Цена добавлена';

                $data = array(
                  'date_start' => ApartmentPrice::returnDate(strtotime($apartmentPrice->date_start)),
                  'date_end' => ApartmentPrice::returnDate(strtotime($apartmentPrice->date_end)),
                  'price' => $apartmentPrice->price,
                  'id' => $apartmentPrice->id
                );
            } else {
                $errors = $apartmentPrice->getErrors();
                foreach($errors as $_errors) {
                    if (is_array($_errors)) {
                        foreach($_errors as $_error) {
                            $error .= $_error."\r\n";
                        }
                    }
                }
            }
        } catch(Exception $e) {
            $error = $e->getMessage();
        }

        echo json_encode(array(
            'message' => $message,
            'error' => $error,
            'data' => $data
        ));
    }

    public function actionDatepriceremove() {
        if (!Yii::app()->request->isPostRequest) return;

        $apartment_id = intval(Yii::app()->request->getPost('apartment_id'));
        $id = intval(Yii::app()->request->getPost('id'));

        $error = '';
        $message = '';

        try {
            if (empty($apartment_id) || empty($id)) {
                throw new Exception('Неверные данные');
            }

            if (!Yii::app()->user->getState('isAdmin')) {
                $criteria = new CDbCriteria();
                $criteria->addCondition('owner_id = :user_id');
                $criteria->addCondition('id = :id');
                $criteria->params[':user_id'] = Yii::app()->user->id;
                $criteria->params[':id'] = $apartment_id;

                $apartment = Apartment::model()->find($criteria);
            } else {
                $apartment = Apartment::model()->findByPk($apartment_id);
            }
            if (!$apartment) {
                throw new Exception('Объект не найден');
            }

            $apartmentPrice = new ApartmentPrice();
            $row = $apartmentPrice->findByPk($id);
            if ($row && $row->apartment_id == $apartment_id) {
                $row->delete();
                $message = 'Цена удалена';
            } else {
                $error = 'Цена не найдена';
            }
        } catch(Exception $e) {
            $error = $e->getMessage();
        }

        echo json_encode(array(
            'message' => $message,
            'error' => $error
        ));
    }

    public function actionSubscribe() {
        if (!Yii::app()->request->isPostRequest) return;

        $name = Yii::app()->request->getPost('name');
        $email = Yii::app()->request->getPost('email');
        $phone = Yii::app()->request->getPost('phone');

        $ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';

        $error = '';
        $message = '';

        try {
            if (empty($name) || (empty($email) && empty($phone))) {
                throw new Exception('Заполните поля');
            }

            $model = ApartmentSubscription::model();
            $model->setAttribute('name', $name);
            $model->setAttribute('email', $email);
            $model->setAttribute('phone', $phone);
            $model->setAttribute('date_added', date('Y-m-d H:i:s'));
            $model->setAttribute('ip', $ip);

            $model->validate();

            if (!$model->hasErrors()) {
                $model->setIsNewRecord(true);
                if ($model->save()) {
                    $message = 'Спасибо! Вы подписались на рассылку.';

                    if (!empty($model->email)) {
                        $model->sendConfirmEmail();
                    }
                    if (!empty($model->phone)) {
                        $model->sendConfirmSMS();
                    }
                } else {
                    $error = 'Ошибка. Попробуйте позже';
                }
            } else {
                $errors = $model->getErrors();
                foreach($errors as $_errors) {
                    if (is_array($_errors)) {
                        foreach($_errors as $_error) {
                            $error .= $_error."\r\n";
                        }
                    }
                }
            }

        } catch(Exception $e) {
            $error = $e->getMessage();
        }

        echo json_encode(array(
            'message' => $message,
            'error' => $error
        ));
    }

	public function actionView($id = 0, $url = '', $printable = 0) {
		$this->htmlPageId = 'viewlisting';

		$apartment = NULL;

		if( ($id || $url) && issetModule('seo') ){
			$url = $url ? $url : $id;
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if($seo){
				$this->setSeo($seo);
				$id = $seo->model_id;
			}
		}

		if($id) {
			$apartment = Apartment::model()->with(array('windowTo', 'objType', 'city'))->findByPk($id);
		}

		if(!$apartment){
			throw404();
		}

		if (!in_array($apartment->type, Apartment::availableApTypesIds())) {
			throw404();
		}

		if( $apartment->owner_id != 1 && $apartment->owner_active == Apartment::STATUS_INACTIVE) {
			if (!(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id) && !Yii::app()->user->getState('isAdmin')) {
				Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
				throw404();
			}
		}

		if(($apartment->active == Apartment::STATUS_INACTIVE || $apartment->active == Apartment::STATUS_MODERATION)
		&& !Yii::app()->user->getState('isAdmin')
		&& !(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id)
        && ($apartment->active != Apartment::STATUS_MODERATION|| !Apartment::SHOW_MODERATION_OBJECTS_IN_TIME || time()-strtotime($apartment->date_created) > Apartment::MODERATION_VIEW_TIME)
        ){
			Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
			//$this->redirect(Yii::app()->homeUrl);
			throw404();
		}

		if($apartment->active == Apartment::STATUS_MODERATION && $apartment->owner_active == Apartment::STATUS_ACTIVE && $apartment->owner_id == Yii::app()->user->id){
			Yii::app()->user->setFlash('error', tc('Awaiting moderation'));
		}

		$dateFree = CDateTimeParser::parse($apartment->is_free_to, 'yyyy-MM-dd');
		if($dateFree && $dateFree < (time()-60*60*24)){
			$apartment->is_special_offer = 0;
			$apartment->update(array('is_special_offer'));
		}

        $bookingCriteria = new CDbCriteria();
        $bookingCriteria->addCondition('apartment_id = :apartment_id');
        $bookingCriteria->params[':apartment_id'] = $apartment->id;
        $bookingCriteria->addCondition('active = :status');
        $bookingCriteria->params[':status'] = Bookingtable::STATUS_CONFIRM;
        $bookings = Bookingtable::model()->findAll($bookingCriteria);


		if (!Yii::app()->request->isAjaxRequest) {
			$ipAddress = Yii::app()->request->userHostAddress;
			$userAgent = Yii::app()->request->userAgent;
			Apartment::setApartmentVisitCount($apartment, $ipAddress, $userAgent);
		}

		$lastNews = News::getLastNews();
		$lastArticles = Article::getLastArticles();

		if ($printable) {
			$this->layout='//layouts/print';
			$this->render('view_print', array(
				'model' => $apartment,
			));
		} else {
			$this->render('view', array(
				'model' => $apartment,
				'statistics' => Apartment::getApartmentVisitCount($apartment),
				'lastNews' => $lastNews,
				'lastArticles' => $lastArticles,
                'bookings' => $bookings
			));
		}
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true), true);

		if($result){
			return $this->renderPartial('backend/_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}

	public function actionOSmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomOSMap::actionOSmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('backend/_osmap', $result, true);
		}
		return '';
	}

	public function actionGeneratePhone($id = null, $width=130, $font=3) {

		if ($id && param('useShowUserInfo')) {
            $this->countPhoneViews($id);

			$apartmentInfo = Apartment::model()->findByPk($id, array('select' => 'owner_id, phone'));

            $phone = $apartmentInfo->phone;

			if (!$phone && $apartmentInfo->owner_id){
                $userInfo = User::model()->findByPk($apartmentInfo->owner_id, array('select' => 'phone'));
                $phone = $userInfo->phone;
            }

			if ($phone) {
				$image = imagecreate($width, 20);

				$font = Yii::app()->theme->name != 'classic' ? 4 : $font;

				$bg = imagecolorallocate($image, 255, 255, 255);
				$textcolor = imagecolorallocate($image, 37, 75, 137);

				imagestring($image, $font, 0, 0, $phone, $textcolor);

				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Transfer-Encoding: binary');
				header("Content-type: image/png");
				imagepng($image);
				//echo $image;
				imagedestroy($image);
			}
		}
	}

    public function actionGeneratePhone2($id = null, $width=130, $font=3) {

        if ($id && param('useShowUserInfo')) {
            $this->countPhoneViews($id);

            $apartmentInfo = Apartment::model()->findByPk($id, array('select' => 'owner_id, phone2'));

            $phone = $apartmentInfo->phone2;

            if ($phone) {
                $image = imagecreate($width, 20);

                $font = Yii::app()->theme->name != 'classic' ? 4 : $font;

                $bg = imagecolorallocate($image, 255, 255, 255);
                $textcolor = imagecolorallocate($image, 37, 75, 137);

                imagestring($image, $font, 0, 0, $phone, $textcolor);

                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Transfer-Encoding: binary');
                header("Content-type: image/png");
                imagepng($image);
                //echo $image;
                imagedestroy($image);
            }
        }
    }

    private function countPhoneViews($id) {

        $model = Apartment::model()->findByPk($id);
        if ($model) {
            $count = $model->count_views;
            $count++;
            $model->count_views = $count;
            $model->save();
        }
    }

	public function actionAllListings() {
		$userId = (int) Yii::app()->request->getParam('id');
		if ($userId) {
			$this->userListingId = $userId;

			$criteria = new CDbCriteria;
			$criteria->addCondition('active = '.Apartment::STATUS_ACTIVE);
			if (param('useUserads'))
				$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);

			//$criteria->order = 't.id ASC';

			$userModel = User::model()->findByPk($userId);
			$userName = $userModel->getNameForType();

            if($userModel->type == User::TYPE_AGENCY){
                $userName = $userModel->getTypeName() . ' "' . $userName .'"';
                $sql = "SELECT id FROM {{users}} WHERE agency_user_id = :user_id AND agent_status=:status";
                $agentsId = Yii::app()->db->createCommand($sql)->queryColumn(array(':user_id' => $userId, ':status' => User::AGENT_STATUS_CONFIRMED));
                $agentsId[] = $userId;
                $criteria->compare('owner_id', $agentsId, false);
            } else {
                $criteria->compare('owner_id', $userId);
            }

			// find count
			$apCount = Apartment::model()->count($criteria);

			if(Yii::app()->request->isAjaxRequest){
				$this->renderPartial('_user_listings', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'username' => $userName,
				), false, true);
			}else{
				$this->render('_user_listings', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'username' => $userName,
				));
			}
		}
	}

	public function actionSendEmail($id, $isFancy = 0){
		$apartment = Apartment::model()->findByPk($id);

		if (!$apartment) {
			throw404();
		}

		if (!param('use_module_request_property'))
			throw404();

		$model = new SendMailForm;

		if(isset($_POST['SendMailForm'])){
			$model->attributes = $_POST['SendMailForm'];

			if(!Yii::app()->user->isGuest){
				$model->senderEmail = Yii::app()->user->email;
				$model->senderName = Yii::app()->user->username;
			}

			$model->ownerId = $apartment->user->id;
			$model->ownerEmail = $apartment->user->email;
			$model->ownerName = $apartment->user->username;

			$model->apartmentUrl = $apartment->getUrl();

			if($model->validate()){
				$notifier = new Notifier;
				$notifier->raiseEvent('onRequestProperty', $model, array('forceEmail' => $model->ownerEmail));

				Yii::app()->user->setFlash('success', tt('Thanks_for_request', 'apartments'));
				$model = new SendMailForm; // clear fields
			} else {
				$model->unsetAttributes(array('verifyCode'));
				Yii::app()->user->setFlash('error', tt('Error_send_request', 'apartments'));
			}
		}

		if($isFancy){
			//Yii::app()->clientscript->scriptMap['*.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;

			$this->renderPartial('send_email', array(
				'apartment' => $apartment,
				'isFancy' => true,
				'model' => $model,
			), false, true);
		}
		else{
			$this->render('send_email', array(
				'apartment' => $apartment,
				'isFancy' => false,
				'model' => $model,
			));
		}
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
			$apartment = $this->loadModel($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = $_POST['lat'];
				$apartment->lng = $_POST['lng'];
				$apartment->save(false);
			}
			Yii::app()->end();
		}
	}

	public function actionGetVideoFile() {
		$id = Yii::app()->request->getParam('id');
		$apId = Yii::app()->request->getParam('apId');

		if ($id && $apId) {
			$sql = 'SELECT video_file, video_html
					FROM {{apartment_video}}
					WHERE id = "'.$id.'"
					AND apartment_id = "'.$apId.'"';

			$result = Yii::app()->db->createCommand($sql)->queryRow();

			if ($result['video_file']) {
				$this->renderPartial('_video_file',
					array(
						'video'=>$result['video_file'],
						'apartment_id' => $apId,
						'id' => $id,
					), false, true
				);
			}
			elseif ($result['video_html']) {
				echo CHtml::decode($result['video_html']);
			}
		}
	}
}
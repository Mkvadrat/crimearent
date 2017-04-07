<?php
	class CustomHttpRequest extends CHttpRequest{
		public function validateCsrfToken($event){
			if($this->getIsPostRequest()){
				$cookies=$this->getCookies();
				if($cookies->contains($this->csrfTokenName) && isset($_POST[$this->csrfTokenName]) || isset($_GET[$this->csrfTokenName] )){
					$tokenFromCookie=$cookies->itemAt($this->csrfTokenName)->value;
					$tokenFrom=!empty($_POST[$this->csrfTokenName]) ? $_POST[$this->csrfTokenName] : $_GET[$this->csrfTokenName];
					$valid=$tokenFromCookie===$tokenFrom;
				}
				else
					$valid=false;
				if(!$valid)
					throw new CHttpException(400,Yii::t('yii','Lite: The CSRF token could not be verified.'));
			}
		}

        public $noCsrfValidationRoutes = array();

        protected function normalizeRequest(){
            parent::normalizeRequest();

            if($_SERVER['REQUEST_METHOD'] != 'POST') return;

            $route = Yii::app()->getUrlManager()->parseUrl($this);
            if($this->enableCsrfValidation){
                foreach($this->noCsrfValidationRoutes as $cr){
                    if(preg_match('#'.$cr.'#', $route)){
                        Yii::app()->detachEventHandler('onBeginRequest',
                            array($this,'validateCsrfToken'));
                        Yii::trace('Route "'.$route.' passed without CSRF validation');
                        break; // found first route and break
                    }
                }
            }
        }
	}
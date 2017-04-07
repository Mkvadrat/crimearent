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

class SeoWidget extends CWidget{

	public $model;
	public $prefixUrl;
    public $canUseDirectUrl = false;

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'seo'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'seo'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.seo.views');
	}

	public function run(){
		if(!param('genFirendlyUrl')){
			return '';
		}

		if(!$this->model){
			return NULL;
		}

		$friendlyUrl = SeoFriendlyUrl::getAndCreateForModel($this->model);

		$this->render('seoWidget', array(
			'model' => $this->model,
			'friendlyUrl' => $friendlyUrl,
		));
	}
}

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

class Sitemap extends CActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment}}';
	}

	public static function publishAssets() {
		$assetsPath = Yii::getPathOfAlias('webroot.themes.'.Yii::app()->theme->name . '.views.modules.sitemap.assets');
		if (is_dir($assetsPath)) {
			$baseUrl = Yii::app()->assetManager->publish($assetsPath);
			Yii::app()->clientScript->registerCssFile($baseUrl . '/module_sitemap.css');
		}
	}
}
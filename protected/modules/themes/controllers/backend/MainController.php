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

class MainController extends ModuleAdminController{
	public $modelName = 'Themes';

	public function actionAdmin(){
		parent::actionAdmin();
	}

	public function actionSetDefault(){
		$id = (int) Yii::app()->request->getPost('id');

		$model = Themes::model()->findByPk($id);
		$model->setDefault();

		// delete assets js cache
		ConfigurationModel::clearGenerateJSAssets();

		Yii::app()->end();
	}
}

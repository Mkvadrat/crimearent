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
	public $modelName = 'News';


	public function actionIndex(){
		$newsPage = Menu::model()->findByPk(Menu::NEWS_ID);
		if ($newsPage) {
			if ($newsPage->active == 0) {
				throw404();
			}
		}

		$model = new $this->modelName;
		$result = $model->getAllWithPagination();

		$this->render('index', array(
			'items' => $result['items'],
			'pages' => $result['pages'],
		));
	}
}
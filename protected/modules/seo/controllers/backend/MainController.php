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

class MainController extends ModuleAdminController{
	public $modelName = 'Seo';

    public function actionAdmin(){
        $model = new $this->modelName('search');
        $model->resetScope();
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET[$this->modelName])){
            $model->attributes = $_GET[$this->modelName];
        }

        $this->render('admin', array('model'=>$model));
    }

	public function actionUpdate($id) {
		$this->redirectTo = array('admin');
		parent::actionUpdate($id);
	}

    public function actionRegenSeo(){

        $modelsAll = SeoFriendlyUrl::model()->findAll();
        $activeLangs = Lang::getActiveLangs();

        foreach($modelsAll as $model){
            foreach($activeLangs as $lang){
                $field = 'url_' . $lang;
                $model->$field = translit($model->$field);
            }

            $model->save();
        }

        echo 'end';
    }
}
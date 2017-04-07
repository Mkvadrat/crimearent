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

class commentListWidget extends CWidget {
	public $model;
	public $url;
	public $showRating = false;

	// TODO
	// уведомление на почту о комментариях
	// Reply

	public function getModelName(){
		return get_class($this->model);
	}

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'comments'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'comments'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.comments.views');
	}

	public function createComment(){
		$comment = new Comment();
		$comment->model_name = $this->getModelName();
		$comment->model_id = $this->getModelId();
		return $comment;
	}

	protected function getModelId() {
		if (is_array($this->model->primaryKey)) {
			return implode('.', $this->model->primaryKey);
		} else {
			return $this->model->primaryKey;
		}
	}

	public function run() {
		$newComment = $this->createComment();
		$comments = $newComment->getCommentsThree();

		$form = new CommentForm();
		$form->url = $this->url;
		$form->modelName = $this->getModelName();
		$form->modelId = $this->getModelId();
		$form->defineShowRating();

		$this->render('commentsListWidget', array(
			'comments' => $comments,
			'newComment' => $newComment,
			'form' => $form,
		));
	}
}
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

class Themes extends ParentModel {
	private static $_defaultTheme;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{themes}}';
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_updated',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function rules() {
		return array(
			array('title, is_default, date_updated', 'required'),
			array('is_default', 'numerical', 'integerOnly' => true),
			array('title', 'length', 'max' => 20),
			array('id, title, is_default, date_updated', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array();
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'title' => tt('title'),
			'is_default' => tt('Is Default'),
			'date_updated' => tc('Last updated on'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('is_default', $this->is_default);
		$criteria->compare('date_updated', $this->date_updated, true);
		$criteria->order = 'title ASC';


		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function beforeSave() {
		if ($this->scenario == 'set_default') {
			$sql = "UPDATE " . $this->tableName() . " SET is_default=0 WHERE id !=" . $this->id;
			Yii::app()->db->createCommand($sql)->execute();
		}

		return parent::beforeSave();
	}

	public static function getDefaultTheme() {
		if (!isset(self::$_defaultTheme)) {
			$sql = "SELECT title FROM {{themes}} WHERE is_default=1";
			self::$_defaultTheme = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return self::$_defaultTheme;
	}

	public function getIsDefaultHtml() {
		if ($this->is_default == 1) {
			$onclick = 'return false;';
		} else {
			$onclick = "changeDefault(" . $this->id . ");";
		}
		return CHtml::radioButton("is_default", ($this->is_default == 1), array('onclick' => $onclick));
	}

	public function setDefault()
	{
		if ($this->is_default) {
			return false;
		}

		$this->scenario = 'set_default';
		$this->is_default = 1;
		$this->update('is_default');

		return true;
	}
}
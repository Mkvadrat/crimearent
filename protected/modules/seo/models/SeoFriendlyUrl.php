<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *    version                :    %TAG%
 *    copyright            :    (c) %YEAR% Monoray
 *    website                :    http://www.monoray.ru/
 *    contact us            :    http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/


/**
 * This is the model class for table "{{seo_friendly_url}}".
 *
 * The followings are the available columns in table '{{seo_friendly_url}}':
 * @property integer $id
 * @property string $model_name
 * @property integer $model_id
 * @property string $url_ru
 * @property string $url_en
 * @property string $title_ru
 * @property string $title_en
 * @property string $description_ru
 * @property string $description_en
 * @property string $keywords_ru
 * @property string $keywords_en
 */
class SeoFriendlyUrl extends ParentModel
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SeoFriendlyUrl the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{seo_friendly_url}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('model_name, model_id', 'required'),
			array('model_id', 'numerical', 'integerOnly'=>true),
			array('model_name', 'length', 'max'=>20),
			array($this->i18nRules('url'), 'match', 'pattern' => '#^[-a-zA-Z0-9_+]{1,50}$#', 'message' => tc('It is allowed to use the characters "-a-zA-Z0-9_+" without spaces')), // допускаются любые символы кроме кириллицы
			array($this->i18nRules('url'), 'validUniqueUrl'),
			array($this->getI18nFieldSafe(), 'length', 'max'=>255),
			array($this->getI18nFieldSafe().', direct_url', 'safe'),
			array('url', 'i18nRequired'),
			array('url_' . Yii::app()->language, 'uniqInLangs'),

			array('id, model_name, model_id', 'safe', 'on'=>'search'),
		);
	}

    public function uniqInLangs($attribute, $params) {
        if(!$this->direct_url){
            return;
        }

        $activeLangs = Lang::getActiveLangs();

        $allValue = array();
        foreach($activeLangs as $lang){
            $field = 'url_'.$lang;
            $allValue[] = $this->$field;
        }

        if(array_diff_assoc($allValue, array_unique($allValue))){
            $this->addError('url', tt('The same URL for different languages', 'seo'));
        }
    }

	public function validUniqueUrl($attribute, $params) {
        $reservedUrl = array(
            'sitemap.xml',
            'yandex_export_feed.xml',
            'version',
            'sell',
            'rent',
            'rss',
        );

		$langs = Lang::getActiveLangs(true);
        $reservedUrl = CMap::mergeArray($langs, $reservedUrl);

        $label = '';
        if(count($langs) > 1){
            $ex = explode('_', $attribute);
            if(isset($ex[1]) && array_key_exists($ex[1], $langs)){
                $label = 'url ' . $langs[$ex[1]]['name'] . ' - ';
            }
        }

        if($this->direct_url && in_array($this->$attribute, $reservedUrl)){
            $this->addError($attribute, $label . tt('This url already exists', 'seo'));
            return false;
        }

        $where = $this->isNewRecord ? '' : ' AND id != '.$this->id;

        $arr = array();
        foreach($langs as $lang){
            $arr[] = 'url_'.$lang['name_iso'].' = :alias';
        }
        $condition = '('.implode(' OR ', $arr).')';

        if($this->direct_url){
            $sql = "SELECT id FROM ".$this->tableName()." WHERE direct_url=1 AND " . $condition . $where;
            $exist = Yii::app()->db->createCommand($sql)
                ->queryScalar(array(
                    ':alias' => $this->$attribute,
                ));
        } else {
            $sql = "SELECT id FROM ".$this->tableName()." WHERE model_name=:model_name AND " . $condition . $where;
            $exist = Yii::app()->db->createCommand($sql)
                ->queryScalar(array(
                    ':alias' => $this->$attribute,
                    ':model_name' => $this->model_name,
                ));
        }

        if($exist){
            $this->addError($attribute, $label . tt('This url already exists', 'seo'));
            return false;
        }

        $this->clearErrors($attribute);
        return true;
	}

	public function i18nFields(){
		return array(
			'url' => 'varchar(255) not null',
			'title' => 'varchar(255) not null',
			'description' => 'varchar(255) not null',
			'keywords' => 'varchar(255) not null',
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'model_name' => 'Model Name',
			'model_id' => 'Model',
			'direct_url' => tt('Direct url', 'seo'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('model_name',$this->model_name,true);
		$criteria->compare('model_id',$this->model_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function setDefault($model){
		$langs = Lang::getActiveLangs();

		$params = CMap::mergeArray(array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'description'
		), $model->seoFields());

		foreach($langs as $lang){
			$fieldTitle = $params['fieldTitle'].'_'.$lang;
			$fieldDescription = $params['fieldDescription'].'_'.$lang;

			$fieldSeoTitle = 'title_'.$lang;
			$fieldSeoDescription = 'description_'.$lang;
			$fieldUrl = 'url_'.$lang;

			if(empty($model->$fieldTitle) || !$model->$fieldTitle){
				return false;
			}

			$translitTitle = translit($model->$fieldTitle) . (param('genUrlWithID', 0) ? '-' . $model->id : '');

			$this->$fieldSeoTitle = $model->$fieldTitle;
			$this->$fieldUrl = $translitTitle;

			// проверяем есть ли такой урл, подбираем уникальный 29 раз
			for($i = 0; $i < 30; $i++){
				if($this->validate($fieldUrl)){
                    break;
                }
                $this->$fieldUrl = $translitTitle . '-' . ($model->id + $i);
            }

			if(isset($model->$fieldDescription)){
				$this->$fieldSeoDescription = utf8_substr(trim(strip_tags($model->$fieldDescription)), 0, 255);
			}
		}

		$this->model_id = $model->id;
		$this->model_name = get_class($model) == 'UserAds' ? 'Apartment' : get_class($model);

		return true;
	}

	private static $_prefixUrlArray = array(
		'Apartment' => 'property/',
		'News' => 'news/',
		'Article' => 'faq/',
		'InfoPages' => 'page/',
	);

	public function getPrefixUrl(){
		return isset(self::$_prefixUrlArray[$this->model_name]) ? self::$_prefixUrlArray[$this->model_name] : '';
	}

	public static $seoLangUrls = array();

	/**
	 * @param $url
	 * @param $modelName
	 * @return SeoFriendlyUrl
	 */
	public static function getForView($url, $modelName){
		if(param('urlExtension')){
			$url = rstrtrim($url, '.html');
		}

		$seo = SeoFriendlyUrl::model()->findByAttributes(array(
			'model_name' => $modelName,
			'url_'.Yii::app()->language => $url
		));

		if($seo){
			$activeLangs = Lang::getActiveLangs();
			foreach($activeLangs as $lang){
				$field = 'url_'.$lang;
				if(isset(self::$_prefixUrlArray[$modelName]) && isset($seo->$field)){
                    $prefix = $seo->direct_url ? '' : $lang . '/' . self::$_prefixUrlArray[$modelName];

					if($seo->$field){
						self::$seoLangUrls[$lang] = Yii::app()->baseUrl . '/' . $prefix . $seo->$field . ( param('urlExtension') ? '.html' : '' );
					}else{
						self::$seoLangUrls[$lang] = Yii::app()->baseUrl . '/' . $prefix . $seo->model_id;
					}
				}
			}
		}

		return $seo;
	}

	private static $_cache;

	public static function getForUrl($id, $modelName) {
        if(!isset(self::$_cache[$modelName][$id])){
            self::$_cache[$modelName][$id] = SeoFriendlyUrl::model()->findByAttributes(array('model_name' => $modelName, 'model_id' => $id));
        }

		return self::$_cache[$modelName][$id];
	}

	public static function getAndCreateForModel($model){
		if(!param('genFirendlyUrl')){
			return false;
		}

		// костылек
		$modelName = get_class($model) == 'UserAds' ? 'Apartment' : get_class($model);

		$friendlyUrl = SeoFriendlyUrl::model()->findByAttributes(array(
			'model_name' => $modelName,
			'model_id' => $model->id
		));

		// Если еще нет, создаем
		if(!$friendlyUrl){
			$friendlyUrl = new SeoFriendlyUrl();

			if($model->id > 0 && $friendlyUrl->setDefault($model)){
				$friendlyUrl->save();
			} else {
				$friendlyUrl->model_name = $modelName;
				$friendlyUrl->model_id = $model->id;
			}

		}

		return $friendlyUrl;
	}
}
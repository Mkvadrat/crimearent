<?php

class ApartmentBanner extends CActiveRecord {
    const LIMIT = 5;
    const BANNER_NONE = 'none';
    const BANNER_LEFT = 'left';
    const BANNER_RIGHT = 'right';
    const WIDTH = 340;
    const HEIGHT = 412;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }


    public function tableName() {
        return '{{apartment_banner}}';
    }


    public function rules() {
        return array(
            array('apartment_id, position', 'required'),
            array('apartment_id', 'numerical', 'integerOnly'=>true)
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'apartment_id' => 'Apartment',
            'position' => 'Banner position',
            'sort_order' => 'Sort order'
        );
    }

    public static function getApartmentBanner($apartment_id) {
        $criteria=new CDbCriteria();
        $criteria->addCondition('apartment_id = :apartment_id');
        $criteria->params[':apartment_id'] = $apartment_id;

        return self::model()->find($criteria);
    }

    public static function getApartmentBannerPosition($apartment_id) {
        $banner = self::getApartmentBanner($apartment_id);
        if (!$banner) return self::BANNER_NONE;

        return $banner->position;
    }

    public static function getBanners($position) {
        $criteria=new CDbCriteria();
        $criteria->addCondition('position = :position');
        $criteria->params[':position'] = $position;
        $criteria->order = 'sort_order ASC, id DESC';
        $criteria->limit = self::LIMIT;

        return self::model()->findAll($criteria);
    }

    public static function getSliderPositionsArray() {
        return array(
            self::BANNER_NONE => tt('Do not show', 'apartments'),
            self::BANNER_LEFT => tt('Left slider', 'apartments'),
            self::BANNER_RIGHT => tt('Right slider', 'apartments')
        );
    }

    public static function savePosition($apartment_id, $position) {
        self::model()->deleteAll('apartment_id = :apartment_id', array(
            ':apartment_id' => $apartment_id
        ));

        if ($position == self::BANNER_LEFT || $position == self::BANNER_RIGHT) {
            $model = new ApartmentBanner();
            $model->setIsNewRecord(true);
            $model->apartment_id = $apartment_id;
            $model->position = $position;
            $model->save();
        }
    }

    public static function remove($apartment_id) {
        self::model()->deleteAll('apartment_id = :apartment_id', array(
            ':apartment_id' => $apartment_id
        ));
    }
}
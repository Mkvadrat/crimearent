<?php

class ApartmentPrice extends CActiveRecord {

    const DATE_START_EMPTY = '1970-01-01';
    const DATE_END_EMPTY = '2038-01-19';
    const DATE_EMPTY_STR = '-';
    const DATE_EMPTY_HTML = '&mdash;';

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }


    public function tableName() {
        return '{{apartment_price}}';
    }


    public function rules() {
        return array(
            array('apartment_id, price, date_start, date_end', 'required'),
            array('apartment_id, price', 'numerical', 'integerOnly'=>true)
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'apartment_id' => 'Apartment',
            'price' => 'Price',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
        );
    }

    public static function getApartmentPrices($apartment_id) {
        $criteria=new CDbCriteria();
        $criteria->addCondition('apartment_id = :apartment_id');
        $criteria->params[':apartment_id'] = $apartment_id;

        return self::model()->findAll($criteria);
    }

    public static function isEmptyDate($time) {
        $empty_start_time = strtotime(self::DATE_START_EMPTY);
        $empty_end_time = strtotime(self::DATE_END_EMPTY);

        return ($time==$empty_start_time || $time==$empty_end_time);
    }

    public static function returnDate($time, $html=true) {
        if (self::isEmptyDate($time)) {
            if ($html) return self::DATE_EMPTY_HTML;
            else return self::DATE_EMPTY_STR;
        }

        return date('d.m.Y', $time);
    }
}
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

class ApartmentsWidgetType extends ApartmentsWidget {
    public $type = 0;
    public $usePagination = 1;
    public $criteria = null;
    public $count = null;
    public $widgetTitle = '';
    public $breadcrumbs = null;
    public $button_text = '';
    public $button_active_registration = '';//иой код

    public function run() {
        if($this->criteria === null){
            $this->criteria = new CDbCriteria;
        }

        $this->criteria->addCondition('t.type = :type');
        $this->criteria->params[':type'] = $this->type;

        Yii::import('application.modules.apartments.helpers.apartmentsHelper');
        $result = apartmentsHelper::getApartments(5, $this->usePagination, 0, $this->criteria, $this->type);

        if (!$this->breadcrumbs) {
            $this->breadcrumbs=array(
                Yii::t('common', 'Apartment search'),
            );
        }

        if($this->count){
            $result['count'] = $this->count;
        }
        else {
            $result['count'] = $result['apCount'];
        }

        $result['type'] = $this->type;
        $result['button_text'] = $this->button_text;
        $result['button_active_registration'] = $this->button_active_registration;

        $this->render('widgetApartments_list_type', $result);
    }
}
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

class apartmentsHelper {
	public static function getApartments($limit = 10, $usePagination = 1, $all = 1, $criteria = null, $type = 0){
		$pages = array();

		Yii::app()->getModule('apartments');

		if($criteria === null){
			$criteria = new CDbCriteria;
		}
		if(!$all){
            if (!Apartment::SHOW_MODERATION_OBJECTS_IN_TIME) {
			$criteria->addCondition('t.active = '.Apartment::STATUS_ACTIVE);
            } else {
            $criteria->addCondition('(t.active = '.Apartment::STATUS_ACTIVE.' OR ( t.active = '.Apartment::STATUS_MODERATION." AND t.date_created > '".date('Y-m-d H:i:s',time()-Apartment::MODERATION_VIEW_TIME)."' ) )");
            }
			if (param('useUserads'))
				$criteria->addCondition('owner_active = '.Apartment::STATUS_ACTIVE);
		}

		$sort = new CSort('Apartment');
		$sort->attributes = array(
			'price' => 'price',
			'date_created' => 'date_created',
		);
		if(!$criteria->order){
			$sort->defaultOrder = 't.date_up_search DESC, t.sorter DESC';
		}
		$sort->applyOrder($criteria);

		$sorterLinks = self::getSorterLinks($sort);
		$criteria->addCondition('t.owner_id = 1 OR t.owner_active = 1');

		$criteria->addInCondition('t.type', Apartment::availableApTypesIds());
		
		// find count
		$apCount = Apartment::model()->count($criteria);

		if($usePagination){
			$pages = new CPagination($apCount);
            if ($type > 0) {
                $pages->pageVar = 'page'.$type;
            }
			$pages->pageSize = $limit;
			$pages->applyLimit($criteria);
		}
		else{
			$criteria->limit = $limit;
		}

        if(issetModule('seo')){
            $criteria->with = array('seo');
        }

//		$apartments = Apartment::model()
//			->cache(param('cachingTime', 1209600), Apartment::getImagesDependency())
//			->with(array('images'))
//			->findAll($criteria);
		return array(
			'pages' => $pages,
			//'apartments' => $apartments,
			'sorterLinks' => $sorterLinks,
			'apCount' => $apCount,
			'criteria' => $criteria
		);
	}

	public static function getSorterLinks($sort){
        $HtmlOption = array('onClick'=>'reloadApartmentList(this.href); return false;');
		$return = array(
			$sort->link('price', tt('Sorting by price', 'quicksearch'), $HtmlOption),
			$sort->link('date_created', tt('Sorting by date created', 'quicksearch'), $HtmlOption),
		);
		return $return;
	}
}

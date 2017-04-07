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

class YandexRealty extends Apartment {
	public static function getActiveAds($where = '') {
		$userAdsCondition = '';
		if (issetModule('userads') && param('useModuleUserAds', 1)) {
			$userAdsCondition = ' AND owner_active = "'.self::STATUS_ACTIVE.'" ';
		}
		$activeAds = Yii::app()->db->createCommand()
			->select('a.id')
			->from('{{apartment}} a')
			->join('{{users}} u', 'a.owner_id = u.id')
			->where('(LENGTH (u.phone) > 0) AND a.active = "'.self::STATUS_ACTIVE.'" '.$userAdsCondition.' '.$where.'')
			->order('a.id DESC')
			->limit('1000')
			->queryColumn();

		return $activeAds;
	}

	public static function getFullDependency($id){
		if ($id) {
			return new CDbCacheDependency('
				SELECT MAX(val) FROM
					(SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "'.(int) $id.'"
					UNION
					SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
					UNION
					SELECT MAX(date_updated) as val FROM {{images}} WHERE id_object = "'.(int) $id.'") as t
			');
		}
		return false;
	}

	public static function getMainData($id) {
		if ($id) {
			$addSelect = '';
			$addSelectJoin = '';
			if (issetModule('location') && param('useLocation', 1)) {
				$addSelect = '
					lc.name_'.Yii::app()->language.' as loc_country_name,
					lr.name_'.Yii::app()->language.' as loc_region_name,
					lcc.name_'.Yii::app()->language.' as loc_city_name,
					ap.loc_country, ap.loc_region, ap.loc_city,
				';
				$addSelectJoin = '
					LEFT JOIN {{location_country}} lc ON lc.id = ap.loc_country
					LEFT JOIN {{location_region}} lr ON lr.id = ap.loc_region
					LEFT JOIN {{location_city}} lcc ON lcc.id = ap.loc_city
				';
			}

			$sql = '
				SELECT ap.id, ap.type, ap.obj_type_id,
				ap.city_id, ap.price, ap.num_of_rooms, ap.floor, ap.floor_total, ap.square, ap.land_square, ap.window_to,
				ap.title_'.Yii::app()->language.', ap.description_'.Yii::app()->language.',
				ap.description_near_'.Yii::app()->language.', ap.address_'.Yii::app()->language.',
				ap.berths, ap.price_type, ap.lat, ap.lng, ap.date_updated, ap.date_created,
			 	'.$addSelect.'
				ac.name_'.Yii::app()->language.' as city_name,
				awt.title_'.Yii::app()->language.' as window_to_name,
				u.phone as owner_phone, u.email as owner_email, u.id as owner_id, u.username as owner_username,
				aop.name_'.Yii::app()->language.' as obj_type_name
				FROM {{apartment}} ap
				'.$addSelectJoin.'
				LEFT JOIN {{apartment_obj_type}} aop ON aop.id = ap.obj_type_id
				LEFT JOIN {{apartment_city}} ac ON ac.id = ap.city_id
				LEFT JOIN {{apartment_window_to}} awt ON awt.id = ap.window_to
				LEFT JOIN {{users}} u ON u.id = ap.owner_id
				WHERE ap.id = "'.(int) $id.'"
				';
			//echo Yii::app()->db->cache(param('cachingTime', 1209600), self::getFullDependency($id))->createCommand($sql)->text;
			$results = Yii::app()->db->cache(param('cachingTime', 1209600), self::getFullDependency($id))->createCommand($sql)->queryRow();
			return $results;
		}
		return false;
	}

	public static function getReferences($apartmentId, $type = Apartment::TYPE_DEFAULT){

		$addWhere = '';
		$addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
		$addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

		$sql = '
			SELECT	reference_values.title_'.Yii::app()->language.' as value,
					reference_values.id as ref_value_id
			FROM	{{apartment_reference}} reference,
					{{apartment_reference_values}} reference_values
			WHERE	reference.apartment_id = "'.intval($apartmentId).'"
					AND reference.reference_value_id = reference_values.id
					'.$addWhere.'
			';

		// Таблица apartment_reference меняется только при измении объявления (т.е. таблицы apartment)
		// Достаточно зависимости от apartment вместо apartment_reference
		$dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "'.intval($apartmentId).'") as t
		');

		$results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

		$return = array();
		foreach($results as $result){
			if(!isset($return[$result['ref_value_id']])){
				$return[$result['ref_value_id']] = $result['value'];
			}
		}
		return $return;
	}

	public static function getImages($id) {
		$sql = '
			SELECT file_name_modified FROM {{images}}
			WHERE id_object = "'.$id.'"
			ORDER BY is_main DESC, sorter DESC
		';
		return Yii::app()->db->createCommand($sql)->queryColumn();
	}
}
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

class MainController extends ModuleUserController{
	public $modelName = 'YandexRealty';
	public $defaultAction = 'viewfeed';
	public $generationDate;
	public $country;
	public $region;
	public $currency;
	public static $squareUnit = 'кв.м';

	# http://help.yandex.ru/webmaster/?id=1113400
	# Сейчас принимаются объявления только о продаже и аренде жилой недвижимости: квартир, комнат, домов и участков.

	# http://help.yandex.ru/realty/actual.xml
	# актуальные объявления

	# проверка файла - http://webmaster.yandex.ru/xsdtest.xml
	# общие условия размещения объявлений - http://help.yandex.ru/webmaster/?id=1113378

	public static $typeApartment = 1; // id типа "квартира" из таблицы {{apartment_obj_type}}
	public static $typeHouse = 2; // id типа "дом" из таблицы {{apartment_obj_type}}
	public static $typeRoom = 0; // если нет такого типа, то оставить 0
	public static $typeLand = 0; // если нет такого типа, то оставить 0

	public function init() {
		# php.ini - date.timezone
		$this->generationDate = date('c', time());

		# если нет модуля "Страна->регион->город" задаём строго
		$this->country = 'Россия';
		$this->region = 'Москва и московская область';

		# валюта
		$this->currency = 'RUR'; # param('siteCurrency', 'RUR');

		if (!isFree()) {
			$activeCurrencyId = Currency::getDefaultValuteId();
			$activeCurrency = Currency::model()->findByPk($activeCurrencyId);
			$this->currency = ($activeCurrency && isset($activeCurrency->char_code)) ? $activeCurrency->char_code : $this->currency;
		}
	}

	public function actionViewFeed() {
		$oldLang = Yii::app()->language;

		Controller::disableProfiler();

		$defaultLangs = Lang::getDefaultLang();
		Yii::app()->language = $defaultLangs;

		// если есть русский или украинский языки, но они не дефолтные. установим на время их.
		if ($defaultLangs != 'ru' || $defaultLangs != 'uk') {
			$allLangs = Lang::getActiveLangs();

			if (array_key_exists('ru', $allLangs))
				Yii::app()->language = 'ru';
			elseif (array_key_exists('uk', $allLangs))
				Yii::app()->language = 'uk';
		}

		$items = $this->generateFeed();

		if (is_array($items) && count($items) > 0) {
			header('Content-type: text/xml');
			header('Pragma: public');
			header('Cache-control: private');
			header('Expires: -1');

			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			$xmlWriter->setIndent(true);
			$xmlWriter->startDocument('1.0', 'UTF-8');

			$xmlWriter->startElement('realty-feed');
			$xmlWriter->writeAttribute('xmlns', 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06');

			$xmlWriter->writeElement('generation-date', $this->generationDate);

			foreach ($items as $item){
				if (isset($item['id'])) {
					$this->prepareItem($item, $xmlWriter);
				}
			}

			$xmlWriter->endElement(); // end realty-feed (xmlns)
			echo $xmlWriter->outputMemory();
		}
		else {
			echo 'no elements';
		}

		// установим обратно пользовательский язык
		Yii::app()->language = $oldLang;
	}

	private function generateFeed() {
		$items = array();
		$where = '';
		// только аренда/продажа
		$where .= ' AND ( a.type = '.YandexRealty::TYPE_RENT.' OR a.type = '.YandexRealty::TYPE_SALE.') ';
		// без "цена договорная" - не поддерживается Яндексом.
		$where .= ' AND (a.is_price_poa = 0) ';


		# http://help.yandex.ru/realty/actual.xml

		# для продажи квартир на вторичке — созданные не более 90 дней назад, либо обновлённые не более 45 дней назад;
		# для длительной аренды квартир — созданные не более 7 дней назад, либо обновлённые не более 14 дней назад;
		# для продажи комнат — созданные не более 120 дней назад, либо обновлённые не более 45 дней назад;
		# для длительной аренды комнат — созданные не более 25 дней назад, либо обновлённые не более 24 дней назад;
		# для длительной аренды домов — созданные не более 30 дней назад, либо обновлённые не более 30 дней назад;
		$where .= '
		AND
		(
			(
				a.obj_type_id = "'.self::$typeApartment.'"
				AND a.type = '.YandexRealty::TYPE_SALE.'
				AND (
					((a.date_created + INTERVAL 90 DAY) > NOW()) OR ((a.date_updated + INTERVAL 45 DAY) > NOW())
				)
			)
			OR
			(
				a.obj_type_id = "'.self::$typeApartment.'"
				AND a.type = '.YandexRealty::TYPE_RENT.'
				AND a.price_type = '.YandexRealty::PRICE_PER_MONTH.'
				AND (
					((a.date_created + INTERVAL 7 DAY) > NOW()) OR ((a.date_updated + INTERVAL 14 DAY) > NOW())
				)
			)
			OR
			(
				a.obj_type_id = "'.self::$typeApartment.'"
				AND a.type = '.YandexRealty::TYPE_RENT.'
				AND ( a.price_type = '.YandexRealty::PRICE_PER_HOUR.' OR a.price_type = '.YandexRealty::PRICE_PER_DAY.' OR a.price_type = '.YandexRealty::PRICE_PER_WEEK.')
			)
			OR
			(
				a.obj_type_id = "'.self::$typeRoom.'"
				AND a.type = '.YandexRealty::TYPE_SALE.'
				AND (
					((a.date_created + INTERVAL 120 DAY) > NOW()) OR ((a.date_updated + INTERVAL 45 DAY) > NOW())
				)
			)
			OR
			(
				a.obj_type_id = "'.self::$typeRoom.'"
				AND a.type = '.YandexRealty::TYPE_RENT.'
				AND a.price_type = '.YandexRealty::PRICE_PER_MONTH.'
				AND (
					((a.date_created + INTERVAL 25 DAY) > NOW()) OR ((a.date_updated + INTERVAL 24 DAY) > NOW())
				)
			)
			OR
			(
				a.obj_type_id = "'.self::$typeRoom.'"
				AND a.type = '.YandexRealty::TYPE_RENT.'
				AND ( a.price_type = '.YandexRealty::PRICE_PER_HOUR.' OR a.price_type = '.YandexRealty::PRICE_PER_DAY.' OR a.price_type = '.YandexRealty::PRICE_PER_WEEK.')
			)
			OR
			(
				a.obj_type_id = "'.self::$typeHouse.'"
				AND a.type = '.YandexRealty::TYPE_RENT.'
				AND a.price_type = '.YandexRealty::PRICE_PER_MONTH.'
				AND (
					((a.date_created + INTERVAL 30 DAY) > NOW()) OR ((a.date_updated + INTERVAL 30 DAY) > NOW())
				)
			)
			OR
			(
				a.obj_type_id = "'.self::$typeHouse.'"
				AND a.type = '.YandexRealty::TYPE_RENT.'
				AND ( a.price_type = '.YandexRealty::PRICE_PER_HOUR.' OR a.price_type = '.YandexRealty::PRICE_PER_DAY.' OR a.price_type = '.YandexRealty::PRICE_PER_WEEK.')
			)
			OR
			(
				a.obj_type_id = "'.self::$typeHouse.'"
				AND a.type = '.YandexRealty::TYPE_SALE.'
			)
			OR
			(
				a.obj_type_id = "'.self::$typeLand.'"
				AND ( a.type = '.YandexRealty::TYPE_SALE.' OR a.type = '.YandexRealty::TYPE_RENT.')
			)
		) ';



		# id активных объявлений
		$activeAds = YandexRealty::getActiveAds($where);

		/*echo '<br>count_find='.count($activeAds);

		$criteria = new CDbCriteria;
		$criteria->addNotInCondition('id', $activeAds);
		$ids = Apartment::model()->findAll($criteria);

		echo '<br>count_not_find='.count($ids);

		foreach ($ids as $ni) {
			echo '<br>'.$ni->getUrl();
		}
		exit;*/

		# если меньше 100, то не выводим xml, а выводим кол-во объявлений
		if (count($activeAds) < 100) {
			echo 'Актуальных объявлений меньше 100. Всего:'.count($activeAds);
			echo '<br><br>Если есть объявления от администратора - проверьте указание телефона, т.к это поле обязательное.';
			echo '<br><br><a href="http://help.yandex.ru/realty/actual.xml" target="_blank">http://help.yandex.ru/realty/actual.xml</a>';
			echo '<br><a href="http://help.yandex.ru/webmaster/?id=1113400" target="_blank">http://help.yandex.ru/webmaster/?id=1113400</a>';

			//echo '<br><br>Если указано, что менее ста валидных объявлений, это не значит, ноль — это тоже меньше ста. Такое сообщение появляется автоматически и при отсутствии контента за последние пять дней.';
			echo '<br><br>Пытаться изменять дату создания на более новую — бессмысленно, поскольку сервис Яндекса запоминает первоначальную дату, с которой объявление попало на сервис.';
			Yii::app()->end();
		}

		if (is_array($activeAds) && count($activeAds) > 0) {
			foreach ($activeAds as $id) {
				$itemInfo = YandexRealty::getMainData($id);
				$itemInfo['images'] = YandexRealty::getImages($id);
				$itemInfo['reference'] = YandexRealty::getReferences($id, $itemInfo['type']); //YandexRealty::getFullInformation($id, $itemInfo['type']);
				$items[] = $itemInfo;
			}
		}

		return $items;
	}

	public function prepareItem($item = array(), $xmlWriter = null) {
		if (count($item) > 0 && $xmlWriter && array_key_exists('type', $item)) {
			/* type */
			if ($item['type'] == YandexRealty::TYPE_RENT)
				$type = 'аренда';
			elseif ($item['type'] == YandexRealty::TYPE_SALE)
				$type = 'продажа';
			else
				return;

			$xmlWriter->startElement('offer');
			$xmlWriter->writeAttribute('internal-id', $item['id']);

				$xmlWriter->writeElement('type', $type);

				/* property-type */
				$category = 'жилая';
				if ($item['obj_type_id'] == self::$typeLand)
					$category = 'нежилая';

				$xmlWriter->writeElement('property-type', $category);

				/* category */
				$xmlWriter->writeElement('category', $item['obj_type_name']);

				/* url */
				$url = YandexRealty::getUrlById($item['id']);
				$xmlWriter->writeElement('url', $url);

				/* creation-date */
				$creationDate = date('c', strtotime($item['date_created']));
				$xmlWriter->writeElement('creation-date', $creationDate);

				/* last-update-date */
				$updateDate = date('c', strtotime($item['date_updated']));
				$xmlWriter->writeElement('last-update-date', $updateDate);

				/* manually-added */
				$xmlWriter->writeElement('manually-added', 1);

				/* location */
				$xmlWriter->startElement('location');
					if (issetModule('location') && param('useLocation', 1)) {
						if ($item['loc_country_name'])
							$xmlWriter->writeElement('country', $item['loc_country_name']);
						if ($item['loc_region_name'])
							$xmlWriter->writeElement('region', $item['loc_region_name']);
						if ($item['loc_city_name'])
							$xmlWriter->writeElement('locality-name', $item['loc_city_name']);
					}
					else {
						$xmlWriter->writeElement('country', $this->country);
						$xmlWriter->writeElement('region', $this->region);
						if ($item['city_name'])
							$xmlWriter->writeElement('locality-name', $item['city_name']);
					}

					if ($item['address_'.Yii::app()->language])
						$xmlWriter->writeElement('address', $item['address_'.Yii::app()->language]);

					if ($item['lat'] && $item['lng']) {
						$xmlWriter->writeElement('latitude', $item['lat']);
						$xmlWriter->writeElement('longitude', $item['lng']);
					}
				$xmlWriter->endElement();

				/* sales info */
				$xmlWriter->startElement('sales-agent');
					if ($item['owner_username'])
						$xmlWriter->writeElement('name', $item['owner_username']);
					if ($item['owner_phone'])
						$xmlWriter->writeElement('phone', $item['owner_phone']);
					if ($item['owner_email'])
						$xmlWriter->writeElement('email', $item['owner_email']);
					$xmlWriter->writeElement('agency-id', $item['owner_id']);
				$xmlWriter->endElement();

				/* price */
				$xmlWriter->startElement('price');
					$xmlWriter->writeElement('value', $item['price']);
					$xmlWriter->writeElement('currency', $this->currency);
					if ($item['type'] == YandexRealty::TYPE_RENT) {
						// только день, месяц
						if ($item['price_type'] == YandexRealty::PRICE_PER_HOUR)
							$xmlWriter->writeElement('period', 'час');
						if ($item['price_type'] == YandexRealty::PRICE_PER_DAY)
							$xmlWriter->writeElement('period', 'день');
						if ($item['price_type'] == YandexRealty::PRICE_PER_WEEK)
							$xmlWriter->writeElement('period', 'неделя');
						if ($item['price_type'] == YandexRealty::PRICE_PER_MONTH)
							$xmlWriter->writeElement('period', 'месяц');
					}
				$xmlWriter->endElement();

				/* images */
				if ($item['images']) {
					if (is_array($item['images']) && count($item['images']) > 0) {
						foreach ($item['images'] as $value) {
							$imageUrl = Yii::app()->getBaseUrl(true).'/uploads/objects/'.$item['id'].'/modified/full_'.$value;
							$xmlWriter->writeElement('image', $imageUrl);
						}
					}
				}

				/* description */
				if($item['description_'.Yii::app()->language]) {
					$xmlWriter->writeElement('description', $item['description_'.Yii::app()->language]);
				}

				/* area */
				if($item['square'] || $item['land_square']) {
					// если участок
					if ($item['obj_type_id'] == self::$typeLand) {
						if ($item['square']) {
							$xmlWriter->startElement('lot-area');
								$xmlWriter->writeElement('value', $item['square']);
								$xmlWriter->writeElement('unit', self::$squareUnit);
							$xmlWriter->endElement();
						}
					}
					else { // комната, квартира, дом
						if ($item['square']) {
							$xmlWriter->startElement('area');
								$xmlWriter->writeElement('value', $item['square']);
								$xmlWriter->writeElement('unit', self::$squareUnit);
							$xmlWriter->endElement();
						}

						if ($item['land_square']) {
							$xmlWriter->startElement('lot-area');
							$xmlWriter->writeElement('value', $item['land_square']);
							$xmlWriter->writeElement('unit', self::$squareUnit);
							$xmlWriter->endElement();
						}
					}
				}

				/* кол-во комнат */
				if ($item['num_of_rooms'])
					$xmlWriter->writeElement('rooms', $item['num_of_rooms']);

				/* кол-во комнат в сделке  */
				if ($item['type'] == YandexRealty::TYPE_RENT || $item['type'] == YandexRealty::TYPE_SALE) {
					if ($item['obj_type_id'] == self::$typeRoom) {
						if ($item['num_of_rooms'])
							$xmlWriter->writeElement('rooms-offered', $item['num_of_rooms']);
						else
							$xmlWriter->writeElement('rooms-offered', 1);
					}
				}

				/* наличие телефона */
				if (isset($item['reference']) && isset($item['reference'][29]))
					$xmlWriter->writeElement('phone', 1);

				/* наличие интернета */
				if (isset($item['reference']) && isset($item['reference'][30]))
					$xmlWriter->writeElement('internet', 1);

				/* наличие телевизора */
				if (isset($item['reference']) && isset($item['reference'][39]))
					$xmlWriter->writeElement('television', 1);

				/* наличие стиральной машины */
				if (isset($item['reference']) && isset($item['reference'][11]))
					$xmlWriter->writeElement('washing-machine', 1);

				/* наличие холодильника */
				if (isset($item['reference']) && isset($item['reference'][27]))
					$xmlWriter->writeElement('refrigerator', 1);

				/* тип санузла */
				if (isset($item['reference']) && isset($item['reference'][10]))
					$xmlWriter->writeElement('bathroom-unit', 'раздельный');
				else
					$xmlWriter->writeElement('bathroom-unit', 'совмещенный');

				/* вид из окон */
				if ($item['window_to']) {
					$xmlWriter->writeElement('window-view', $item['window_to_name']);
				}

				/* этаж */
				if ($item['floor'])
					$xmlWriter->writeElement('floor', $item['floor']);

				/* всего этажей */
				if ($item['floor_total'])
					$xmlWriter->writeElement('floors-total', $item['floor_total']);

				/* для аренды: можно ли с животными */
				if ($item['type'] == YandexRealty::TYPE_RENT) {
					if (isset($item['reference']) && isset($item['reference'][42]))
						$xmlWriter->writeElement('with-pets', 0);
				}


				/* // если участок
				if ($item['obj_type_id'] == self::$typeLand) {
					//
				}
				else {  // комната, квартира, дом
					//
				}*/

			$xmlWriter->endElement(); // end offer
		}
		return;
	}
}
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
	public $modelName = 'Sitemap';
	public $defaultAction = 'index';
	public $changefreq = 'daily';
	public $priority = '0.5';
	public $dateFormat = 'Y-m-d';
	public $isXml = false;
	public $app;
	public $defaultLang;
	public $activeLangs;

	public function init() {
		$this->app = Yii::app();

		$this->showSearchForm = false;

		$this->defaultLang = Yii::app()->language;
		$this->activeLangs = array($this->defaultLang => $this->defaultLang);

		if(!isFree()){
			$this->defaultLang = Lang::getDefaultLang();
			$this->activeLangs = Lang::getActiveLangs();
		}

		parent::init();
	}

	public function actionIndex() {
		if (!$this->isXml) {
			Sitemap::publishAssets();
		}
		$map = $this->generateMap($this->isXml);
		$this->render('index', array('map' => $map));
	}

	public function actionViewXml() {
		Controller::disableProfiler();
		$this->isXml = true;

		$map = $this->generateMap($this->isXml);

		if (is_array($map) && count($map) > 0) {
			header('Content-type: text/xml');
			header('Pragma: public');
			header('Cache-control: private');
			header('Expires: -1');

			$xmlWriter = new XMLWriter();
			$xmlWriter->openMemory();
			$xmlWriter->setIndent(true);
			$xmlWriter->startDocument('1.0', 'UTF-8');
			$xmlWriter->startElement('urlset');
			$xmlWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

			foreach ($map as $item){
				if (isset($item['url'])) {
					$this->prepareItem($item, $xmlWriter, $this->isXml);
				}
				if (isset($item['subsection']) && count($item['subsection']) > 0) {
					foreach ($item['subsection'] as $value) {
						if (isset($value['url'])) {
							$this->prepareItem($value, $xmlWriter, $this->isXml);
						}
						if (isset($value['apartments']) && count($value['apartments']) > 0) {
							foreach ($value['apartments'] as $apartment) {
								if (isset($apartment['url'])) {
									$this->prepareItem($apartment, $xmlWriter, $this->isXml);
								}
							}
						}
					}
				}
			}
			$xmlWriter->endElement(); // end xmlns
			echo $xmlWriter->outputMemory();
		}
		else {
			echo 'no elements';
		}

	}

	public function prepareItem($item = array(), $xmlWriter = null, $isXml = false) {
		if ($isXml) {
			if (count($item) > 0 && $xmlWriter) {
				if (is_string($item['url'])) {

					$xmlWriter->startElement("url");
					$xmlWriter->writeElement('loc', $item['url']);
					if (isset($item['lastmod']))  {
						$xmlWriter->writeElement('lastmod', $item['lastmod']);
					}
					$xmlWriter->writeElement('changefreq', $this->changefreq);
					$xmlWriter->writeElement('priority', $this->priority);
					$xmlWriter->endElement(); // end url
				}
				elseif (is_array($item['url'])) {
					foreach ($item['url'] as $keyUrl => $valUrl) {
						if (isset($item['url'][$keyUrl]) && !empty($item['url'][$keyUrl])) {
							$xmlWriter->startElement("url");

							$xmlWriter->writeElement('loc', $item['url'][$keyUrl]);
							if (isset($item['lastmod'][$keyUrl]))  {
								$xmlWriter->writeElement('lastmod', $item['lastmod'][$keyUrl]);
							}
							$xmlWriter->writeElement('changefreq', $this->changefreq);
							$xmlWriter->writeElement('priority', $this->priority);

							$xmlWriter->endElement(); // end url
						}
					}
				}
			}
		}
		else {
			if (count($item) > 0 && $xmlWriter) {
				$xmlWriter->startElement("url");
				$xmlWriter->writeElement('loc', $item['url']);
				if (isset($item['lastmod']))  {
					$xmlWriter->writeElement('lastmod', $item['lastmod']);
				}
				$xmlWriter->writeElement('changefreq', $this->changefreq);
				$xmlWriter->writeElement('priority', $this->priority);
				$xmlWriter->endElement(); // end url
			}
		}

		return;
	}

	private function generateMap($isXml = false) {
		$map = array();
		$defaultLastMod = date($this->dateFormat, time());

		$articleAll = $menuAll = $newsAll = '';

		// apartments module
		if (issetModule('apartments')) {
			if ($isXml) {
				$dependencyApartment = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');
			}
		}

		// article module
		if (issetModule('articles')) {
			$articlePage = Menu::model()->findByPk(Menu::ARTICLES_ID);

			if ($articlePage && $articlePage->active == 1) {
				Yii::import('application.modules.articles.models.Article');

				$dependencyArticle = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{articles}}');
				$articleAll = Article::model()->cache(param('cachingTime', 1209600), $dependencyArticle)->findAll(array(
					'condition' => 'active = 1',
				));

				if ($isXml) {
					$sql = 'SELECT MAX(date_updated) as date_updated FROM {{articles}}';
					$maxUpdatedArticles = Yii::app()->db->createCommand($sql)->queryRow();
					$maxUpdatedArticles = isset($maxUpdatedArticles['date_updated']) ? date($this->dateFormat, strtotime($maxUpdatedArticles['date_updated'])) : $defaultLastMod;
				}
			}
		}

		// infopages module
		if (issetModule('menumanager')) {
			$dependencyInfoPages = new CDbCacheDependency('SELECT MAX(date_updated) as date_updated FROM {{menu}}');
			$menuAll = Menu::model()->cache(param('cachingTime', 1209600), $dependencyInfoPages)->findAll(array(
				'order' => 'number',
				'condition' => 'active = 1 AND (special = 0 OR id = 5)',
			));

			if ($isXml) {
				$sql = 'SELECT MAX(date_updated) as date_updated FROM {{menu}}';
				$maxUpdatedInfo = Yii::app()->db->createCommand($sql)->queryRow();
				$maxUpdatedInfo = isset($maxUpdatedInfo['date_updated']) ? date($this->dateFormat, strtotime($maxUpdatedInfo['date_updated'])) : $defaultLastMod;
			}
		}

		// news module
		if (issetModule('news')) {
			$newsPage = Menu::model()->findByPk(Menu::NEWS_ID);

			if ($newsPage && $newsPage->active == 1) {
				Yii::import('application.modules.news.models.News');

				$dependencyNews = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{news}}');
				$newsAll = News::model()->cache(param('cachingTime', 1209600), $dependencyNews)->findAll();

				if ($isXml) {
					$sql = 'SELECT MAX(date_updated) as date_updated FROM {{news}}';
					$maxUpdatedNews = Yii::app()->db->createCommand($sql)->queryRow();
					$maxUpdatedNews = isset($maxUpdatedNews['date_updated']) ? date($this->dateFormat, strtotime($maxUpdatedNews['date_updated'])) : $defaultLastMod;
				}
			}
		}


		####################################### index page #######################################
		if ($isXml) {
			if ($this->activeLangs && is_array($this->activeLangs)) {
				foreach ($this->activeLangs as $keyLang => $valLang) {
					$this->app->setLanguage($valLang);

					$map['index_page']['title'][$keyLang] = tt('index_page');
					$map['index_page']['url'][$keyLang] = Yii::app()->createAbsoluteUrl('/');
					$map['index_page']['lastmod'][$keyLang] = (isset($indexPageInfo) && isset($indexPageInfo->date_updated)) ? date($this->dateFormat, strtotime($indexPageInfo->date_updated)) : $defaultLastMod;
				}
			}
			$this->app->setLanguage($this->defaultLang);
		}
		else {
			$map['index_page']['title'] = tt('index_page');
			$map['index_page']['url'] = Yii::app()->createAbsoluteUrl('/');
		}


		####################################### contact form an booking form #######################################
		if (Yii::app()->user->getState('isAdmin')===null) {
			if ($isXml) {
				if ($this->activeLangs && is_array($this->activeLangs)) {
					foreach ($this->activeLangs as $keyLang => $valLang) {
						$this->app->setLanguage($valLang);

						$map['contact_form']['title'][$keyLang] = tt('contact_form');
						$map['contact_form']['url'][$keyLang] = Yii::app()->createAbsoluteUrl('contactform/main/index');
						$map['contact_form']['lastmod'][$keyLang] = (isset($indexPageInfo) && isset($indexPageInfo->date_updated)) ? date($this->dateFormat, strtotime($indexPageInfo->date_updated)) : $defaultLastMod;

						$map['booking_form']['title'][$keyLang] = tt('booking_form');
						$map['booking_form']['url'][$keyLang] = Yii::app()->createAbsoluteUrl('booking/main/mainform');
						$map['booking_form']['lastmod'][$keyLang] = (isset($indexPageInfo) && isset($indexPageInfo->date_updated)) ? date($this->dateFormat, strtotime($indexPageInfo->date_updated)) : $defaultLastMod;
					}
				}
				$this->app->setLanguage($this->defaultLang);
			}
			else {
				$map['contact_form']['title'] = tt('contact_form');
				$map['contact_form']['url'] = Yii::app()->createAbsoluteUrl('contactform/main/index');

				$map['booking_form']['title'] = tt('booking_form');
				$map['booking_form']['url'] = Yii::app()->createAbsoluteUrl('booking/main/mainform');
			}
		}

		####################################### search #######################################
		if ($isXml) {
			if ($this->activeLangs && is_array($this->activeLangs)) {
				foreach ($this->activeLangs as $keyLang => $valLang) {
					$this->app->setLanguage($valLang);

					$map['quick_search']['title'][$keyLang] = tt('quick_search');
					$map['quick_search']['url'][$keyLang] = Yii::app()->createAbsoluteUrl('quicksearch/main/mainsearch');

					$sql = 'SELECT MAX(date_updated) as date_updated FROM {{apartment}}';
					$maxUpdatedApartment = Yii::app()->db->createCommand($sql)->queryRow();
					$maxUpdatedApartment = isset($maxUpdatedApartment['date_updated']) ? date($this->dateFormat, strtotime($maxUpdatedApartment['date_updated'])) : $defaultLastMod;

					$map['quick_search']['lastmod'][$keyLang] = $maxUpdatedApartment;
				}
			}
			$this->app->setLanguage($this->defaultLang);
		}
		else {
			$map['quick_search']['title'] = tt('quick_search');
			$map['quick_search']['url'] = Yii::app()->createAbsoluteUrl('quicksearch/main/mainsearch');
		}


		####################################### search subtypes #######################################
		$types = SearchForm::apTypes();
		if (is_array($types) && isset($types['propertyType'])) {
			$i = 0;
			foreach ($types['propertyType'] as $key => $value) {
				if ($key > 0) {
					if ($isXml) {
						//$map['apartment_types'][$i]['title'] = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
						/*$map['quick_search']['subsection'][$i]['title'] = $value;
						$map['quick_search']['subsection'][$i]['url'] = Yii::app()->createAbsoluteUrl('quicksearch/main/mainsearch', array('apType' => $key));
						$map['quick_search']['subsection'][$i]['lastmod'] = $maxUpdatedApartment;*/

						$criteria = new CDbCriteria();

						$criteria->compare('price_type', $key);
						$criteria->compare('active', Apartment::STATUS_ACTIVE);
						$criteria->compare('owner_active', Apartment::STATUS_ACTIVE);
						$criteria->order = 'date_updated DESC';

						$criteria->select = 'date_updated';

						if($this->activeLangs){
							foreach($this->activeLangs as $lang){
								$criteria->select .= ',title_'.$lang;
							}
						}
						$criteria->with = array('seo');

						$apartmentsByType = Apartment::model()->cache(param('cachingTime', 1209600), $dependencyApartment)->findAll($criteria);

						$k = 0;
						if (is_array($apartmentsByType) && count($apartmentsByType) > 0) {
							foreach ($apartmentsByType as $value) {
								if ($this->activeLangs && is_array($this->activeLangs)) {
									foreach ($this->activeLangs as $keyLang => $valLang) {
										$this->app->setLanguage($valLang);

										$map['quick_search']['subsection'][$i]['apartments'][$k]['title'][$keyLang] = $value->getStrByLang('title');
										$map['quick_search']['subsection'][$i]['apartments'][$k]['url'][$keyLang] = $value->getRelationUrl();
										$map['quick_search']['subsection'][$i]['apartments'][$k]['lastmod'][$keyLang] = date($this->dateFormat, strtotime($value['date_updated']));
									}
								}
								$this->app->setLanguage($this->defaultLang);
								$k++;
							}
						}
					}
					else {
						//$map['apartment_types'][$i]['title'] = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
						$map['quick_search']['subsection'][$i]['title'] = $value;
						$map['quick_search']['subsection'][$i]['url'] = Yii::app()->createAbsoluteUrl('quicksearch/main/mainsearch', array('apType' => $key));
					}
					$i++;
				}
			}
		}

		####################################### search object types #######################################
		$objTypes = Apartment::getObjTypesArray();
		if (is_array($objTypes)) {
			$i = 1;
			if (array_key_exists('subsection',$map['quick_search'])) {
				if ($isXml) {
					$countSubsection = count($map['quick_search']['subsection']);
					if ($this->activeLangs && is_array($this->activeLangs)) {
						foreach ($this->activeLangs as $keyLang => $valLang) {
							foreach ($objTypes as $key => $value) {
								$this->app->setLanguage($valLang);

								$map['quick_search']['subsection'][$countSubsection+$i]['title'][$keyLang] = $value;
								$map['quick_search']['subsection'][$countSubsection+$i]['url'][$keyLang] = Yii::app()->createAbsoluteUrl('quicksearch/main/mainsearch', array('objType' => $key));
								$i++;
							}
						}
						$this->app->setLanguage($this->defaultLang);
					}
				}
				else {
					$countSubsection = count($map['quick_search']['subsection']);
					foreach ($objTypes as $key => $value) {
						$map['quick_search']['subsection'][$countSubsection+$i]['title'] = $value;
						$map['quick_search']['subsection'][$countSubsection+$i]['url'] = Yii::app()->createAbsoluteUrl('quicksearch/main/mainsearch', array('objType' => $key));
						$i++;
					}
				}
			}
			// no in xml because all links to listings generated above in search subtypes section
			// duplication link is not needed.
		}


		####################################### special offers  #######################################
		if (issetModule('specialoffers')) {
			$specialOfferPage = Menu::model()->findByPk(Menu::SPECIALOFFERS_ID);

			if ($specialOfferPage && $specialOfferPage->active == 1) {
				$i = 0;

				if ($isXml) {
					$map['special_offers']['title'] = tt('special_offers');
					$map['special_offers']['url'] = Yii::app()->createAbsoluteUrl('specialoffers/main/index');
					$map['special_offers']['lastmod'] = $maxUpdatedApartment;

					$specialOffers = Apartment::model()->cache(param('cachingTime', 1209600), $dependencyApartment)->findAllByAttributes(array('is_special_offer' => 1), 'active = :active AND owner_active = :ownerActive', array(':active' => Apartment::STATUS_ACTIVE, ':ownerActive' => Apartment::STATUS_ACTIVE));
					$k = 0;
					if (is_array($specialOffers) && count($specialOffers) > 0) {
						foreach ($specialOffers as $value) {
							if ($this->activeLangs && is_array($this->activeLangs)) {
								foreach ($this->activeLangs as $keyLang => $valLang) {
									$this->app->setLanguage($valLang);

									$map['special_offers']['subsection'][$k]['title'][$keyLang] = $value->getStrByLang('title');
									$map['special_offers']['subsection'][$k]['url'][$keyLang] = $value->getUrl();
									$map['special_offers']['subsection'][$k]['lastmod'][$keyLang] = date($this->dateFormat, strtotime($value['date_updated']));
								}
							}
							$this->app->setLanguage($this->defaultLang);
							$k++;
						}
					}
				}
				else {
					$map['special_offers']['title'] = tt('special_offers');
					$map['special_offers']['url'] = Yii::app()->createAbsoluteUrl('specialoffers/main/index');
				}
			}
		}


		####################################### get all info pages  #######################################
		if (is_array($menuAll) && $menuAll > 0) {
			$i = 0;

			if ($isXml) {
				if ($this->activeLangs && is_array($this->activeLangs)) {
					foreach ($this->activeLangs as $keyLang => $valLang) {
						$this->app->setLanguage($valLang);

						$map['section_infopage']['title'][$keyLang] = tt('section_infopage');
						$map['section_infopage']['url'][$keyLang] = null;
						$map['section_infopage']['lastmod'][$keyLang] = $maxUpdatedInfo;

						foreach ($menuAll as $value) {
							// убираем из карты сайта типы "Простая ссылка" и "Простая ссылка в выпад. списке"

							if ($value['type'] != Menu::LINK_NEW_MANUAL && $value['type'] != Menu::LINK_NONE) {
								$title = $value->getTitle();
								if ($title && $value['id'] != 1) {
									$map['section_infopage']['subsection'][$i]['title'][$keyLang] = $title;

									if($value['type'] == Menu::LINK_NEW_INFO){
										$href = $value->getUrl();
									} else {
										if($value['id'] == Menu::SITEMAP_ID){ // sitemap
											$href = Yii::app()->controller->createAbsoluteUrl('/sitemap/main/index');
										}
									}

									$map['section_infopage']['subsection'][$i]['url'][$keyLang] = $href;
									$map['section_infopage']['subsection'][$i]['lastmod'][$keyLang] = date($this->dateFormat, strtotime($value['date_updated']));

									$i++;
								}
							}
						}
					}
				}
				$this->app->setLanguage($this->defaultLang);
			}
			else {
				$map['section_infopage']['title'] = tt('section_infopage');
				$map['section_infopage']['url'] = null;

				foreach ($menuAll as $value) {
					$title = $value->getTitle();
					if ($title && $value['id'] != Menu::MAIN_PAGE_ID && $value['type'] != Menu::LINK_NONE) {
						$map['section_infopage']['subsection'][$i]['title'] = $title;

						if($value['type'] == Menu::LINK_NEW_INFO){
							$href = $value->getUrl();
						} else {
							if($value['id'] == Menu::SITEMAP_ID){ // sitemap
								$href = Yii::app()->controller->createAbsoluteUrl('/sitemap/main/index');
							}
						}

						$map['section_infopage']['subsection'][$i]['url'] = $href;
						$i++;
					}
				}
			}
		}

		####################################### get all news #######################################
		if (is_array($newsAll) && count($newsAll) > 0) {
			$i = 0;

			if ($isXml) {
				if ($this->activeLangs && is_array($this->activeLangs)) {
					foreach ($this->activeLangs as $keyLang => $valLang) {
						$this->app->setLanguage($valLang);

						$map['section_news']['title'][$keyLang] = tt('section_news');
						$map['section_news']['url'][$keyLang] = Yii::app()->createAbsoluteUrl('news/main/index');
						$map['section_news']['lastmod'][$keyLang] = $maxUpdatedNews;

						foreach ($newsAll as $value) {
							$title = $value->getTitle();
							if ($title) {
								$map['section_news']['subsection'][$i]['title'][$keyLang] = $title;
								$map['section_news']['subsection'][$i]['url'][$keyLang] = $value->getUrl();
								$map['section_news']['subsection'][$i]['lastmod'][$keyLang] = date($this->dateFormat, strtotime($value['date_updated']));
								$i++;
							}
						}
					}
				}
				$this->app->setLanguage($this->defaultLang);
			}
			else {
				$map['section_news']['title'] = tt('section_news');
				$map['section_news']['url'] = Yii::app()->createAbsoluteUrl('news/main/index');

				foreach ($newsAll as $value) {
					$title = $value->getTitle();
					if ($title) {
						$map['section_news']['subsection'][$i]['title'] = $title;
						$map['section_news']['subsection'][$i]['url'] = $value->getUrl();
						$i++;
					}
				}
			}
		}


		####################################### get all article #######################################
		if (is_array($articleAll) && count($articleAll) > 0) {
			$i = 0;

			if ($isXml) {
				if ($this->activeLangs && is_array($this->activeLangs)) {
					foreach ($this->activeLangs as $keyLang => $valLang) {
						$this->app->setLanguage($valLang);

						$map['section_article']['title'] = tt('section_article');
						$map['section_article']['url'] = Yii::app()->createAbsoluteUrl('articles/main/index');
						$map['section_article']['lastmod'] = $maxUpdatedArticles;

						foreach ($articleAll as $value) {
							$title = $value->getPage_title();
							if ($title) {
								$map['section_article']['subsection'][$i]['title'] = $title;
								$map['section_article']['subsection'][$i]['url'] = $value->getUrl();
								$map['section_article']['subsection'][$i]['lastmod'] = date($this->dateFormat, strtotime($value['date_updated']));
								$i++;
							}
						}
					}
				}
				$this->app->setLanguage($this->defaultLang);
			}
			else {
				$map['section_article']['title'] = tt('section_article');
				$map['section_article']['url'] = Yii::app()->createAbsoluteUrl('articles/main/index');

				foreach ($articleAll as $value) {
					$title = $value->getPage_title();
					if ($title) {
						$map['section_article']['subsection'][$i]['title'] = $title;
						$map['section_article']['subsection'][$i]['url'] = $value->getUrl();
						$i++;
					}
				}
			}
		}

		####################################### reviews  #######################################
		if (issetModule('reviews')) {
			$reviewsPage = Menu::model()->findByPk(Menu::REVIEWS_ID);

			if ($reviewsPage && $reviewsPage->active == 1) {
				$i = 0;

				if ($isXml) {
					$sql = 'SELECT MAX(date_updated) as date_updated FROM {{reviews}}';
					$maxUpdatedReviews = Yii::app()->db->createCommand($sql)->queryScalar();

					if ($this->activeLangs && is_array($this->activeLangs)) {
						foreach ($this->activeLangs as $keyLang => $valLang) {
							$this->app->setLanguage($valLang);

							$map['reviews']['title'][$keyLang] = tt('Reviews', 'reviews');
							$map['reviews']['url'][$keyLang] = Yii::app()->createAbsoluteUrl('reviews/main/index');
							$map['reviews']['lastmod'][$keyLang] = $maxUpdatedReviews;
						}
					}
					$this->app->setLanguage($this->defaultLang);

				}
				else {
					$map['reviews']['title'] = tt('Reviews', 'reviews');
					$map['reviews']['url'] = Yii::app()->createAbsoluteUrl('reviews/main/index');
				}
			}
		}

		return $map;
	}
}
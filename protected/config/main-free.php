<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

require_once(dirname(__FILE__) . '/../helpers/common.php');
require_once(dirname(__FILE__) . '/../helpers/strings.php');
/*require_once(dirname(__FILE__) . '/../helpers/HUser.php');
require_once(dirname(__FILE__) . '/../helpers/HDate.php');
require_once(dirname(__FILE__) . '/../helpers/HAjax.php');
require_once(dirname(__FILE__) . '/../helpers/HApartment.php');*/
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');

$config = array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'Open Real Estate',

	'sourceLanguage' => 'en',
	'language' => 'ru',

	'theme' => 'classic',

	'preload' => array(
		'log',
		'configuration', // preload configuration
	),

	'onBeginRequest' => array('BeginRequest', 'updateStatusAd'),

	// autoloading model and component classes
	'import' => array(
		'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.*',
		'ext.eauth.services.*',
		'ext.eauth.custom_services.CustomGoogleService',
		'ext.eauth.custom_services.CustomVKService',
		'ext.eauth.custom_services.CustomFBService',
		'ext.eauth.custom_services.CustomTwitterService',
		'ext.eauth.custom_services.CustomMailruService',
		'ext.setReturnUrl.ESetReturnUrlFilter',

		'application.models.*',
		'application.components.*',
		'application.helpers.*',

		'application.modules.configuration.components.*',
		'application.modules.notifier.components.Notifier',
		'application.modules.booking.models.*',

		'application.modules.comments.models.Comment',
		'application.modules.comments.models.CommentForm',
		'application.modules.windowto.models.WindowTo',
		'application.modules.apartments.models.*',
		'application.modules.news.models.*',
		'application.extensions.image.Image',
		'application.modules.selecttoslider.models.SelectToSlider',
		'application.modules.similarads.models.SimilarAds',
		'application.modules.menumanager.models.Menu',
		'application.modules.windowto.models.WindowTo',
		'application.modules.apartments.components.*',
		'application.modules.apartmentCity.models.ApartmentCity',
		'application.modules.apartmentObjType.models.ApartmentObjType',
		'application.modules.translateMessage.models.TranslateMessage',

		'application.components.behaviors.ERememberFiltersBehavior',
		'application.modules.service.models.Service',

		'application.modules.socialauth.models.SocialauthModel',
		'application.modules.antispam.components.MathCCaptchaAction',

		'application.modules.images.models.*',
		'application.modules.images.components.*',

		'application.modules.lang.models.Lang',

		'zii.behaviors.CTimestampBehavior',
		'application.modules.apartmentsComplain.models.ApartmentsComplain',
		'application.modules.apartmentsComplain.models.ApartmentsComplainReason',
		'application.modules.comparisonList.models.ComparisonList',
		'application.modules.articles.models.Article',
		'application.modules.infopages.models.InfoPages',
		'application.modules.reviews.models.Reviews',
		'application.modules.bookingtable.models.Bookingtable',
		'application.modules.themes.models.Themes',
		'application.components.oldbrowsers.CheckBrowser',
		'application.modules.clients.models.Clients',

		'application.modules.formdesigner.models.*',
		'application.modules.seo.models.*',
		'application.modules.sitemap.models.Sitemap',
		'application.modules.socialposting.models.SocialpostingModel',
		'application.modules.socialposting.components.*',
		'application.modules.yandexRealty.models.YandexRealty',
	),

	'modules' => array(
		'news',
		'referencecategories',
		'referencevalues',
		'apartments',
		'apartmentObjType',
		'apartmentCity',
		'comments',
		'booking',
		'windowto',
		'contactform',
		'articles',
		'usercpanel',
		'users',
		'quicksearch',
		'configuration',
		'timesin',
		'timesout',
		'adminpass',
		'specialoffers',
		'install',
		'selecttoslider',
		'similarads',
		'menumanager',
		'userads',
		'translateMessage',
		'service',
		'socialauth',
		'antispam',
		'rss',
		'images',
		'apartmentsComplain',
		'formdesigner',
		'comparisonList',
		'guestad',
		'reviews',
		'bookingtable',
		'modules',
		'infopages',
		'themes',
		'notifier',
		'clients',
		'seo',
		'sitemap',
		'socialposting',
		'yandexRealty',

		// uncomment the following to enable the Gii tool
		/*'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin1',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
			'generatorPaths'=>array(
				'bootstrap.gii', // since 0.9.1
			),
		),*/

	),

	'controllerMap'=>array(
		'min'=>array(
			'class'=>'ext.minScript.controllers.ExtMinScriptController',
		),
	),

	'components' => array(
		'loid' => array(
			'class' => 'application.extensions.lightopenid.loid',
		),
		'eauth' => array(
			// yii-eauth-1.1.8
			'class' => 'ext.eauth.EAuth',
			'popup' => true, // Use popup windows instead of redirect to site of provider
		),

		'user' => array(
			// enable cookie-based authentication
			'allowAutoLogin' => true,
		),

		'configuration' => array(
			'class' => 'Configuration',
			'cachingTime' => 0, // caching configuration for 180 days
		),

		'cache' => array(
			'class' => 'system.caching.CFileCache',
			/*'class'=>'system.caching.CMemCache',
			//'useMemcached' => true,
			'servers'=>array(
				array('host'=>'127.0.0.1', 'port'=>11211),
			),*/
		),

		'request'=>array(
			'class' => 'application.components.CustomHttpRequest',
			'enableCsrfValidation'=>true,
		),

		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'class'=>'application.components.CustomUrlManager',
		),

		'mailer' => array(
			'class' => 'application.extensions.mailer.EMailer',
		),

		//'db'=>require(dirname(__FILE__) . '/db.php'),

		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
//		'log'=>array(
//			'class'=>'CLogRouter',
//			'routes'=>array(
//				array(
//					'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
//					'ipFilters'=>array('127.0.0.1'),
//				),
//			),
//		),
		/*'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages

				//array(
				//	'class'=>'CWebLogRoute',
				//),
			),
		),*/
		'messages' => array(
			'class' => 'DbMessageSource',
			'forceTranslation' => true,
			'onMissingTranslation' => array('CustomEventHandler', 'handleMissingTranslation'),
		),

		'messagesInFile' => array(
			'class' => 'CPhpMessageSource',
			'forceTranslation' => true,
		),

		'bootstrap'=>array(
			'class'=>'bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
		),
	),

	'params' => array(
		'module_rss_itemsPerFeed' => 20,
		'allowedImgExtensions' => array('jpg', 'jpeg', 'gif', 'png'),
		'maxImgFileSize' => 8 * 1024 * 1024, // maximum file size in bytes
		'minImgFileSize' => 5 * 1024, // min file size in bytes
		'langToInstall' => 'ru',
		'countListingsInComparisonList' => 6, # максимум объявлений в списке сравнения
		'searchMaxField' => 15, // максимальное кол-во полей в поиске,
		'useMinify' => true,
		'useLangPrefixIfOneLang' => 0, // использовать префикс языка в url если активен только 1 язык
	),
);

$addons['components'] = array(
	'session' => array(
		'class' => 'CDbHttpSession',
		'connectionID' => 'db',
		'sessionTableName' => '{{users_sessions}}',
		'autoCreateSessionTable' => false, //!!!
	),
	'clientScript'=>array(
		'class'=>'ext.minScript.components.ExtMinScript',
		'minScriptLmCache' => (YII_DEBUG) ? 0 : 3600,
		'minScriptDisableMin' => array('/[-\.]min\.(?:js|css)$/i', '/bootstrap.js$/i', '/jquery.js$/i', '/ckeditor.js$/i', '/[-\.]pack\.(?:js|css)$/i'),
	),
);

$addons['import'] = array(
	'application.modules.configuration.models.ConfigurationModel',
);

if(oreInstall::isInstalled()){
	$config = CMap::mergeArray($config, $addons);
}

$db = require(dirname(__FILE__) . '/db.php');
if($db === 1){
	$db = array();
}

return CMap::mergeArray($config, $db);

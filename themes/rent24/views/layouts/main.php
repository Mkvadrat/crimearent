<!DOCTYPE html>
<?php
/*$nameRFC3066 = 'ru-ru';
$allLangs = Lang::getActiveLangs(true);
if ($allLangs) {
	$nameRFC3066 = (array_key_exists(Yii::app()->language, $allLangs) && array_key_exists('name_rfc3066', $allLangs[Yii::app()->language])) ? $allLangs[Yii::app()->language]['name_rfc3066'] : 'ru-ru';
}
$nameRFC3066 = utf8_strtolower($nameRFC3066);
*/
$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->baseUrl;
$baseThemeUrl = Yii::app()->theme->baseUrl;
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language;?>" lang="<?php echo Yii::app()->language;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1024, target-densityDpi=device-dpi">

    <title><?php echo CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle); ?></title>
    <meta name="description" content="<?php echo CHtml::encode($this->seoDescription ? $this->seoDescription : $this->pageDescription); ?>" />
    <meta name="keywords" content="<?php echo CHtml::encode($this->seoKeywords ? $this->seoKeywords : $this->pageKeywords); ?>" />

    <link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/print.css" media="print" />
    <!--<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/form.css" />-->
    <link media="screen, projection" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/styles.css" rel="stylesheet" />

    <!--[if IE]> <link href="<?php echo $baseThemeUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->

    <link rel="icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon" />

    <?php
    $cs->scriptMap['jquery-ui.min.js'] = false;

    $cs->registerCoreScript('jquery');
    //$cs->registerCoreScript('jquery.ui');

    $cs->registerCssFile($baseThemeUrl . '/bootstrap/css/bootstrap.min.css');
    $cs->registerCssFile($baseThemeUrl . '/jquery-ui/jquery-ui.min.css');
    //$cs->registerScriptFile($baseThemeUrl . '/js/jquery.min.js');
    $cs->registerScriptFile($baseThemeUrl . '/bootstrap/js/bootstrap.min.js');
    $cs->registerScriptFile($baseThemeUrl . '/jquery-ui/jquery-ui-custom.min.js');


    $cs->registerCoreScript('rating');
    $cs->registerCssFile($cs->getCoreScriptUrl().'/rating/jquery.rating.css');
    $cs->registerCssFile($baseThemeUrl . '/css/ui/jquery-ui.multiselect.css');
    $cs->registerCssFile($baseThemeUrl . '/css/redmond/jquery-ui-1.7.1.custom.css');
    $cs->registerCssFile($baseThemeUrl . '/css/ui.slider.extras.css');
    $cs->registerScriptFile($baseThemeUrl . '/js/jquery.multiselect.min.js');
    $cs->registerCssFile($baseThemeUrl . '/css/ui/jquery-ui.multiselect.css');
    $cs->registerScriptFile($baseThemeUrl . '/js/jquery.dropdownPlain.js', CClientScript::POS_HEAD);
    $cs->registerScriptFile($baseThemeUrl . '/js/common.js', CClientScript::POS_HEAD);
    $cs->registerScriptFile($baseThemeUrl . '/js/habra_alert.js', CClientScript::POS_END);
    $cs->registerScriptFile($baseThemeUrl . '/js/jquery.cookie.js', CClientScript::POS_END);
    $cs->registerScriptFile($baseThemeUrl . '/js/scrollto.js', CClientScript::POS_END);
    $cs->registerCssFile($baseThemeUrl.'/css/form.css', 'screen, projection');

    // superfish menu
    $cs->registerCssFile($baseThemeUrl.'/js/superfish/css/superfish.css', 'screen');
    $cs->registerCssFile($baseThemeUrl.'/js/superfish/css/superfish-vertical.css', 'screen');
    $cs->registerScriptFile($baseThemeUrl.'/js/superfish/js/hoverIntent.js', CClientScript::POS_HEAD);
    $cs->registerScriptFile($baseThemeUrl.'/js/superfish/js/superfish.js', CClientScript::POS_HEAD);

    $cs->registerScript('initizlize-superfish-menu', '
			$("#sf-menu-id").superfish( {delay: 100, autoArrows: false, dropShadows: false, pathClass: "overideThisToUse", speed: "fast" });
		', CClientScript::POS_READY);

    if(param('useYandexMap') == 1){
        $cs->registerScriptFile('http://api-maps.yandex.ru/2.0/?load=package.standard,package.clusters&coordorder=longlat&lang='.CustomYMap::getLangForMap(), CClientScript::POS_END);
    }
    elseif (param('useGoogleMap') == 1){
        //$cs->registerScriptFile('https://maps.google.com/maps/api/js??v=3.5&sensor=false&language='.Yii::app()->language.'', CClientScript::POS_END);
        //$cs->registerScriptFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', CClientScript::POS_END);
    }
    elseif (param('useOSMMap') == 1){
        //$cs->registerScriptFile('http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js', CClientScript::POS_END);
        //$cs->registerCssFile('http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css');

        $cs->registerScriptFile($baseThemeUrl . '/js/leaflet/leaflet-0.7.2/leaflet.js', CClientScript::POS_HEAD);
        $cs->registerCssFile($baseThemeUrl . '/js/leaflet/leaflet-0.7.2/leaflet.css');

        $cs->registerScriptFile($baseThemeUrl . '/js/leaflet/leaflet-0.7.2/dist/leaflet.markercluster-src.js', CClientScript::POS_HEAD);
        $cs->registerCssFile($baseThemeUrl . '/js/leaflet/leaflet-0.7.2/dist/MarkerCluster.css');
        $cs->registerCssFile($baseThemeUrl . '/js/leaflet/leaflet-0.7.2/dist/MarkerCluster.Default.css');
    }

    if(Yii::app()->user->getState('isAdmin')){
        ?><link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/tooltip/tipTip.css" /><?php
    }
    ?>

    <link type="text/css" href="<?php echo $baseThemeUrl; ?>/css/rent24.css?c=3" rel="stylesheet" />
</head>

<body>
<div id="main-wrapper">
    <?php $this->renderPartial('//site/header'); ?>

    <?php echo $content; ?>

    <?php $this->renderPartial('//site/footer'); ?>
</div><!--/main-wrapper-->

<?php $this->renderPartial('//site/subscription'); ?>

<div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
<?php
$cs->registerScript('main-vars', '
		var BASE_URL = '.CJavaScript::encode(Yii::app()->baseUrl).';
		var params = {
			change_search_ajax: '.param("change_search_ajax", 1).'
		}
	', CClientScript::POS_HEAD, array(), true);

$this->renderPartial('//layouts/_common');

$this->widget('application.modules.fancybox.EFancyBox', array(
        'target'=>'a.fancy',
        'config'=>array(
            'ajax' => array('data'=>"isFancy=true"),
            'titlePosition' => 'inside',
            'onClosed' => 'js:function(){
					var capClick = $("#yw0_button");
					if(typeof capClick !== "undefined")	capClick.click();
				}'
        ),
    )
);
//var capClick = $("#yw0_button");alert(capClick);
if(Yii::app()->user->getState('isAdmin')){
    $cs->registerScriptFile($baseThemeUrl.'/js/tooltip/jquery.tipTip.minified.js', CClientScript::POS_HEAD);
    $cs->registerScript('adminMenuToolTip', '
			$(function(){
				$(".adminMainNavItem").tipTip({maxWidth: "auto", edgeOffset: 10, delay: 200});
			});
		', CClientScript::POS_READY);
    ?>

    <div class="admin-menu-small <?php echo demo() ? 'admin-menu-small-demo' : '';?> ">
        <a href="<?php echo $baseUrl; ?>/apartments/backend/main/admin">
            <img src="<?php echo $baseThemeUrl; ?>/images/adminmenu/administrator.png" alt="<?php echo Yii::t('common','Administration'); ?>" title="<?php echo Yii::t('common','Administration'); ?>" class="adminMainNavItem" />
        </a>
    </div>
<?php } ?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter30987806 = new Ya.Metrika({id:30987806,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/30987806" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
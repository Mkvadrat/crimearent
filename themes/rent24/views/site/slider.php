<?php $banners = ApartmentBanner::getBanners($slider_position); ?>
<?php $showed_apartments = array() ?>

<div class="nivoSlider">
    <?php foreach($banners as $banner): ?>
        <?php $apartment = Apartment::model()->findByPk($banner->apartment_id); ?>
        <?php if (!$apartment || $apartment->active!=Apartment::STATUS_ACTIVE) continue; ?>
        <?php if (empty($apartment->images)) continue; ?>
        <?php $res = Images::getMainThumb(ApartmentBanner::WIDTH,ApartmentBanner::HEIGHT, $apartment->images); ?>
        <?php if (!$res || !$res['thumbUrl']) continue; ?>
        <?php $img = CHtml::image($res['thumbUrl'], $apartment->getStrByLang('title'), array(
            'title' => '#slider-desc-'.$apartment->id,
            'class' => 'slider-img'
        ));
        ?>
        <?php echo CHtml::link($img, $apartment->getUrl(), array()) ?>
        <?php $showed_apartments[]= $apartment; ?>
    <?php endforeach; ?>
</div>

<?php foreach ($showed_apartments as $apartment): ?>
    <div id="slider-desc-<?php echo $apartment->id ?>" class="nivo-html-caption">
        <div class="slider-price"><?php echo CHtml::encode($apartment->getPrettyPrice()) ?></div>
        <span class="slider-desc-caption">
            <?php $title = $apartment->getStrByLang('title') ?>
            <?php if (utf8_strlen($title)>30) $title=utf8_substr($title,0,30).'...'; ?>
            <?php echo CHtml::encode($title) ?>
        </span>
    <span>
        <?php $desc_array = array(); ?>
        <?php if (isset($apartment->city) && !empty($apartment->city->name)): ?>
            <?php $desc_array[] = CHtml::encode($apartment->city->name) ?>
        <?php endif ?>
        <?php $street = $apartment->getStrByLang('address') ?>
        <?php if (!empty($street)): ?>
            <?php $desc_array[] = CHtml::encode($street); ?>
        <?php endif; ?>
        <?php if (!empty($apartment->berths)): ?>
            <?php $desc_array[] = 'мест: '.CHtml::encode($apartment->berths) ?>
        <?php endif ?>
        <?php if (!empty($apartment->sea_distance)): ?>
            <?php $desc_array[] = 'до моря: '.CHtml::encode($apartment->sea_distance).'м' ?>
        <?php endif ?>
        <?php if (!empty($desc_array)): ?>
            <?php echo implode(', ',$desc_array); ?>
        <?php endif; ?>
    </span>
    </div>
<?php endforeach; ?>

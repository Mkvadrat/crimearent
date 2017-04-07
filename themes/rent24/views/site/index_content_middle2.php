<div class="panel-wrapper"><div class="panel"><div class="panel-row">
    <div id="left-banner" class="panel-left panel-item panel-small banner">
        <a href="#" class="shadow-center"><img src="/themes/rent24/images/banners/left-banner-sample.jpg" /></a>
    </div><!--/left-banner-->
    <div class="panel-item panel-space panel-space-big"></div>
    <div id="middle-grey-list" class="panel-middle panel-item  shadow-center">
        <h2 class="big-caption">Популярные города</h2>
        <ul class="grey-list">
            <?php
                $cities = array(
                    array(
                        'name' => 'Алушта',
                        'image' => '/themes/rent24/images/cities/alushta.jpg',
                        'desc' => 'На берегах Азовского и Черного морей расположен один из древнейших городов мира',
                        'price' => '',
                        'url' => 'page/20'
                    ),
                    array(
                        'name' => 'Новый свет',
                        'image' => '/themes/rent24/images/cities/noviy.jpg',
                        'desc' => 'В 7 км от Судака и в 112 км от Симферополя на берегу моря раскинулся поселок Новый Свет',
                        'price' => '',
                        'url' => '/page/15'
                    ),
                    array(
                        'name' => 'Симферополь',
                        'image' => '/themes/rent24/images/cities/simferopol.jpg',
                        'desc' => 'Город Симферополь является символическими воротами Крыма',
                        'price' => '',
                        'url' => '/page/16'
                    ),
                    array(
                        'name' => 'Судак',
                        'image' => '/themes/rent24/images/cities/sudak.jpg',
                        'desc' => 'Город, который может похвастаться своей вековой привлекательностью для туристов',
                        'price' => '',
                        'url' => '/page/17'
                    ),
                    array(
                        'name' => 'Феодосия',
                        'image' => '/themes/rent24/images/cities/feodosia.jpg',
                        'desc' => 'Город, который раскинулся на берегу залива в Чёрном море',
                        'price' => '',
                        'url' => '/page/18'
                    ),
                    array(
                        'name' => 'Ялта',
                        'image' => '/themes/rent24/images/cities/yalta.jpg',
                        'desc' => 'По праву город Ялта считается жемчужиной Крымского полуострова',
                        'price' => '',
                        'url' => '/page/19'
                    ),
                );
            ?>
            <?php foreach ($cities as $city): ?>
                <li>
                    <div class="grey-image">
                        <a href="<?php echo $city['url'] ?>"><img src="<?php echo $city['image'] ?>" alt="<?php echo $city['name'] ?>" /></a>
                    </div>
                    <div class="grey-desc">
                        <a href="<?php echo $city['url'] ?>" title="<?php echo $city['name'] ?>"><?php echo $city['name'] ?></a>
                        <p><?php echo $city['desc'] ?></p>
                        <?php if (!empty($city['price'])): ?>
                        <div class="grey-list-price-wrapper">
                            Цена в сутки:
                            <div class="grey-list-price"><?php echo $city['price'] ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div><!--/middle-grey-list-->
</div></div></div><!--/panel-wrapper-->
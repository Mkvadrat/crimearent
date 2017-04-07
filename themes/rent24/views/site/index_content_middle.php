<div id="content-middle">
    <div class="container">

    <?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        if ($key=='error' || $key == 'success' || $key == 'notice'){
            echo "<div class='flash-{$key}'>{$message}</div>";
        }
    }
    ?>


    <div class="panel-wrapper"><div class="panel"><div class="panel-row">

        <?php $this->renderPartial('//site/index_menu'); ?>

        <div class="panel-item panel-space panel-space-big"></div>
        <div id="content-middle" class="panel-middle panel-item justified">
            <?php

            if($page && isset($page->page)){
                if ($page->page->title) echo '<h1>'.$page->page->title.'</h1>';
                if ($page->page->body) echo $page->page->body;
            }
            ?>
        </div><!--/content-middle-->
        <div class="panel-item panel-space panel-space-big"></div>
        <div id="banners-right" class="panel-right panel-item panel-small no-shadow">
            <div class="right-info shadow-center">
                <h2>Квартиры на сутки, <br />0% комиссии!</h2>
                <div class="big-text bold-text">На crimearent24.ru:</div>
                <ul>
                    <li>Квартиры сдаются без посредников</li>
                    <li>Вы не платите комиссионные</li>
                    <li>Только актуальные цены</li>
                    <li>Вы экономите время</li>
                    <li>Подбор квартиры любого бюджета</li>
                </ul>
                <?php
                $apartments_co = 0;
                $apartments_co = Apartment::model()->count("active = '1'");
                ?>
                <?php if ($apartments_co>0): ?>
                <div class="right-info-bottom">
                    <div class="info-count"><?php echo $apartments_co ?></div>
                    <div class="info-bottom-text">Туристов доверили нам свой отдых</div>
                </div>
                <?php endif ?>
            </div>
            <div class="right-banner banner">
                <a href="#"><img src="/themes/rent24/images/banners/right-banner-sample.jpg" /></a>
            </div>
        </div><!--/banners-right-->
    </div></div></div><!--/panel-wrapper-->

    <?php $this->renderPartial('//site/index_content_middle2'); ?>

    </div>
</div><!--/content-middle-->
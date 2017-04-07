<div id="header" class="shadowed">
    <div class="container">
        <div class="navbar">
            <div class="header-social">
                <a class="social-btn social-vk" href="https://vk.com/crimearent24ru"></a>
                <!--
                <a class="social-btn social-fb" href="#"></a>
                <a class="social-btn social-ok" href="#"></a>
                <a class="social-btn social-tw" href="#"></a>
                -->
                <span class="social-text">Мы в социальных сетях</span>
            </div>
            <div class="header-menu">
                <ul class="nav">
                    <li class="active"><a href="/userads/create">Разместить объявление</a></li>
                    <?php if (!Yii::app()->user->isGuest): ?>
                        <li><a href="/site/logout">(<?php echo Yii::app()->user->username ?>) Выйти</a></li>
                    <?php else: ?>
                        <li><a href="/usercpanel">Личный кабинет</a></li>
                    <?php endif; ?>
                    <li class="separator"><span>|</span></li>
                    <li><a href="/faq">FAQ</a></li>
                    <li class="separator"><span>|</span></li>
                    <li><a href="/page/22">О нас</a></li>
                </ul>
            </div><!--/header-menu -->
        </div><!-- /.navbar -->
    </div><!--/container-->
</div><!--/header-->
<div id="content-top" class="shadowed">
    <div class="container">
        <?php $this->renderPartial('//site/logo_panel'); ?>
        <?php $this->renderPartial('//site/inner_menu'); ?>
        <?php if($this->showSearchForm): ?>
        <div class="filter-collapse">
            <div id="wide-filter" class="panel-wrapper shadow-down"><div class="panel"><div class="panel-row">

            <?php $this->renderPartial('//site/inner-search'); ?>

            <div class="hide-ico-wrapper"><a href="#" class="hide-ico"></a></div>
            </div></div></div><!--/panel-wrapper-->
        </div><!--/filter-collapse-->
        <div class="show-ico-wrapper"><a href="#" class="show-ico"></a></div>
        <?php endif; ?>
    </div><!--/container-->
</div><!--/content-top-->
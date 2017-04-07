<div class="logo-panel">
    <a id="logo" title="<?php echo Yii::t('common', 'Go to main page'); ?>" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
        <span id="logotext"><span>Жильё для комфортного отдыха в Крыму</span></span>
    </a>
    <div class="input-append search-input shadowed search-term">
        <form action="<?php echo Yii::app()->controller->createUrl('/quicksearch/main/mainsearch');?>" method="get">
            <input type="text"  class="textbox span2" name="term" id="search_term_text" maxlength="50" placeholder="Введите поисковый запрос" value="<?php echo (isset($this->term)) ? $this->term : '';?>" />
            <button class="btn" type="button" onclick="prepareSearch(); return false;"></button>
            <input type="hidden" value="0" id="do-term-search" name="do-term-search">
        </form>
    </div>
</div><!--/logo-panel-->
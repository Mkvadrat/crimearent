<?php if (!Yii::app()->request->isAjaxRequest): ?>
<?php $this->renderPartial('//site/index_content_middle', array('page' => $page)); ?>
<?php endif; ?>

<?php $this->renderPartial('//site/index_content_bottom', array('page' => $page)); ?>
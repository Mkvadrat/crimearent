<?php

$this->beginContent('//layouts/main');

$this->renderPartial('//site/inner_content_top');

?>


<?php $this->renderPartial('//site/inner_content_middle', array('content'=>$content)); ?>

<?php $this->endContent(); ?>
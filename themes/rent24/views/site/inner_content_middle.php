<div id="content-middle" class="inner-content-middle">
    <div class="container product-container">

    <?php if(isset($this->breadcrumbs) && $this->breadcrumbs):?>
        <?php
        $this->widget('zii.widgets.CBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
            'separator' => ' &nbsp;/&nbsp; ',
        ));
        ?>
    <?php endif?>

    <?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        if ($key=='error' || $key == 'success' || $key == 'notice'){
            echo "<div class='flash-{$key}'>{$message}</div>";
        }
    }
    ?>

    <?php echo $content; ?>
    </div>
</div><!--/content-middle-->
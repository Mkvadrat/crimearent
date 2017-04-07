<?php
	if($this->images){
		?>
		<div class="images-area">
			<?php
				foreach($this->images as $image){
					if($this->withMain && $image['is_main'] || !$this->withMain && !$image['is_main'] || !$image['is_main']){
						?>
						<div class="image-area-item">
						<?php
							$imgTag = CHtml::image(Images::getThumbUrl($image, 180, 100), Images::getAlt($image));
                            /*
							echo CHtml::link($imgTag, Images::getFullSizeUrl($image), array(
								'rel' => 'prettyPhoto[img-gallery]',
								'title' => Images::getAlt($image),
							));
                            */
                        /*
                            echo CHtml::link($imgTag, Images::getThumbUrl($image, 600, 400), array(
                                'rel' => 'prettyPhoto[img-gallery]',
                                'title' => Images::getAlt($image),
                            ));
                        */
                            echo CHtml::link($imgTag, Images::getThumbUrlWatermark($image, 600, 400), array(
                                'rel' => 'prettyPhoto[img-gallery]',
                                'title' => Images::getAlt($image),
                            ));
						?>
                        </div>
						<?php
					}
				}
			?>
			<div class="clear"></div>
		</div>
		<?php
	}

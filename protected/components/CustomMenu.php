<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *	version				:	1.10.0
 *	copyright			:	(c) 2015 Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 *
 * This file is part of CMS Open Real Estate
 *
 * Open Real Estate is free software. This work is licensed under a GNU GPL.
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 ***********************************************************************************************/

Yii::import('zii.widgets.CMenu');

class CustomMenu extends CMenu {
	protected function renderMenuItem($item){
		if(isset($item['url'])){
			if(isset($item['linkOptions']['submit'])){
				$item['linkOptions']['csrf'] = true;
			}

			$label=$this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);
			return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
		}
		else
			return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
	}

	protected function isItemActive($item,$route) {
		if(isset($item['url']) && is_array($item['url'])) {
			if (!strcasecmp(trim($item['url'][0],'/'),$route))
			{
				unset($item['url']['#']);
				if(count($item['url'])>1)
				{
					foreach(array_splice($item['url'],1) as $name=>$value)
					{
						if(!isset($_GET[$name]) || $_GET[$name]!=$value)
							return false;
					}
				}
				return true;
			}
		}
		elseif (isset($item['url']) && !is_array($item['url'])) {
			$activeModule = (Yii::app()->getController()->getModule() && Yii::app()->getController()->getModule()->getId()) ? Yii::app()->getController()->getModule()->getId() : '';

				if ($activeModule == 'infopages' && is_array(Yii::app()->getController()->getActionParams())) {
					$tUrl = trim($item['url'], '/');
					$tUrlExplode = explode('/', $tUrl);
					$tUrl = (count($tUrlExplode) >  1) ? $tUrlExplode[count($tUrlExplode) - 1] : null;

					if ($tUrl) {
						$activeMenuPage = Yii::app()->getController()->getActionParams();

						if (is_array($activeMenuPage) && array_key_exists('url', $activeMenuPage)) {
							if ($activeMenuPage['url'] == $tUrl) {
								return true;
							}
						}
					}

					return false;
				}
		}
		return false;
	}
}
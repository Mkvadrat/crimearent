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

class itemPaginatorAtlas extends CLinkPager {
	public $htmlOption = array();
	public $nextPageLabel = '&gt;';
	public $prevPageLabel = '&lt;';

	public function init(){
		$this->cssFile = Yii::app()->theme->baseUrl.'/css/pager.css';
		parent::init();
	}

	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected)
			$class.=' '.($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);
		if ($hidden) {
			return '<li class="'.$class.'">'.CHtml::link($label, 'javascript: void(0);'/*, $this->htmlOption*/).'</li>';
		}
		else
			return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page), $this->htmlOption).'</li>';
	}

	protected function createPageButtons() {
		if(($pageCount=$this->getPageCount())<=1)
			return array();

		list($beginPage,$endPage)=$this->getPageRange();
		$currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
		$buttons=array();

		/*// first page
		$buttons[]=$this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,$currentPage<=0,false);*/

		// prev page
		if(($page=$currentPage-1)<0)
			$page=0;
		$buttons[]=$this->createPageButton($this->prevPageLabel,$page,$this->previousPageCssClass,$currentPage<=0,false);

		// internal pages
		for($i=$beginPage;$i<=$endPage;++$i)
			$buttons[]=$this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);

		// next page
		if(($page=$currentPage+1)>=$pageCount-1)
			$page=$pageCount-1;
		$buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);

		/*// last page
		$buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);*/

		return $buttons;
	}
}
<?php

/**********************************************************************************************
 *	copyright			:	(c) 2013 Monoray
 *	website				:	http://www.monoray.ru/
 *	contact us			:	http://www.monoray.ru/contact
 ***********************************************************************************************/

Yii::import('ext.groupgridview.BootGroupGridView');

class CustomBootStrapGroupGridView extends BootGroupGridView {
	//public $pager = array('class'=>'objectPaginator');
	public $template = "{summary}\n{pager}\n{items}\n{pager}";

	//public $extraRowColumns = array('reference_category_id');
	public $mergeType = 'nested';

	public $type = 'striped bordered condensed';
}
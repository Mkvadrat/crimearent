<?php
/**********************************************************************************************
 *                            CMS Open Real Estate
 *                              -----------------
 *	version				:	%TAG%
 *	copyright			:	(c) %YEAR% Monoray
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

class YandexRealtyModule extends Module {
	public $defaultController = 'main';

	public function init() {
		$this->setImport(array(
			'application.modules.'.$this->getName() . '.models.*',
			'application.modules.'.$this->getName() . '.components.*',
		));
	}
}

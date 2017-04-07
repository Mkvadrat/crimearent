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


class HApartment {
    public static function saveOther(Apartment $ad){
        if(ApartmentVideo::saveVideo($ad)){
            $ad->panoramaFile = CUploadedFile::getInstance($ad, 'panoramaFile');
            $ad->scenario = 'panorama';
            if(!$ad->validate()) {
                return false;
            }
        }

        $city = "";
        if (issetModule('location') && param('useLocation', 1)) {
            $city .= $ad->locCountry ? $ad->locCountry->getStrByLang('name') : "";
            $city .= ($city && $ad->locCity) ? ", " : "";
            $city .= $ad->locCity ? $ad->locCity->getStrByLang('name') : "";
        } else
            $city = $ad->city ? $ad->city->getStrByLang('name') : "";

        // data
        if(($ad->address && $city) && (param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1))){
            if (!$ad->lat && !$ad->lng) { # уже есть
                $coords = Geocoding::getCoordsByAddress($ad->address, $city);

                if(isset($coords['lat']) && isset($coords['lng'])){
                    $ad->lat = $coords['lat'];
                    $ad->lng = $coords['lng'];
                }
            }
        }

        return true;
    }

    public static function getRequestType(){
        $type = Yii::app()->getRequest()->getQuery('type');
        $existType = array_keys(Apartment::getTypesArray());
        if(!in_array($type, $existType)){
            $type = Apartment::TYPE_DEFAULT;
        }
        return $type;
    }

    /** Сохраняем данные выбранных справочников
     * @return array
     */
    public static function getCategoriesForUpdate(Apartment $ad)
    {
        if (isset($_POST['category']) && is_array($_POST['category'])) {
            $ad->references = Apartment::getCategories(null, $ad->type);
            foreach ($_POST['category'] as $cat => $categoryArray) {
                foreach ($categoryArray as $key => $value) {
                    $ad->references[$cat]['values'][$key]['selected'] = true;
                }
            }
        } else {
            $ad->references = Apartment::getCategories($ad->id, $ad->type);
        }

        return $ad->references;
    }
}
<?php

if ($data->canShowInView('address')) {
    $adressFull = '';

    if (issetModule('location') && param('useLocation', 1)) {
        if ($data->locCountry || $data->locRegion || $data->locCity)
            $adressFull = ' ';

        if ($data->locCountry) {
            $adressFull .= $data->locCountry->getStrByLang('name');
        }
        if ($data->locRegion) {
            if ($data->locCountry)
                $adressFull .= ',&nbsp;';
            $adressFull .= $data->locRegion->getStrByLang('name');
        }
        if ($data->locCity) {
            if ($data->locCountry || $data->locRegion)
                $adressFull .= ',&nbsp;';
            $adressFull .= $data->locCity->getStrByLang('name');
        }
    } else {
        if (isset($data->city) && isset($data->city->name)) {
            $cityName = $data->city->name;
            if ($cityName) {
                $adressFull = ' ' . $cityName;
            }
        }
    }

    $adress = CHtml::encode($data->getStrByLang('address'));
    if ($adress) {
        $adressFull .= ', ' . $adress;
    }


    $house = CHtml::encode($data->getStrByLang('house'));
    if ($house) {
        if (!empty($adressFull)) $adressFull .=', ';
        $adressFull .= $house;
    }

    if ($adressFull) {
        echo '<dt>' . tt('Address') . ':</dt><dd>' . $adressFull . '</dd>';
    }
}
?>
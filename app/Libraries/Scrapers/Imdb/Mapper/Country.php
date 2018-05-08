<?php
/**
 * Created by PhpStorm.
 * User: sirio
 * Date: 08/05/2018
 * Time: 22:58
 */

namespace App\Libraries\Scrapers\Imdb\Mapper;


class Country
{
    public static function getMappedValue(string $value): string
    {
        switch ($value) {
            case "PuertoRico":
                return "Puerto Rico";
            case "HongKong":
                return "Hong Kong";
            case "WestGermany":
                return "West Germany";
            case "NewZealand":
                return "New Zealand";
            case "SouthKorea":
                return "South Korea";
            case "CzechRepublic":
                return "Czech Republic";
            case "Bosnia and Herzegovina":
            case "Bosnia And Herzegovina":
                return "Bosnia-Herzegovina";
            case "Federal Republic of Yugoslavia":
                return "Yugoslavia";
        }
        return $value;
    }
}
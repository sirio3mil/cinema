<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 13:06
 */

namespace App\Libraries\Scrapers\Imdb\Utils;


class Cleaner
{
    public static function clearText(string $value): string
    {
        return str_replace("> <", "><",
            preg_replace('/\s+/', ' ',
                str_replace(["\r\n", "\n\r", "\n", "\r"], "", $value)));
    }

    public static function clearField(string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return trim(str_replace("%20", " ", str_replace("\"", "", strip_tags($value))));
    }
}
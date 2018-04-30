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
    public static function getText(string $url): string
    {
        return str_replace("> <", "><",
            preg_replace('/\s+/', ' ',
                str_replace(["\r\n", "\n\r", "\n", "\r"], "", file_get_contents($url))));
    }

    public static function clearField(string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return trim(str_replace("%20", " ", str_replace("\"", "", $value)));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 16:52
 */

namespace App\Libraries\Scrapers\Imdb\Pages;


class Locations extends Page
{
    public function dameLocalizacion()
    {
        $matches = array();
        if (strpos($this->content, "Filming Locations:") !== false) {
            $html = static::clean();
            if (!empty($html)) {
                preg_match_all('|/search/title\?locations=([^>]+)\"itemprop=\'url\'>([^>]+)</a>|U', $html,
                    $matches);
                return $matches;
            }
        }
        return $matches;
    }
}
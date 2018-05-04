<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 17:11
 */

namespace App\Libraries\Scrapers\Imdb\Pages;


class ParentalGuide extends Page
{

    public function __construct()
    {
        $this->setFolder('parentalguide');
    }

    public function getCertificates(): array
    {
        $matches = [];
        if (!$this->isChapter && (strpos($this->content, "See all certifications") !== false)) {
            $html = file_get_contents($this->url . "parentalguide");
            if (!empty($html)) {
                preg_match_all('|<a href=\"/search/title\?certificates=([^>]+)\">([^>]+)</a>|U', $html, $matches);
            }
        }
        return $matches;
    }
}
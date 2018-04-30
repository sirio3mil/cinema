<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 15:40
 */

namespace App\Libraries\Scrapers\Imdb\Pages;


class EpisodesList extends Page
{

    protected const EPISODE_LIST_PATTERN = '|<a href=\"/title/([^>]+)\">|U';

    public function getEpisodes(): array
    {
        if (!$this->content) {
            return [];
        }
        preg_match_all(static::EPISODE_LIST_PATTERN, $this->content, $matches);
        if (empty($matches[1])) {
            return [];
        }
        $episodes = [];
        foreach ($matches[1] as $url) {
            $number = str_replace('tt', '', $url);
            $parts = explode("/", $number);
            $number = (int)($parts[0]);
            if (!in_array($number, $episodes)) {
                $episodes[] = $number;
            }
        }
        return $episodes;
    }
}
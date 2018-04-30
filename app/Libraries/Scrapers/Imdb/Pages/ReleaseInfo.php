<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 13:27
 */

namespace App\Libraries\Scrapers\Imdb\Pages;

use App\Libraries\Scrapers\Imdb\Objects\Release;


class ReleaseInfo extends Page
{

    protected const RELEASE_DATE_PATTERN = '|<td><a href=\"([^>]+)\">([^>]+)</a></td><td class=\"release_date\">([^>]+)<a href=\"([^>]+)\">([^>]+)</a></td><td>([^>]*)</td>|U';
    protected const OTHER_TITLES_PATTERN = '|<tr class="([^>]+)"><td>([^>]+)</td><td>([^>]+)</td></tr>|U';

    public function getReleaseDates(): array
    {
        $releases = [];
        if ($this->content) {
            preg_match_all(static::RELEASE_DATE_PATTERN, $this->content, $matches);
            /*
             * 2 USA
             * 3 29 September
             * 5 2014
             * 6 detail
             */
            foreach ($matches as $match) {
                $releases[] = (new Release())->setFromScrapper($match);
            }
        }

        /** var Release[] $matches */
        return $releases;
    }

    public static function getPreviousReleaseDate(int $timestamp, array $releases): int
    {
        $min = $timestamp;
        if (!$releases){
            return 0;
        }
        /** @var Release $release */
        foreach ($releases as $release){
            $actual = $release->getDate()->getTimestamp();
            if ($actual && $min > $actual) {
                $min = $actual;
            }
        }
        return $min;
    }

    public function getAlsoKnownAs(): array
    {
        $matches = array();
        if (!empty($this->content)) {
            preg_match_all(static::OTHER_TITLES_PATTERN, $this->content, $matches);
        }
        return $matches;
    }
}
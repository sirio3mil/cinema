<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 13:27
 */

namespace App\Libraries\Scrapers\Imdb;


class ReleaseInfo extends Page
{

    protected const RELEASE_DATE_PATTERN = '|<td><a href=\"([^>]+)\">([^>]+)</a></td><td class=\"release_date\">([^>]+)<a href=\"([^>]+)\">([^>]+)</a></td><td>([^>]*)</td>|U';

    public function getReleaseDates(): array
    {
        $matches = [];
        if ($this->content) {
            preg_match_all(static::RELEASE_DATE_PATTERN, $this->content, $matches);
        }
        /*
         * 2 USA
         * 3 29 September
         * 5 2014
         * 6 detail
         */
        return $matches;
    }

    public static function getPreviousReleaseDate(int $timestamp, array $releases): int
    {
        $min = $timestamp;
        if (!empty($releases[0])) {
            $elements = count($releases[0]);
            for ($i = 0; $i < $elements; $i++) {
                $monthDay = trim($releases[3][$i]);
                $year = trim($releases[5][$i]);
                if (!empty($monthDay) && !empty($year)) {
                    $actual = strtotime("{$monthDay} {$year}");
                    if ($actual && $min > $actual) {
                        $min = $actual;
                    }
                }
            }
        }
        return $min;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 15:07
 */

namespace App\Libraries\Scrapers\Imdb\Pages;


class Credits extends Page
{

    protected const CREDITS_PATTERN = '|<a href=\"/name/nm([^>]+)/([^>]+)\"> ([^>]+)</a>|U';
    protected const CAST_PATTERN = '|<a href=\"/name/nm([^>]+)/([^>]+)\"itemprop=\'url\'><span class=\"itemprop\" itemprop=\"name\">([^>]+)</span></a></td><td class=\"ellipsis\">(.*)</td><td class=\"character\"><div>(.*)</div>|U';

    protected $directorsContent;

    protected $writersContent;

    public function setContent(string $content): Page
    {
        parent::setContent($content);

        if (strpos($this->content, "Directed by") !== false) {
            $arrayTemp = explode("Directed by", $this->content);
            $arrayTemp = explode("</table>", $arrayTemp[1]);
            if (!empty($arrayTemp[0])) {
                $this->directorsContent = $arrayTemp[0];
            }
        }
        if (strpos($this->content, "Writing Credits") !== false) {
            $arrayTemp = explode("Writing Credits", $this->content);
            $arrayTemp = explode("</table>", $arrayTemp[1]);
            if (!empty($arrayTemp[0])) {
                $this->writersContent = $arrayTemp[0];
            }
        }

        return $this;
    }

    public function getDirectors()
    {
        $matches = [];
        if (!empty($this->directorsContent)) {
            preg_match_all(static::CREDITS_PATTERN, $this->directorsContent, $matches);
        }
        return $matches;
    }

    public function getWriters()
    {
        $matches = [];
        if (!empty($this->writersContent)) {
            preg_match_all(static::CREDITS_PATTERN, $this->writersContent, $matches);
        }
        return $matches;
    }

    public function getCast()
    {
        $matches = [];
        preg_match_all(static::CAST_PATTERN, $this->content_cast, $matches);
        return $matches;
    }

}
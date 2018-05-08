<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 17:22
 */

namespace App\Libraries\Scrapers\Imdb\Pages;

use App\Libraries\Scrapers\Imdb\Mapper\Country;
use App\Libraries\Scrapers\Imdb\Utils\Cleaner;

class Home extends Page
{
    protected const IMDB_NUMBER_PATTERN = '|title/tt([^>]+)/|U';
    protected const TITLE_PATTERN = '|<title>([^>]+) \(|U';
    protected const ORIGINAL_TITLE_PATTERN = '|<div class=\"originalTitle\">([^>]+)<span|U';
    protected const TV_SHOW_PATTERN = '|<div class=\"titleParent\"><a href=\"/title/tt([0-9]{7})|U';
    protected const YEAR_PATTERN = '|<title>([^>]+)([1-2][0-9][0-9][0-9])([^>]+)</title>|U';
    protected const DURATION_PATTERN = '|datetime=\"PT([0-9]{1,3})M\"|U';
    protected const SCORE_PATTERN = '|<span itemprop="ratingValue">([^>]+)</span>|U';
    protected const VOTES_PATTERN = '|<span class="small" itemprop="ratingCount">([^>]+)</span>|U';
    protected const COLOR_PATTERN = '|<a href=\"/search/title\?colors=([^>]+)\"itemprop=\'url\'>([^>]+)</a>|U';
    protected const SOUND_PATTERN = '|<a href=\"/search/title\?sound_mixes=([^>]+)\"itemprop=\'url\'>([^>]+)</a>|U';
    protected const COUNTRY_PATTERN = '|country_of_origin=([^>]+)>([^>]+)<|U';
    protected const LANGUAGE_PATTERN = '|primary_language=([^>]+)>([^>]+)<|U';
    protected const RECOMMENDATIONS_PATTERN = '|data-tconst=\"tt([0-9]{7})\"|U';
    protected const GENRE_PATTERN = '|genre/([^>]+)>([^>]+)<|U';
    protected const SEASON_PATTERN = '|>Season ([0-9]{1,2}) <|U';
    protected const EPISODE_PATTERN = '|> Episode ([0-9]{1,2})<|U';

    protected const SEASON_SPLITTER = '<h4 class="float-left">Seasons</h4>';

    public $season;
    public $episode;
    public $title;
    public $status;

    protected $episodeFlag;
    protected $tvShow;

    public function isTvShow(): bool
    {
        if($this->tvShow === null){
            $this->setTvShowFlags();
        }
        return $this->tvShow;
    }

    public function isEpisode(): bool
    {
        if($this->episodeFlag === null){
            $this->setTvShowFlags();
        }
        return $this->episodeFlag;
    }

    public function setContent(?string $content): Page
    {
        parent::setContent($content);
        if (!$this->content) {
            throw new \Exception("Error fetching content from $this->url");
        }
        return $this;
    }

    public function haveReleaseInfo(): bool
    {
        if (strpos($this->content, "Also Known As:") === false) {
            return false;
        }
        if (strpos($this->content, "Release Date:") === false) {
            return false;
        }
        return true;
    }

    public function setTvShowFlags(): Home
    {
        $this->episodeFlag = false;
        $matches = [
            'Episode cast overview',
            'Episode credited cast',
            'Episode complete credited cast'
        ];
        foreach ($matches as $match) {
            if (strpos($this->content, $match) !== false) {
                $this->episodeFlag = true;
                $this->tvShow = false;
                return $this;
            }
        }
        $this->tvShow = (strpos($this->content, static::SEASON_SPLITTER) !== false) ? true : false;
        return $this;
    }

    public function setTitle(): Home
    {
        $matches = [];
        preg_match_all(static::TITLE_PATTERN, $this->content, $matches);
        if (empty($matches[1][0])) {
            throw new \Exception("Error fetching original title");
        }
        $title = html_entity_decode(trim($matches[1][0]), ENT_QUOTES);
        if ($this->episodeFlag) {
            $parts = explode("\"", $title);
            $title = end($parts);
        } else {
            if (strpos($this->content, "(original title)") !== false) {
                preg_match_all(static::ORIGINAL_TITLE_PATTERN, $this->content, $matches);
                $title = html_entity_decode(trim($matches[1][0]), ENT_QUOTES);
            } else {
                $parts = explode("(", $title);
                $title = trim($parts[0]);
            }
        }
        if (empty($title)) {
            throw new \Exception("Error fetching original title");
        }
        $title = str_replace('"', '', trim(strip_tags($title)));
        $this->title = Cleaner::clearField($title);
        return $this;
    }

    public function getTitle(): ?string
    {
        if(is_null($this->title)){
            $this->setTitle();
        }
        return $this->title;
    }

    public function getTvShow(): ?int
    {
        if ($this->episodeFlag) {
            preg_match_all(static::TV_SHOW_PATTERN, $this->content, $matches);
            if(!empty($matches[1][0])){
                return (int)($matches[1][0]);
            }
        }
        return null;
    }

    public function getYear(): ?int
    {
        preg_match_all(static::YEAR_PATTERN, $this->content, $matches);
        if (!empty($matches[2][0])) {
            return (is_numeric($matches[2][0])) ? (int)$matches[2][0] : null;
        }
        return null;
    }

    public function getDuration(): ?int
    {
        preg_match_all(static::DURATION_PATTERN, $this->content, $matches);
        return (!empty($matches[1][0])) ? (int)trim($matches[1][0]) : null;
    }

    public function getScore(): int
    {
        $matches = array();
        preg_match_all(static::SCORE_PATTERN, $this->content, $matches);
        if (empty($matches[1][0])) {
            return 0;
        }
        return intval(filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_INT)) / 20;
    }

    public function getVotes(): int
    {
        $matches = array();
        preg_match_all(static::VOTES_PATTERN, $this->content, $matches);
        if (empty($matches[1][0])) {
            return 0;
        }
        return intval(filter_var($matches[1][0], FILTER_SANITIZE_NUMBER_INT));
    }

    public function getColor(): ?string
    {
        $matches = array();
        preg_match_all(static::COLOR_PATTERN, $this->content,$matches);
        return (!empty($matches[2][0])) ? Cleaner::clearField(strip_tags($matches[2][0])) : null;
    }

    public function getSounds(): array
    {
        $sounds = [];
        $matches = [];
        preg_match_all(static::SOUND_PATTERN, $this->content,$matches);
        if (array_key_exists(2, $matches) && is_array($matches[2])) {
            foreach ($matches[2] as $sound) {
                $sounds[] = Cleaner::clearField($sound);
            }
        }
        return $sounds;
    }

    public function getRecommendations(): array
    {
        $recommended = [];
        $matches = [];
        preg_match_all(static::RECOMMENDATIONS_PATTERN, $this->content, $matches);
        if (array_key_exists(1, $matches) && is_array($matches[1])) {
            foreach ($matches[1] as $imdb) {
                $recommended[] = (int)Cleaner::clearField($imdb);
            }
        }
        return $recommended;
    }

    public function getCountries(): array
    {
        $countries = [];
        $matches = [];
        preg_match_all(static::COUNTRY_PATTERN, $this->content, $matches);
        if(array_key_exists(2, $matches) && is_array($matches[2])) {
            foreach ($matches[2] as $country) {
                $countries[] = Country::getMappedValue(Cleaner::clearField($country));
            }
        }
        return array_unique($countries);
    }

    public function getLanguages(): array
    {
        $languages = [];
        $matches = [];
        preg_match_all(static::LANGUAGE_PATTERN, $this->content, $matches);
        if(array_key_exists(2, $matches) && is_array($matches[2])) {
            foreach ($matches[2] as $language) {
                $languages[] = Cleaner::clearField($language);
            }
        }
        return array_unique($languages);
    }

    public function getGenres(): array
    {
        $genres = [];
        $matches = [];
        preg_match_all(static::GENRE_PATTERN, $this->content, $matches);
        if(array_key_exists(2, $matches) && is_array($matches[2])) {
            foreach ($matches[2] as $key => $genre) {
                if(stripos($matches[1][$key], "tt_stry_gnr")) {
                    $genres[] = Cleaner::clearField($genre);
                }
            }
        }
        return array_unique($genres);
    }

    public function setSeasonData(): Home
    {
        if ($this->episodeFlag) {
            $this->setSeasonNumber()->setEpisodeNumber();
        }
        return $this;
    }

    protected function setSeasonNumber(): Home
    {
        $matches = [];
        preg_match_all(static::SEASON_PATTERN, $this->content, $matches);
        if (!empty($matches[1][0]) && is_numeric($matches[1][0])) {
            $this->season = (int)($matches[1][0]);
        }
        return $this;
    }

    protected function setEpisodeNumber(): Home
    {
        $matches = [];
        preg_match_all(static::EPISODE_PATTERN, $this->content, $matches);
        if (!empty($matches[1][0]) && is_numeric($matches[1][0])) {
            $this->episode = (int)($matches[1][0]);
        }
        return $this;
    }
}
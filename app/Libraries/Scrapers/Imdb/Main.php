<?php

namespace App\Libraries\Scrapers\Imdb;

class Main extends Page
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

    protected const SEASON_SPLITTER = '<h4 class="float-left">Seasons</h4>';

    protected const RELEASE_INFO_PAGE = 'releaseinfo';
    protected const FULL_CREDITS_PAGE = 'fullcredits';
    protected const EPISODES_PAGE = 'episodes';


    protected $url;

    protected $releaseInfo;
    protected $credits;
    protected $episodesList;

    public $imdbNumber;
    public $season;
    public $chapter;
    public $isChapter;
    public $isTvShow;
    public $title;
    public $status;

    public function __construct($url)
    {
        $this->url = $url;
        $this->setContent(Cleaner::getText($this->url))
            ->setTvShowFlags()
            ->setTitle()
            ->setImdbNumber()
            ->setReleaseInfo()
            ->setCredits()
            ->setEpisodesList();
    }

    public function setContent(string $content): Page
    {
        parent::setContent($content);
        if (!$this->content) {
            throw new \Exception("Error fetching content from $this->url");
        }
        return $this;
    }

    protected function setReleaseInfo(): Main
    {
        $this->releaseInfo = new ReleaseInfo();
        if (strpos($this->content, "Also Known As:") === false) {
            return $this;
        }
        if (strpos($this->content, "Release Date:") === false) {
            return $this;
        }
        $this->releaseInfo->setContent(Cleaner::getText($this->url . static::RELEASE_INFO_PAGE));
        return $this;
    }

    public function getReleaseInfo(): ?ReleaseInfo
    {
        return $this->releaseInfo;
    }

    protected function setCredits(): Main
    {
        $this->credits = (new Credits())->setContent(Cleaner::getText($this->url . static::FULL_CREDITS_PAGE));
        return $this;
    }

    public function getCredits(): ?Credits
    {
        return $this->credits;
    }

    protected function setImdbNumber(): Main
    {
        preg_match_all(static::IMDB_NUMBER_PATTERN, $this->url, $matches);
        $this->imdbNumber = (int)($matches[1][0]);
        return $this;
    }

    protected function setTvShowFlags(): Main
    {
        $this->isChapter = false;
        $matches = [
            'Episode cast overview',
            'Episode credited cast',
            'Episode complete credited cast'
        ];
        foreach ($matches as $match) {
            if (strpos($this->content, $match) !== false) {
                $this->isChapter = true;
                $this->isTvShow = false;
                return $this;
            }
        }
        $this->isTvShow = (strpos($this->content, static::SEASON_SPLITTER) !== false) ? true : false;
        return $this;
    }

    protected function setTitle(): Main
    {
        $matches = [];
        preg_match_all(static::TITLE_PATTERN, $this->content, $matches);
        if (empty($matches[1][0])) {
            throw new \Exception("Error fetching original title");
        }
        $title = html_entity_decode(trim($matches[1][0]), ENT_QUOTES);
        if ($this->isChapter) {
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

    protected function setEpisodesList(): Main
    {
        $this->episodesList = new EpisodesList();
        if ($this->isTvShow) {
            $this->episodesList->setContent(Cleaner::getText($this->url . static::EPISODES_PAGE));
        }
        return $this;
    }

    public function getEpisodesList(): ?EpisodesList
    {
        return $this->episodesList;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getTvShow(): ?int
    {
        if ($this->isChapter) {
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

    public function getSound(): ?string
    {
        preg_match_all(static::SOUND_PATTERN, $this->content,$matches);
        if (!empty($matches[2]) && is_array($matches[2])) {
            $sounds = "";
            foreach ($matches[2] as $sound) {
                $sounds .= trim(strip_tags($sound)) . ", ";
            }
            return Cleaner::clearField(substr($sounds, 0, -2));
        }
        return null;
    }

    public function dameRecomendada()
    {
        if (strpos($this->content, "<h2>Recommendations</h2>") !== false) {
            $arrayTemp = explode("<h2>Recommendations</h2>", $this->content);
            if (!empty($arrayTemp[1])) {
                preg_match_all('|/title/tt([^>]+)/\">|U', $arrayTemp[1], $matches);
                if (!empty($matches[1][0])) {
                    $imdb = trim(strip_tags($matches[1][0]));
                    settype($imdb, 'integer');
                    return $imdb;
                }
            }
        }
        return false;
    }

    public function dameTituloAdicionales()
    {
        $matches = array();
        if (!empty($this->pagesContent['info'])) {
            preg_match_all('|<tr class="([^>]+)"><td>([^>]+)</td><td>([^>]+)</td></tr>|U', $this->pagesContent['info'],
                $matches);
        }
        return $matches;
    }

    public function dameLocalizacion()
    {
        $matches = array();
        if (strpos($this->content, "Filming Locations:") !== false) {
            $html = static::clean($this->url . "locations");
            if (!empty($html)) {
                preg_match_all('|/search/title\?locations=([^>]+)\"itemprop=\'url\'>([^>]+)</a>|U', $html,
                    $matches);
                return $matches;
            }
        }
        return $matches;
    }

    public static function damePaisReal($pais)
    {
        switch ($pais) {
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
        return $pais;
    }

    public function damePaises()
    {
        $matches = array();
        preg_match_all('|country_of_origin=([^>]+)>([^>]+)<|U', $this->content, $matches);
        return $matches;
    }

    public function dameIdiomas()
    {
        $matches = array();
        preg_match_all('|primary_language=([^>]+)>([^>]+)<|U', $this->content, $matches);
        return $matches;
    }

    public function dameKeywords()
    {
        $matches = array();
        if (strpos($this->content, "Plot Keywords:") !== false) {
            $html = file_get_contents($this->url . "keywords");
            if (!empty($html)) {
                preg_match_all('|/keyword/([^>]+)\?|U', $html, $matches);
                return $matches;
            }
        }
        return $matches;
    }

    public function dameGeneros()
    {
        $matches = array();
        preg_match_all('|genre/([^>]+)>([^>]+)<|U', $this->content, $matches);
        return $matches;
    }

    public function dameCertificaciones()
    {
        $matches = array();
        if (!$this->isChapter && (strpos($this->content, "See all certifications") !== false)) {
            $html = file_get_contents($this->url . "parentalguide");
            if (!empty($html)) {
                preg_match_all('|<a href=\"/search/title\?certificates=([^>]+)\">([^>]+)</a>|U', $html, $matches);
            }
        }
        return $matches;
    }

    public function actualizaTemporada()
    {
        if ($this->isChapter) {
            $sub_coincidencias = array();
            preg_match_all('|>Season ([0-9]{1,2}) <|U', $this->content, $sub_coincidencias);
            if (!empty($sub_coincidencias[1][0]) && is_numeric($sub_coincidencias[1][0])) {
                $this->season = (int)($sub_coincidencias[1][0]);
            }
            $sub_coincidencias = array();
            preg_match_all('|> Episode ([0-9]{1,2})<|U', $this->content, $sub_coincidencias);
            if (!empty($sub_coincidencias[1][0]) && is_numeric($sub_coincidencias[1][0])) {
                $this->chapter = (int)($sub_coincidencias[1][0]);
            }
        }
    }
}

?>
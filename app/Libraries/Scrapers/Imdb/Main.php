<?php

namespace App\Libraries\Scrapers\Imdb;

use App\Libraries\Scrapers\Imdb\Pages\Home;
use App\Libraries\Scrapers\Imdb\Pages\Credits;
use App\Libraries\Scrapers\Imdb\Pages\EpisodesList;
use App\Libraries\Scrapers\Imdb\Pages\Keywords;
use App\Libraries\Scrapers\Imdb\Pages\Locations;
use App\Libraries\Scrapers\Imdb\Pages\ParentalGuide;
use App\Libraries\Scrapers\Imdb\Pages\ReleaseInfo;
use App\Libraries\Scrapers\Imdb\Utils\Cleaner;

class Main
{

    protected const RELEASE_INFO_PAGE = 'releaseinfo';
    protected const FULL_CREDITS_PAGE = 'fullcredits';
    protected const EPISODES_PAGE = 'episodes';
    protected const LOCATIONS_PAGE = 'locations';
    protected const KEYWORDS_PAGE = 'keywords';
    protected const PARENTAL_GUIDE_PAGE = 'parentalguide';

    protected $url;

    /** @var Pages\Home $homePage */
    protected $homePage;
    /** @var Pages\ReleaseInfo $releaseInfo */
    protected $releaseInfo;
    /** @var Pages\Credits $credits */
    protected $credits;
    /** @var Pages\EpisodesList $episodesList */
    protected $episodesList;
    /** @var Pages\Locations $locations */
    protected $locations;
    /** @var Pages\Keywords $keywords */
    protected $keywords;
    /** @var Pages\ParentalGuide $parentalGuide */
    protected $parentalGuide;

    public function __construct(int $imdbNumber)
    {
        $this->url = self::createUrl($imdbNumber);
    }

    public static function createUrl(int $imdbNumber): string
    {
        return 'https://www.imdb.com/title/tt' . str_pad($imdbNumber, 7, 0, STR_PAD_LEFT) . '/';
    }

    public function init(): void
    {
        $this->setHome()
            ->setTvShowFlags()
            ->setTitle()
            ->setImdbNumber()
            ->setReleaseInfo()
            ->setCredits()
            ->setEpisodesList()
            ->setLocations();
    }

    public function setHome(): Main
    {
        $this->homePage = (new Home())->setContent(Cleaner::getText($this->url));
        return $this;
    }

    public function getHome(): Home
    {
        if(!$this->homePage instanceof Home){
            $this->setHome();
        }
        return $this->homePage;
    }

    public function setReleaseInfo(): Main
    {
        $this->releaseInfo = new ReleaseInfo();
        if ($this->getHome()->haveReleaseInfo()) {
            $this->releaseInfo->setContent(Cleaner::getText($this->url . static::RELEASE_INFO_PAGE));
        }
        return $this;
    }

    public function getReleaseInfo(): ReleaseInfo
    {
        if(!$this->releaseInfo instanceof ReleaseInfo){
            $this->setReleaseInfo();
        }
        return $this->releaseInfo;
    }

    public function setCredits(): Main
    {
        $this->credits = (new Credits())->setContent(Cleaner::getText($this->url . static::FULL_CREDITS_PAGE));
        return $this;
    }

    public function getCredits(): Credits
    {
        if(!$this->credits instanceof Credits){
            $this->setCredits();
        }
        return $this->credits;
    }

    public function setEpisodesList(): Main
    {
        $this->episodesList = new EpisodesList();
        if ($this->getHome()->isTvShow()) {
            $this->episodesList->setContent(Cleaner::getText($this->url . static::EPISODES_PAGE));
        }
        return $this;
    }

    public function getEpisodesList(): EpisodesList
    {
        if(!$this->episodesList instanceof EpisodesList){
            $this->setEpisodesList();
        }
        return $this->episodesList;
    }

    public function setLocations(): Main
    {
        $this->locations = (new Locations())->setContent(Cleaner::getText($this->url . static::LOCATIONS_PAGE));
        return $this;
    }

    public function getLocations(): Locations
    {
        if(!$this->locations instanceof Locations){
            $this->setLocations();
        }
        return $this->locations;
    }

    public static function getMappedCountry(string $countryName): string
    {
        switch ($countryName) {
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
        return $countryName;
    }

    public function setKeywords(): Main
    {
        $this->keywords = (new Keywords())->setContent(Cleaner::getText($this->url . static::KEYWORDS_PAGE));
        return $this;
    }

    public function getKeywords(): Keywords
    {
        if(!$this->keywords instanceof Keywords){
            $this->setKeywords();
        }
        return $this->keywords;
    }

    public function setParentalGuide(): Main
    {
        $this->parentalGuide = (new ParentalGuide())->setContent(Cleaner::getText($this->url . static::PARENTAL_GUIDE_PAGE));
        return $this;
    }

    public function getParentalGuide(): ParentalGuide
    {
        if(!$this->parentalGuide instanceof ParentalGuide){
            $this->setParentalGuide();
        }
        return $this->parentalGuide;
    }
}
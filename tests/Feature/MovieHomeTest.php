<?php
/**
 * Created by PhpStorm.
 * User: sirio
 * Date: 04/05/2018
 * Time: 23:17
 */

namespace Tests\Feature;

use App\Libraries\Scrapers\Imdb\Pages\Home;
use PHPUnit\Framework\TestCase;

class MovieHomeTest extends TestCase
{

    /** @var Home $imdbScrapper */
    protected $imdbScrapper;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->imdbScrapper = (new Home())->setImdbNumber(1563742)->setContentFromUrl();
        parent::__construct($name, $data, $dataName);
    }

    public function testGetYear()
    {
        $this->assertEquals(2018, $this->imdbScrapper->getYear());
    }

    public function testGetTvShow()
    {
        $this->assertEquals(null, $this->imdbScrapper->getTvShow());
    }

    public function testHaveReleaseInfo()
    {
        $this->assertEquals(true, $this->imdbScrapper->haveReleaseInfo());
    }

    public function testGetLanguages()
    {

    }

    public function testIsTvShow()
    {
        $this->assertEquals(false, $this->imdbScrapper->isTvShow());
    }

    public function testGetTitle()
    {
        $this->assertEquals('Overboard', $this->imdbScrapper->getTitle());
    }

    public function testGetDuration()
    {
        $this->assertEquals(112, $this->imdbScrapper->getDuration());
    }

    public function testGetColor()
    {
        $this->assertEquals('Color', $this->imdbScrapper->getColor());
    }

    public function testGetRecommendations()
    {

    }

    public function testGetCountries()
    {

    }

    public function testIsEpisode()
    {
        $this->assertEquals(false, $this->imdbScrapper->isEpisode());
    }

    public function testGetGenres()
    {

    }

    public function testGetSound()
    {
        $this->assertEquals('Dolby Digital', $this->imdbScrapper->getSound());
    }

    public function testGetScore()
    {
        $this->assertGreaterThan(0, $this->imdbScrapper->getScore());
    }

    public function testGetVotes()
    {
        $this->assertGreaterThan(0, $this->imdbScrapper->getVotes());
    }
}

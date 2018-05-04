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

    /** @var Main $imdbScrapper */
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

    }

    public function testHaveReleaseInfo()
    {

    }

    public function testSetTvShowFlags()
    {

    }

    public function testGetLanguages()
    {

    }

    public function testIsTvShow()
    {

    }

    public function testGetTitle()
    {

    }

    public function testSetSeasonData()
    {

    }

    public function testGetDuration()
    {

    }

    public function testGetColor()
    {

    }

    public function testGetRecommendations()
    {

    }

    public function testGetCountries()
    {

    }

    public function testIsEpisode()
    {

    }

    public function testGetGenres()
    {

    }

    public function testSetTitle()
    {

    }

    public function testGetSound()
    {

    }

    public function testGetScore()
    {

    }

    public function testGetVotes()
    {

    }
}

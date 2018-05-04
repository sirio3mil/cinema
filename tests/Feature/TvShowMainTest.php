<?php
/**
 * Created by PhpStorm.
 * User: sirio
 * Date: 04/05/2018
 * Time: 23:18
 */

namespace Tests\Feature;

use App\Libraries\Scrapers\Imdb\Main;
use PHPUnit\Framework\TestCase;

class TvShowMainTest extends TestCase
{

    /** @var Main $imdbScrapper */
    protected $imdbScrapper;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->imdbScrapper = new Main(944947);
        parent::__construct($name, $data, $dataName);
    }

    public function testGetEpisodesList()
    {
        $this->assertNotEmpty($this->imdbScrapper->getEpisodesList()->getContent());
    }
}

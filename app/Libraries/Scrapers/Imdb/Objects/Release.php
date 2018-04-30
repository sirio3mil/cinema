<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 16:24
 */

namespace App\Libraries\Scrapers\Imdb\Objects;


class Release
{
    protected $country;

    protected $date;

    protected $details;

    public function setFromScrapper(array $match): Release
    {
        $this->setCountry($match[2]);
        $this->setDateFromScrapper($match[3], $match[5]);
        $this->setDetails($match[6]);
    }

    public function setCountry(string $country): Release
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setDateFromScrapper(string $dayMonth, int $year): Release
    {
        $this->date = \DateTime::createFromFormat("d F Y", "{$dayMonth} {$year}");
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDetails(string $details): Release
    {
        $this->details = $details;
        return $this;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

}
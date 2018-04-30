<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 30/04/2018
 * Time: 13:28
 */

namespace App\Libraries\Scrapers\Imdb\Pages;


class Page
{
    protected $content;

    public function setContent(string $content): Page
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 04/05/2018
 * Time: 11:22
 */

namespace App\Libraries\Scrapers\Imdb\Utils;

use Curl\Curl;

class Getter
{
    public static function getUrlContent(string $url): ?string
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)){
            throw new \Exception('Wrong getter url');
        }
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->get($url);
        if ($curl->error) {
            throw new \Exception($curl->errorMessage, $curl->errorCode);
        }
        return $curl->response;
    }
}
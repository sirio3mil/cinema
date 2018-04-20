<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 20/04/2018
 * Time: 14:05
 */

namespace App\Auth\Passwords;

use Illuminate\Support\Str;
use Illuminate\Auth\Passwords\PasswordBrokerManager as LaravelPasswordBrokerManager;

class CinemaPasswordBrokerManager extends LaravelPasswordBrokerManager
{
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new CinemaDatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }
}
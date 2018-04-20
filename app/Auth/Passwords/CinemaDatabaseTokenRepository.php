<?php
/**
 * Created by PhpStorm.
 * User: reynier.delarosa
 * Date: 20/04/2018
 * Time: 14:02
 */

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as LaravelDatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;


class CinemaDatabaseTokenRepository extends LaravelDatabaseTokenRepository
{
    protected function getPayload($email, $token)
    {
        return ['email' => $email, 'token' => $this->hasher->make($token), 'createdAt' => new Carbon];
    }

    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record &&
            ! $this->tokenExpired($record['createdAt']) &&
            $this->hasher->check($token, $record['token']);
    }

    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('createdAt', '<', $expiredAt)->delete();
    }
}
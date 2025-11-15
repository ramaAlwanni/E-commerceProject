<?php

namespace App\Models;

use App\Jobs\UpdatePersonalAccessToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public static function findToken($token)
    {
        $token = Cache::remember("PersonalAccessToken::$token", 600, function () use ($token) {
            return parent::findToken($token) ?? '_null_';
        });

        if ($token === '_null_') {
            return null;
        }
        return $token;
    }


    public function getTokenableAttribute()
    {
        return Cache::remember("PersonalAccessToken::{$this->id}::tokenable", 600, function () {
            return parent::tokenable()->first();
        });
    }


    public static function boot()
    {
        parent::boot();

        static::updating(function (self $personalAccessToken) {
            try {
                Cache::remember("PersonalAccessToken::lastUsageUpdate", 3600, function () use ($personalAccessToken) {
                    dispatch(new UpdatePersonalAccessToken($personalAccessToken, $personalAccessToken->getDirty()));
                    return now();
                });
            } catch (\Exception $e) {
                Log::critical($e->getMessage());
            }
            return false;
        });
    }


}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plausible Host
    |--------------------------------------------------------------------------
    |
    | Should only be used when self-hosting plausible.
    | All requests will be made to this domain.
    |
    */

    'host' => env('FILAMENT_PLAUSIBLE_HOST', 'https://plausible.io'),

    /*
    |--------------------------------------------------------------------------
    | Plausible Token
    |--------------------------------------------------------------------------
    |
    | API Token to access the APIs.
    | You can obtain an API key for your account by going to your user settings
    | page https://plausible.io/settings.
    |
    */

    'token' => env('PLAUSIBLE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Plausible Site ID
    |--------------------------------------------------------------------------
    |
    | Site ID you want to get stats of.
    | You can obtain this value by navigating to your site settings in Plausible
    | and grab the "value" of the domain field.
    |
    */

    'site_id' => env('FILAMENT_PLAUSIBLE_SITE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Periods
    |--------------------------------------------------------------------------
    |
    | Periods are the time spans of the statistics. You can configure how
    | the user can (or cannot) interact with them and what's the default value.
    |
    */

    'periods' => [
        /*
        |--------------------------------------------------------------------------
        | Default period
        |--------------------------------------------------------------------------
        |
        | Can be one of these values: day, 7d, 30d, month, 6mo, 12mo.
        |
        */
        'default' => '7d',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Configure how stats are cached.
    |
    */

    'cache' => [
        'enabled' => true,

        /*
        |--------------------------------------------------------------------------
        | Cache TTL
        |--------------------------------------------------------------------------
        |
        | Configure for how long the statistics are cached.
        | Valid units: millenium, century, decade, year, quarter, month, week,
        | day, weekday, hour, minute, second, microsecond.
        |
        | You can use their alias as well, like "hour" => "h", "day" => "d".
        |
        */
        'ttl' => '1h',
    ],

];

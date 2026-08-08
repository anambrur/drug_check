<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Master switch for the daily automatic runner
    |--------------------------------------------------------------------------
    */
    'enabled' => (bool) env('RANDOM_SELECTION_SCHEDULE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Time of day (app timezone) to run due protocols
    |--------------------------------------------------------------------------
    */
    'run_at' => env('RANDOM_SELECTION_RUN_AT', '01:00'),
];

<?php

return [
    'enabled' => env('EVOLUTION_ENABLED', false),
    'url' => rtrim(env('EVOLUTION_API_URL', ''), '/'),
    'api_key' => env('EVOLUTION_API_KEY', ''),
    'instance' => env('EVOLUTION_INSTANCE', 'cosmo_doctors'),
];

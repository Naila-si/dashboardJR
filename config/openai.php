<?php

return [

    'api_key' => env('OPENAI_API_KEY'),

    'organization' => env('OPENAI_ORGANIZATION', null),

    'base_uri' => env('OPENAI_BASE_URI', 'https://api.openai.com/v1'),

    'default_headers' => [],

    'request_timeout' => 30,

    'connect_timeout' => 10,
];

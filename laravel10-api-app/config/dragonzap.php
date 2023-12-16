<?php
use App\Assistants\BossyBettyAssistant;
use App\Assistants\WeatherAssistant;

/*
 * Licensed under GPLv2
 * Author: Daniel McCarthy
 * Dragon Zap Publishing
 * Website: https://dragonzap.com
 */


return [
    'openai' => [
        'key' => env('OPENAI_CHATGPT_KEY', 'default-key-value'),
    ],
    'assistants' => [
        'betty' => [
            'class' => BossyBettyAssistant::class,
            'id' => env('BOSSYBETTYAASSISTANT_ID')
        ],
        'weather' => [
            'class' => WeatherAssistant::class,
            'id' => env('WEATHERASSISTANT_ID'),
        ]
    ],
];

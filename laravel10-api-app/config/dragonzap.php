<?php
use App\Assistants\BossyBettyAssistant;

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
        'betty' => BossyBettyAssistant::class
    ]
];

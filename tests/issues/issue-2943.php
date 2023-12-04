<?php

declare(strict_types=1);

return [
    'issue-2943-A' => [
        'ua' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.0; +https://openai.com/gptbot)',
        'properties' => [
            'Comment' => 'OpenAI',
            'Browser' => 'GPTBot',
            'Browser_Type' => 'Bot',
            'Browser_Bits' => '0',
            'Browser_Maker' => 'OpenAI',
            'Browser_Modus' => 'unknown',
            'Version' => '1.0',
            'Platform_Bits' => '0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'ISSUE-2943-B' => [
        'ua' => 'Mozilla/5.0 (compatible; ImagesiftBot; +imagesift.com)',
        'properties' => [
            'Comment' => 'General Crawlers',
            'Browser' => 'ImagesiftBot',
            'Browser_Type' => 'Bot',
            'Browser_Bits' => '0',
            'Browser_Maker' => 'unknown',
            'Browser_Modus' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ]
];

<?php
declare(strict_types = 1);
return [
    'issue-2415-A' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 7.0;) AppleWebKit/537.36 (KHTML, like Gecko) Mobile Safari/537.36 (compatible; PetalBot;+https://aspiegel.com/petalbot)',
        'properties' => [
            'Browser' => 'PetalBot',
            'Browser_Type' => 'Bot/Crawler'
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2415-B' => [
        'ua' => 'Mozilla/5.0 (compatible; PetalBot;+https://aspiegel.com/petalbot',
        'properties' => [
            'Browser' => 'PetalBot',
            'Browser_Type' => 'Bot/Crawler'
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ]
];

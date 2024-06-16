<?php

declare(strict_types=1);

return [
    'issue-3016-A' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        'properties' => [
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            'Browser_Version' => 'Chrome',
            'Browser_Version' => '126',
            'Comment' => 'Chrome 126.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-3016-B' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/126.0',
        'properties' => [
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            'Browser_Version' => 'Firefox',
            'Browser_Version' => '126',
            'Comment' => 'Firefox 126.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-3016-C' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        'properties' => [
            'Platform' => 'MacOSX',
            'Platform_Version' => '10',
            'Platform_Maker' => 'Apple Inc',
            'Browser_Version' => 'Chrome',
            'Browser_Version' => '126',
            'Comment' => 'Chrome 126.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ]
];

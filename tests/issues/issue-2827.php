<?php

declare(strict_types=1);

return [
    'issue-2827-A' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        'properties' => [
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            'Browser_Version' => 'Chrome',
            'Browser_Version' => '115',
            'Comment' => 'Chrome 115.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2827-B' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0',
        'properties' => [
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            'Browser_Version' => 'Firefox',
            'Browser_Version' => '115',
            'Comment' => 'Firefox 115.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2827-C' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        'properties' => [
            'Platform' => 'MacOSX',
            'Platform_Version' => '10',
            'Platform_Maker' => 'Apple Inc',
            'Browser_Version' => 'Chrome',
            'Browser_Version' => '115',
            'Comment' => 'Chrome 115.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2827-D' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/115.0.5790.130 Mobile/15E148 Safari/604.1',
        'properties' => [
            'Platform' => 'iOS',
            'Platform_Maker' => 'Apple Inc',
            'Browser_Version' => 'Chrome',
            'Browser_Version' => '115',
            'Comment' => 'Chrome 115.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
];

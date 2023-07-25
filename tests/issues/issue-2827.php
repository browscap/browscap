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
];

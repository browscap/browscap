<?php
declare(strict_types = 1);

return [
    'issue-2537-A' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 12_0_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.3 Safari/605.1.15',
        'properties' => [
            "Platform"=> "macOS",
            "Platform_Description"=> "macOS",
            'Platform_Version' => '12.0',
            'Platform_Maker' => 'Apple Inc',
            "Browser_Version" => "Safari",
            "Browser_Version" => "15.3",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2537-B' => [
        'ua' => 'Mozilla/5.0 (iPad; CPU OS 15_2 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/15.4 Safari/602.1.50',
        'properties' => [
            "Browser_Version" => "Safari",
            "Browser_Version" => "15.3",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2537-C' => [
        'ua' => 'Mozilla/5.0 (iPad; CPU OS 14_2 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/14.2 Safari/602.1.50',
        'properties' => [
            "Browser_Version" => "Safari",
            "Browser_Version" => "14.2",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2537-D' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-G973F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/15.0 Chrome/90.0.4430.210 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Samsung Browser 15.0',
            'Browser' => 'Samsung Browser',
            'Version' => '15',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2537-E' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-T295) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/14.2 Chrome/87.0.4280.141 Safari/537.36',
        'properties' => [
            'Comment' => 'Samsung Browser 14.2',
            'Browser' => 'Samsung Browser',
            'Version' => '14.2',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
];

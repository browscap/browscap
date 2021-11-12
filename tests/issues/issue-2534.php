<?php
declare(strict_types = 1);

return [
    'issue-2534-A' => [
        'ua' => 'Mozilla/5.0 (iPad; CPU OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Mobile/15E148 Safari/604.1',
        'properties' => [
            "Platform": "MacOSX",
            "Platform_Description": "Mac OS X",
            'Platform_Version' => '12.2',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2534-B' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36',
        'properties' => [
            "Platform": "MacOSX",
            "Platform_Description": "Mac OS X",
            'Platform_Version' => '11.1',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2534-C' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
        'properties' => [
            "Platform": "iOS",
            "Platform_Version": "15.1",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2534-D' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_8_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari/604.1',
        'properties' => [
            "Platform": "iOS",
            "Platform_Version": "14.8",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ]
];

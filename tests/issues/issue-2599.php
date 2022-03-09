<?php
declare(strict_types = 1);

return [
    'issue-2599-A' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4758.102 Safari/537.36',
        'properties' => [
            "Platform"=> "Win10",
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            "Browser_Version" => "Chrome",
            "Browser_Version" => "100",
            "Comment" => "Chrome 100.0",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2599-B' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0',
        'properties' => [
            "Platform"=> "Win10",
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            "Browser_Version" => "Firefox",
            "Browser_Version" => "100",
            "Comment" => "Firefox 100.0"
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2599-A' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.4758.102 Safari/537.36',
        'properties' => [
            "Platform"=> "Win10",
            'Platform_Version' => '10.0',
            'Platform_Maker' => 'Microsoft Corporation',
            "Browser_Version" => "Chrome",
            "Browser_Version" => "107",
            "Comment" => "Chrome 107.0",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
];

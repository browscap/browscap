<?php
declare(strict_types = 1);

return [
    'issue-2651-A' => [
        'ua' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/17.0 Chrome/96.0.4664.104 Safari/537.36',
        'properties' => [
            "Platform"=> "Android",
            'Platform_Maker' => 'Google Inc',
            "Browser_Version" => "17.0",
            "Comment" => "Samsung Browser 17.0",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2651-B' => [
        'ua' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/16.2 Chrome/92.0.4515.166 Safari/537.36',
        'properties' => [
            "Platform"=> "Android",
            'Platform_Maker' => 'Google Inc',
            "Browser_Version" => "16.2",
            "Comment" => "Samsung Browser 16.2",
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2651-C' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/17.0 Chrome/96.0.4664.104 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Samsung Browser 17.0',
            'Browser' => 'Samsung Browser',
            'Version' => '17.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-2651-D' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 10; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/16.2 Chrome/92.0.4515.166 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Samsung Browser 16.2',
            'Browser' => 'Samsung Browser',
            'Version' => '16.2',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
];

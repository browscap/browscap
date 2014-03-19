<?php

return [
    'issue-176-A' => [
        'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:27.0) Gecko/20100101 Firefox/27.0',
        [
            'Browser' => 'Firefox',
            'Version' => '27.0',
            'Win64' => false,
            'isMobileDevice' => false,
            'Platform' => 'Linux',
            'Crawler' => false,
            'Device_Type' => 'Desktop',
            'Device_Pointing_Method' => 'mouse'
        ],
    ],
    'issue-176-B' => [
        'Mozilla/5.0 (Windows NT 6.1; rv:27.0) Gecko/20100101 Firefox/27.0',
        [
            'Browser' => 'Firefox',
            'Version' => '27.0',
            'Win64' => false,
            'isMobileDevice' => false,
            'Platform' => 'Win7',
            'Platform_Version' => '6.1',
            'Crawler' => false,
            'Device_Type' => 'Desktop',
            'Device_Pointing_Method' => 'mouse'
        ],
    ],
];

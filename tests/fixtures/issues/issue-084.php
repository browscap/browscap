<?php

return [
    'issue-84-H' => [
        'Mozilla/5.0 (Linux; U) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4',
        [
            'Browser' => 'Chrome',
            'Version' => '22.0',
            'Platform' => 'Linux',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-84-G' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.4; pt-BR; H5000 Build/IMM76D) AppleWebKit/534.31 (KHTML, like Gecko) UCBrowser/9.3.0.321 U3/0.8.0 Mobile Safari/534.31',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.3',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Device_Name' => 'unknown',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'unknown'
        ],
    ],
    'issue-84-F' => [
        'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)',
        [
            'Browser' => 'Fake IE',
            'Crawler' => true,
        ],
    ],
    'issue-84-E' => [
        'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; BTRS101607; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C; Ask',
        [
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-84-D' => [
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 7.1; Trident/5.0)',
        [
            'Browser' => 'Fake IE',
            'Crawler' => true,
        ],
    ],
    'issue-84-C' => [
        'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/4.0; InfoPath.2; SV1; .NET CLR 2.0.50727; WOW64)',
        [
            'Browser' => 'Fake IE',
            'Crawler' => true,
        ],
    ],
    'issue-84-B' => [
        'Mozilla/5.0 (Linux; U; Android 4.2.2; es-sa; LG-D805 Build/JDQ39B) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.2 Mobile Safari/534.30',
        [
            'Browser' => 'Android',
            'Version' => '4.2',
            'Platform' => 'Android',
            'Platform_Version' => '4.2',
            'isMobileDevice' => true,
            'Device_Name' => 'D805',
            'Device_Maker' => 'LG',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen'
        ],
    ],
    'issue-84-A' => [
        'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0',
        [
            'Browser' => 'Fake IE',
            'Crawler' => true,
        ],
    ],
];

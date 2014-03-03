<?php

return [
    'issue-92-B' => [
        'Mozilla/5.0 (Series40; Nokia306/03.63; Profile/MIDP-2.1 Configuration/CLDC-1.1) Gecko/20100401 S40OviBrowser/3.9.0.0.22',
        [
            'Browser' => 'Nokia Proxy Browser',
            'Version' => '3.9',
            'Platform' => 'SymbianOS',
            'isMobileDevice' => true,
        ],
    ],
    'issue-92-A' => [
        'Mozilla/5.0 (compatible; proximic; +http://www.proximic.com/info/spider.php)',
        [
            'Browser' => 'Proximic Spider',
            'Crawler' => true,
        ],
    ],
];

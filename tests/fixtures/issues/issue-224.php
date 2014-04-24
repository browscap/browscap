<?php

return [
    'issue-224-A' => [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 1084) AppleWebKit/536.29.13 (KHTML like Gecko) Version/6.0.4 Safari/536.29.13',
        [
            'Browser' => 'Fake Safari',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-224-B' => [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 107) AppleWebKit/534.48.3 (KHTML like Gecko) Version/5.1 Safari/534.48.3',
        [
            'Browser' => 'Fake Safari',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-228' => [
        'Mozilla/5.0 (X11; Windows x86_64) AppleWebKit/536.11 (KHTML, like Gecko) chrome/22.0.1190.0 Safari/536.11',
        [
            'Browser' => 'Fake Chrome',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
];

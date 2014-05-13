<?php

return [
    'issue-270-A' => [
        'Mozilla/5.0 (compatible; Alexabot/1.0; +http://www.alexa.com/help/certifyscan; certifyscan@alexa.com)',
        [
            'Browser' => 'Alexabot',
            'Version' => '1.0',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'Alexa Internet, Inc',
        ],
    ],
    'issue-270-B' => [
        'Mozilla/5.0 (compatible; EasouSpider; +http://www.easou.com/search/spider.html)',
        [
            'Browser' => 'EasouSpider',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'easou.com',
        ],
    ],
    'issue-270-C' => [
        'Mozilla/4.0 (compatible; MSIE 4.01; Digital AlphaServer 1000A 4/233; Windows NT; Powered By 64-Bit Alpha Processor)',
        [
            'Browser' => 'IE',
            'Version' => '4.01',
            'Platform' => 'WinNT',
            'isMobileDevice' => false,
            'Crawler' => false,
            'Browser_Maker' => 'Microsoft Corporation',
            'Platform_Maker' => 'Microsoft Corporation',
        ],
    ],
    'issue-270-D' => [
        'NerdyBot',
        [
            'Browser' => 'NerdyBot',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'NerdyData',
        ],
    ],
    'issue-270-E' => [
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64) KomodiaBot/1.0',
        [
            'Browser' => 'KomodiaBot',
            'Version' => '1.0',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'Komodia Inc',
            'Platform' => 'Win7'
        ],
    ],
    'issue-270-F' => [
        'WebTarantula.com Crawler',
        [
            'Browser' => 'WebTarantula',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'webtarantula.com',
        ],
    ],
    'issue-270-G' => [
        'Mozilla/4.0 (compatible; Synapse)',
        [
            'Browser' => 'Apache Synapse',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'Apache Foundation',
        ],
    ],
    'issue-270-H' => [
        'ScreenerBot Crawler Beta 2.0 (+http://www.ScreenerBot.com)',
        [
            'Browser' => 'ScreenerBot',
            'Version' => '2.0',
            'isMobileDevice' => false,
            'Crawler' => true,
            'Browser_Maker' => 'ScreenerBot.com',
            'Beta' => true
        ],
    ],
];

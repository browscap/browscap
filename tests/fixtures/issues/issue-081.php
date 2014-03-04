<?php

return [
    'issue-81-G' => [
        'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)',
        [
            'Browser' => 'Mail.RU',
            'Version' => '2.0',
            'Crawler' => true,
        ],
    ],
    'issue-81-F' => [
        'masscan/1.0 (https://github.com/robertdavidgraham/masscan)',
        [
            'Browser' => 'Download Accelerator',
            'Version' => '1.0',
            'Crawler' => true,
        ],
    ],
    'issue-81-E' => [
        'ZmEu',
        [
            'Browser' => 'ZmEu',
            'Crawler' => true,
        ],
    ],
    'issue-81-D' => [
        'Mozilla/5.0 (X11; Linux i686; U;rv: 1.7.13) Gecko/20070322 Kazehakase/0.4.4.1',
        [
            'Browser' => 'Kazehakase',
            'Platform' => 'Linux',
            'Version' => '0.4',
            'Crawler' => false,
        ],
    ],
    'issue-81-C' => [
        'woobot/1.1',
        [
            'Browser' => 'WooRank',
            'Version' => '1.1',
            'Crawler' => true,
        ],
    ],
    'issue-81-B' => [
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko; Google Page Speed Insights) Chrome/27.0.1453 Safari/537.36',
        [
            'Browser' => 'Google Page Speed',
            'Crawler' => true,
        ],
    ],
    'issue-81-A' => [
        'Sogou web spider/4.0(+http://www.sogou.com/docs/help/webmasters.htm#07)',
        [
            'Browser' => 'sogou web spider',
            'Version' => '4.0',
            'Crawler' => true,
        ],
    ],
];

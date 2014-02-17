<?php

return [
    'issue-128' => [
        'Mozilla/5.0 (compatible; Another Web Mining Tool 1.0; +none; awmt)',
        [
            'Browser' => 'Another Web Mining Tool',
            'Version' => '1.0',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-122' => [
        'Mozilla/5.0 (Windows NT 6.1; rv:6.0) Gecko/20110814 Firefox/6.0 Google (+https://developers.google.com/+/web/snippet/)',
        [
            'Browser' => 'Google Web Snippet',
            'Platform' => 'Win7',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-120' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0_1 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko; Google Page Speed Insights) Version/6.0 Mobile/10A525 Safari/8536.25',
        [
            'Browser' => 'Google Page Speed',
            'Platform' => 'iOS',
            'Platform_Version' => '6.0',
            'isMobileDevice' => true,
            'Crawler' => true,
        ],
    ],
    'issue-117' => [
        'netEstate NE Crawler (+http://www.website-datenbank.de/)',
        [
            'Browser' => 'netEstate NE Crawler',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-114-D' => [
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1200.0 Iron/21.0.1200.0 Safari/537.1',
        [
            'Browser' => 'Iron',
            'Version' => '21.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-114-C' => [
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.30 (KHTML, like Gecko) Iron/12.0.750.0 Chrome/12.0.750.0 Safari/534.30',
        [
            'Browser' => 'Iron',
            'Version' => '12.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-114-B' => [
        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.4) Gecko/20091016 Firefox/3.5.4 (.NET CLR 3.5.30729)',
        [
            'Browser' => 'Firefox',
            'Version' => '3.5',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-114-A' => [
        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.7) Gecko/2009021910 Firefox/3.0.7 (.NET CLR 3.5.30729)',
        [
            'Browser' => 'Firefox',
            'Version' => '3.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-102' => [
        'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.16) Gecko/20110322 Fedora/3.6.16-1.fc14 Firefox/3.6.16',
        [
            'Browser' => 'Firefox',
            'Version' => '3.6',
            'Platform' => 'Linux',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-99-C' => [
        'DavClnt',
        [
            'Browser' => 'Microsoft-WebDAV',
            'Platform' => 'Win7',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-99-B' => [
        'XING-contenttabreceiver/2.0',
        [
            'Browser' => 'XING Contenttabreceiver',
            'Version' => '2.0',
            'isMobileDevice' => false,
            'Crawler' => true,
        ],
    ],
    'issue-99-A' => [
        'Opera/9.60 (Windows NT 5.1; U; de) Presto/2.1.1',
        [
            'Browser' => 'Opera',
            'Version' => '9.60',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-96-AS' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.4; en-US; GT-S7562 Build/IMM76I) AppleWebKit/534.31 (KHTML, like Gecko) UCBrowser/9.3.1.344 U3/0.8.0 Mobile Safari/534.31',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.3',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AR' => [
        'MicromaxX650 ASTRO36_TD/v3 MAUI/10A1032MP_ASTRO_W1052 Release/31.12.2010 Browser/Opera Sync/SyncClient1.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera/9.80 (MTK; U; en-US) Presto/2.5.28 Version/10.10',
        [
            'Browser' => 'Opera',
            'Version' => '10.10',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-96-AQ' => [
        'Opera/9.80 (MAUI Runtime; Opera Mini/4.4.31762/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '4.4',
            'Platform' => 'Android',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AP' => [
        'Opera/9.80 (Bada; Opera Mini/6.5/34.1016; U; tr) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '6.5',
            'Platform' => 'Bada',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AO' => [
        'Opera/9.80 (BREW; Opera Mini/6.5/34.1016; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '6.5',
            'Platform' => 'Brew',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AN' => [
        'UCWEB/2.0 (Linux; U; Adr 4.1.1; en-US; AURUS III) U2/1.0.0 UCBrowser/9.3.0.321 U2/1.0.0 Mobile',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.3',
            'Platform' => 'Android',
            'Platform_Version' => '4.1',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AM' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.3; en-us; KFTT Build/IML74K) AppleWebKit/535.7 (KHTML, like Gecko) CrMo/16.0.912.77 Safari/535.7',
        [
            'Browser' => 'Chrome',
            'Version' => '16.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AL' => [
        'JUC(Linux;U;Android2.3.5;Zh_cn;Micromax A35;480*800;)UCWEB7.8.0.95/139/444',
        [
            'Browser' => 'UC Browser',
            'Version' => '7.8',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AK' => [
        'OneBrowser/4.2.0/Adr(Linux; U; Android 2.3.5; en-us; Micromax A27 Build/MocorDroid2.3.5) AppleWebKit/533.1 (KHTML, like Gecko) Mobile Safari/533.1',
        [
            'Browser' => 'OneBrowser',
            'Version' => '4.2',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AJ' => [
        'UCWEB/2.0 (Linux; U; Adr 2.3.5; en-US; Micromax A27) U2/1.0.0 UCBrowser/9.3.2.349 U2/1.0.0 Mobile',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.3',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AI' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.4; en-US; GT-S7562 Build/IMM76I) AppleWebKit/534.31 (KHTML, like Gecko) UCBrowser/9.3.2.349 U3/0.8.0 Mobile Safari/534.31',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.3',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AH' => [
        'Mozilla/5.0 (Linux; U; en-us; KFTHWI Build/JDQ39) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.12 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.12',
            'Platform' => 'Android',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AG' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0',
        [
            'Browser' => 'Firefox',
            'Version' => '29.0',
            'Platform' => 'Win8.1',
            'Win64' => true,
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-96-AF' => [
        'SAMSUNG-GT-B7722/DDKD1 SHP/VPP/R5 Dolfin/1.5 Nextreaming SMM-MMS/1.2.0 profile/MIDP-2.1 configuration/CLDC-1.1',
        [
            'Browser' => 'Dolfin',
            'Version' => '1.5',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AE' => [
        'OneBrowser/4.2.0/Adr(Linux; U; Android 4.0.4; en-gb; GT-S7562 Build/IMM76I) AppleWebKit/533.1 (KHTML, like Gecko) Mobile Safari/533.1',
        [
            'Browser' => 'OneBrowser',
            'Version' => '4.2',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AD' => [
        'Opera/9.80 (J2ME/MIDP; Opera Mini; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AC' => [
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0',
        [
            'Browser' => 'Firefox',
            'Version' => '29.0',
            'Platform' => 'Win7',
            'Win64' => true,
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-96-AB' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.4; en-US; GT-S7562 Build/IMM76I) AppleWebKit/534.31 (KHTML, like Gecko) UCBrowser/9.2.3.324 U3/0.8.0 Mobile Safari/534.31',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.2',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-AA' => [
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:29.0) Gecko/20100101 Firefox/29.0',
        [
            'Browser' => 'Firefox',
            'Version' => '29.0',
            'Platform' => 'Win7',
            'Win64' => true,
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-96-Z' => [
        'Mozilla/5.0 (Linux; U; Android 4.1.1; en-gb; ALCATEL ONE TOUCH 4030D Build/JRO03C) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.1 Mobile Safari/534.30',
        [
            'Browser' => 'Android',
            'Version' => '4.1',
            'Platform' => 'Android',
            'Platform_Version' => '4.1',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-Y' => [
        'MT6515M-A1+/1.0 Linux/2.6.35.7 Android 2.3.6 Release/12.09.2012 Browser/AppleWebKit533.1 (KHTML, like Gecko) Mozilla/5.0 Mobile',
        [
            'Browser' => 'Android',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-X' => [
        'UCWEB/2.0 (MIDP-2.0; U; Adr 4.1.2; en-US; GT-S7262) U2/1.0.0 UCBrowser/8.8.1.359 U2/1.0.0 Mobile',
        [
            'Browser' => 'UC Browser',
            'Version' => '8.8',
            'Platform' => 'Android',
            'Platform_Version' => '4.1',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-W' => [
        'UCWEB/2.0 (Linux; U; Opera Mini/7.1.32052/30.3697; en-US; GT-S7262) U2/1.0.0 UCBrowser/8.8.1.359 Mobile',
        [
            'Browser' => 'UC Browser',
            'Version' => '8.8',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-V' => [
        'SAMSUNG-GT-S3802 Opera/9.80 (J2ME/MIDP; Opera Mini/7.1.32830/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '7.1',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-U' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.4; en-US; GT-S7562 Build/IMM76I) AppleWebKit/534.31 (KHTML, like Gecko) UCBrowser/9.3.1.344 U3/0.8.0 Mobile Safari/534.31',
        [
            'Browser' => 'UC Browser',
            'Version' => '9.3',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-T' => [
        'MicromaxX650 ASTRO36_TD/v3 MAUI/10A1032MP_ASTRO_W1052 Release/31.12.2010 Browser/Opera Sync/SyncClient1.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera/9.80 (MTK; U; en-US) Presto/2.5.28 Version/10.10',
        [
            'Browser' => 'Opera',
            'Version' => '10.10',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
    'issue-96-S' => [
        'SAMSUNG-GT-C6712/C6712DDKG1 SHP/VPP/R5 Dolfin/2.0 NexPlayer/3.0 SMM-MMS/1.2.0 profile/MIDP-2.1 configuration/CLDC-1.1 OPN-B',
        [
            'Browser' => 'Dolfin',
            'Version' => '2.0',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-R' => [
        'Opera/9.80 (J2ME/MIDP; Opera Mini/6.5.27510/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '6.5',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-Q' => [
        'SAMSUNG-GT-C3262 Opera/9.80 (J2ME/MIDP; Opera Mini/7.0.30281/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '7.0',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-P' => [
        'iBrowser/2.7 Mozilla/5.0 (Linux; U; Android 2.3.6; en-in; A1+ Build/MocorDroid2.3.5) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
        [
            'Browser' => 'iBrowser',
            'Version' => '2.7',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-O' => [
        'Mozilla/5.0 (Linux; U; Android 4.1.1; en-us; AURUS III Build/JRO03C) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.1 Mobile Safari/534.30',
        [
            'Browser' => 'Android',
            'Version' => '4.1',
            'Platform' => 'Android',
            'Platform_Version' => '4.1',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-N' => [
        'SAMSUNG-GT-E2202 Opera/9.80 (J2ME/MIDP; Opera Mini/4.4.32420/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '4.4',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-M' => [
        'Mozilla/5.0 (Linux; U; en-us; KFOT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.11 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.11',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-L' => [
        'SAMSUNG-GT-E2252 Opera/9.80 (J2ME/MIDP; Opera Mini/4.4.29595/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '4.4',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-K' => [
        'Mozilla/5.0 (Linux;U; Android 2.3.5; en-us; Micromax A40 Build/IML74K) AppleWebKit/534.30(khtml,LIKE Gecko) Version/4.0 Mobile Safari/534.30',
        [
            'Browser' => 'Android',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-J' => [
        'SAMSUNG-GT-E1282T Opera/9.80 (J2ME/MIDP; Opera Mini/4.4.31524/34.1016; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '4.4',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-I' => [
        'Mozilla/5.0 (Linux; U; en-us; KFSOWI Build/JDQ39) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.8 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.8',
            'Platform' => 'Android',
            'Platform_Version' => '4.2',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-H' => [
        'Mozilla/5.0 (Linux; U; en-us; KFOT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.8 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.8',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-G' => [
        'Mastone_G9_TD/V2.00 Release/3.19.2012 Mozilla/5.0 (Linux; U; Android 2.3.5) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
        [
            'Browser' => 'Android',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-F' => [
        'Opera/9.80 (SpreadTrum; Opera Mini/4.4.31492/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '4.4',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-E' => [
        'Mozilla/5.0 (Linux; U; en-us; KFTT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.11 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.11',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-D' => [
        'Mozilla/5.0 (Linux; U; en-us; KFTT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.8 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.8',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-C' => [
        'SAMSUNG-GT-E1282T Opera/9.80 (J2ME/MIDP; Opera Mini/4.4.31524/34.1000; U; en) Presto/2.8.119 Version/11.10',
        [
            'Browser' => 'Opera Mini',
            'Version' => '4.4',
            'Platform' => 'JAVA',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-B' => [
        'Mozilla/5.0 (Linux; U; Android 2.3.5; en-us; Micromax A35 Build/IML74K) AppleWebKit/534.30(KHTML,like Gecko) Version/4.0 Mobile Safari/534.30',
        [
            'Browser' => 'Android',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-96-A' => [
        'Mozilla/5.0 (Linux; U; Android 2.3.5; en-us; Micromax A27 Build/IML74K) AppleWebKit/534.30(KHTML,like Gecko) Version/4.0 Mobile Safari/534.30',
        [
            'Browser' => 'Android',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
            'Crawler' => false,
        ],
    ],
    'issue-97' => [
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Iron/30.0.1650.0 Chrome/30.0.1650.0 Safari/537.36',
        [
            'Browser' => 'Iron',
            'Version' => '30.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
            'Crawler' => false,
        ],
    ],
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
        ],
    ],
    'issue-84-A' => [
        'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0',
        [
            'Browser' => 'Fake IE',
            'Crawler' => true,
        ],
    ],
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
    'issue-mobiles-F' => [
        'Mozilla/5.0 (Linux; U; Android 2.3.4; en-us; GT-I9100G Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) FlyFlow/2.5 Version/4.0 Mobile Safari/533.1 baidubrowser/042_6.3.5.2_diordna_008_084/gnusmas_01_4.3.2_G0019I-TG/7400001l/AFCD145CE647EC590CFE42154CB19B89%7C274573340474753/1',
        [
            'Browser' => 'FlyFlow',
            'Version' => '2.5',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
        ],
    ],
    'issue-mobiles-E' => [
        'Dalvik/1.6.0 (Linux; U; Android 4.0.4; GT-I9100 Build/IMM76D)',
        [
            'Browser' => 'Dalvik',
            'Version' => '1.6',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-mobiles-D' => [
        'Dalvik/1.4.0 (Linux; U; Android 2.3.6; GT-I9100G Build/GINGERBREAD)',
        [
            'Browser' => 'Dalvik',
            'Version' => '1.4',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
        ],
    ],
    'issue-mobiles-C' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.4; de-de; GT-I9100 Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Dolphin/INT-1.0 Mobile Safari/534.30',
        [
            'Browser' => 'Dolfin',
            'Version' => '1.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-mobiles-B' => [
        'Mozilla/5.0 (Linux; U; Android 4.0.3; de-de; GT-I9100 Build/IML74K) AppleWebKit/535.7 (KHTML, like Gecko) CrMo/16.0.912.77 Mobile Safari/535.7',
        [
            'Browser' => 'Chrome',
            'Version' => '16.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-mobiles-A' => [
        'Mozilla/5.0 (Linux; U; de-de; GT-I9100 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Safari/533.1',
        [
            'Browser' => 'Android',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
        ],
    ],
    'issue-82' => [
        'Mozilla/5.0 (X11; U; Linux; en-US) AppleWebKit/532.4 (KHTML, like Gecko) Arora/0.10.2 Safari/532.4',
        [
            'Browser' => 'Arora',
            'Version' => '0.10',
            'Platform' => 'Linux',
            'isMobileDevice' => false,
        ],
    ],
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
    'issue-77-G' => [
        'SAMSUNG-SGH-E250/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Browser/6.2.3.3.c.1.101 (GUI) MMP/2.0 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)',
        [
            'Browser' => 'Googlebot-Mobile',
            'Version' => '2.1',
            'Crawler' => true,
        ],
    ],
    'issue-77-F' => [
        'DoCoMo/2.0 N905i(c100;TB;W24H16) (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)',
        [
            'Browser' => 'Googlebot-Mobile',
            'Version' => '2.1',
            'Crawler' => true,
        ],
    ],
    'issue-77-E' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        [
            'Browser' => 'Googlebot',
            'Version' => '2.1',
            'Crawler' => true,
        ],
    ],
    'issue-77-D' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)',
        [
            'Browser' => 'Googlebot-Mobile',
            'Version' => '2.1',
            'Crawler' => true,
        ],
    ],
    'issue-77-C' => [
        'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        [
            'Browser' => 'Googlebot',
            'Version' => '2.1',
            'Crawler' => true,
        ],
    ],
    'issue-77-B' => [
        'integrity/4',
        [
            'Browser' => 'Integrity',
            'Crawler' => true,
            'Platform' => 'MacOSX',
        ],
    ],
    'issue-77-A' => [
        'Mozilla/5.0 (Linux; U; en-us; KFTT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/3.11 Safari/535.19 Silk-Accelerated=true',
        [
            'Browser' => 'Silk',
            'Version' => '3.11',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-71-C' => [
        'Mozilla/5.0 (Linux; U; Android 2.3.4; de-de; GT-I9100 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 Maxthon/4.0.3.3000',
        [
            'Browser' => 'Maxthon',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'isMobileDevice' => true,
        ],
    ],
    'issue-71-B' => [
        'MQQBrowser/3.0/Mozilla/5.0 (Linux; U; Android 4.0.3; de-de; GT-I9100 Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
        [
            'Browser' => 'MQQBrowser',
            'Version' => '3.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-71-A' => [
        'JUC (Linux; U; 4.0.4; zh-cn; GT-I9100; 480*800) UCWEB7.9.0.94/139/444',
        [
            'Browser' => 'UC Browser',
            'Version' => '7.9',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-69' => [
        'ContextAd Bot 1.0',
        [
            'Browser' => 'ContextAd Bot',
            'Version' => '1.0',
            'Crawler' => true,
        ],
    ],
    'issue-62' => [
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.3; WOW64; Trident/7.0; Touch; .NET4.0E; .NET4.0C; Tablet PC 2.0)',
        [
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-49' => [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.73.11 (KHTML, like Gecko) Version/7.0.1 Safari/537.73.11',
        [
            'Browser' => 'Safari',
            'Version' => '7.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.9',
            'isMobileDevice' => false,
        ],
    ],
    'issue-38-b' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_2 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) CriOS/30.0.1599.16 Mobile/11A501 Safari/8536.25',
        [
            'Browser' => 'Chrome',
            'Version' => '30.0',
            'Platform' => 'iOS',
            'Platform_Version' => '7.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-38-a' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) CriOS/28.0.1500.16 Mobile/11A4449d Safari/8536.25',
        [
            'Browser' => 'Chrome',
            'Version' => '28.0',
            'Platform' => 'iOS',
            'Platform_Version' => '7.0',
            'isMobileDevice' => true,
        ],
    ],
    'issue-50-2' => [
        'Mozilla/5.0 (X11; Linux) AppleWebKit/531.2+ Midori/0.3',
        [
            'Browser' => 'Midori',
            'Version' => '0.3',
            'Platform' => 'Linux',
            'isMobileDevice' => true,
        ],
    ],
    'issue-50-1' => [
        'Mozilla/5.0 (X11; Linux) AppleWebKit/537.32 (KHTML, like Gecko) Chrome/18.0.1025.133 Safari/537.32 Midori/0.5',
        [
            'Browser' => 'Midori',
            'Version' => '0.5',
            'Platform' => 'Linux',
            'isMobileDevice' => true,
        ],
    ],
    'issue-51-3' => [
        'Mozilla/5.0 (compatible; grapeFX/0.9; crawler@grapeshot.co.uk',
        [
            'Browser' => 'grapeFX',
            'Crawler' => true,
        ],
    ],
    'issue-51-2' => [
        'Mozilla/5.0 (compatible; GrapeshotCrawler/2.0; +http://www.grapeshot.co.uk/crawler.php)',
        [
            'Browser' => 'GrapeshotCrawler',
            'Crawler' => true,
        ],
    ],
    'issue-51-1' => [
        'niki-bot',
        [
            'Browser' => 'NikiBot',
            'Crawler' => true,
        ],
    ],
    'issue-52' => [
        'Mozilla/5.0 (Windows NT; Win64; x64; rv:26.0) Gecko/20100101 Firefox/26.0 Waterfox/26.0',
        [
            'Browser' => 'Waterfox',
            'Version' => '26.0',
            'Platform' => 'WinNT',
            'Win64' => true,
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-8' => [
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '31.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-7' => [
        'Mozilla/5.0 (Windows NT 5.1; rv:26.0) Gecko/20100101 Firefox/26.0',
        [
            'Browser' => 'Firefox',
            'Version' => '26.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-6' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36 OPR/18.0.1284.49',
        [
            'Browser' => 'Opera',
            'Version' => '18.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-5' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko',
        [
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-4' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '31.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-3' => [
        'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
        [
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win7',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-2' => [
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0',
        [
            'Browser' => 'Firefox',
            'Version' => '25.0',
            'Platform' => 'Win7',
            'isMobileDevice' => false,
        ],
    ],
    'issue-47-phpbrowscap-1' => [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:25.0) Gecko/20100101 Firefox/25.0 FirePHP/0.7.4',
        [
            'Browser' => 'Firefox',
            'Version' => '25.0',
            'Platform' => 'MacOSX',
            'isMobileDevice' => false,
        ],
    ],
    'issue-53' => [
        'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 4 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19',
        [
            'Browser' => 'Chrome',
            'Version' => '18.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.2',
            'isMobileDevice' => true,
        ],
    ],
    'chrome34' => [
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1760.0 Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '34.0',
            'Platform' => 'Win7',
            'isMobileDevice' => false,
        ],
    ],
    'issue-7' => [
        'Mozilla/5.0 (Linux; Android 4.2.2; Nexus 4 Build/JDQ39E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.90 Mobile Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '27.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.2',
            'isMobileDevice' => true,
        ],
    ],
    'issue-48' => [
        'Mozilla/5.0 (Android; Mobile; rv:26.0) Gecko/26.0 Firefox/26.0',
        [
            'Browser' => 'Firefox',
            'Version' => '26.0',
            'Platform' => 'Android',
            'isMobileDevice' => true,
        ],
    ],
    'chrome31' => [
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '31.0',
            'Platform' => 'Linux',
            'isMobileDevice' => false,
        ],
    ],
    'issue-46-a' => [
        'Mozilla/5.0 (compatible; Ezooms/1.0; ezooms.bot@gmail.com)',
        [
            'Browser' => 'Ezooms',
        ],
    ],
    'issue-46-b' => [
        'Mozilla/5.0 (compatible; Ezooms/1.0; help@moz.com)',
        [
            'Browser' => 'Ezooms',
        ],
    ],
    'issue-45' => [
        'Mozilla/5.0 (compatible; SISTRIX Crawler; http://crawler.sistrix.net/)',
        [
            'Browser' => 'SISTRIX',
            'Crawler' => true
        ],
    ],
    'issue-42' => [
        'Mozilla/5.0 (BB10; Kbd) AppleWebKit/537.35+ (KHTML, like Gecko)',
        [
            'Browser' => 'BlackBerry',
            'Platform' => 'BlackBerry OS',
            'isMobileDevice' => true,
        ],
    ],
    'issue-41' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36 OPR/18.0.1284.49',
        [
            'Browser' => 'Opera',
            'Version' => '18.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-39' => [
        'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.1',
        [
            'Browser' => 'Firefox',
            'Version' => '2.0',
            'Platform' => 'WinXP',
            'isMobileDevice' => false,
        ],
    ],
    'issue-36' => [
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1712.4 Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '33.0',
            'Platform' => 'Win7',
            'isMobileDevice' => false,
        ],
    ],
    'issue-33' => [
        'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10.5; en-US; rv:1.9.2.23) Gecko/20110920 Firefox/3.6.23',
        [
            'Browser' => 'Firefox',
            'Version' => '3.6',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.5',
            'isMobileDevice' => false,
        ],
    ],
    'issue-32' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; MASMJS; rv:11.0) like Gecko',
        [
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-29' => [
        'Mozilla/5.0 (Mobile; rv:18.0) Gecko/18.0 Firefox/18.0',
        [
            'Browser' => 'Firefox',
            'Version' => '18.0',
            'Platform' => 'FirefoxOS',
            'isMobileDevice' => true,
        ],
    ],
    'issue-26' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.101 Safari/537.36',
        [
            'Browser' => 'Chrome',
            'Version' => '30.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-25' => [
        'Mozilla/5.0 (Linux; U; Android 4.2.2; ru-ru; HTC_One_X Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
        [
            'Browser' => 'Safari',
            'Version' => '4.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.2',
            'isMobileDevice' => true,
        ],
    ],
    'issue-13' => [
        'Mozilla/5.0 (Android; Tablet; rv:23.0) Gecko/23.0 Firefox/23.0',
        [
            'Browser' => 'Firefox',
            'Version' => '23.0',
            'Platform' => 'Android',
            'isMobileDevice' => false,
        ],
    ],
    'issue-12-a' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko',
        [
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-12-b' => [
        'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0',
        [
            'Browser' => 'Firefox',
            'Version' => '24.0',
            'Platform' => 'Win8.1',
            'isMobileDevice' => false,
        ],
    ],
    'issue-11' => [
        'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36 OPR/16.0.1196.62',
        [
            'Browser' => 'Opera',
            'Version' => '16.0',
            'Platform' => 'Win8',
            'isMobileDevice' => false,
        ],
    ],
    'issue-4' => [
        'Mozilla/5.0 (PLAYSTATION 3; 2.00)',
        [
            'Browser' => 'Sony PS3',
            'Platform' => 'CellOS',
        ],
    ],
];

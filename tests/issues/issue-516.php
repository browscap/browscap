<?php

declare(strict_types=1);

return [
    'issue-516' => [
        'ua' => 'AdsBot-Google-Mobile (+http://www.google.com/mobile/adsbot.html) Mozilla (iPhone; U; CPU iPhone OS 3 0 like Mac OS X) AppleWebKit (KHTML, like Gecko) Mobile Safari',
        'properties' => [
            'Comment' => 'AdsBot Google-Mobile for iOS',
            'Browser' => 'AdsBot Google-Mobile',
            'Browser_Type' => 'Bot/Crawler',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Google Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'Platform' => 'iOS',
            'Platform_Version' => '3.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
            'Alpha' => false,
            'Beta' => false,
            'Frames' => true,
            'IFrames' => true,
            'Tables' => true,
            'Cookies' => true,
            'JavaScript' => true,
            'VBScript' => false,
            'JavaApplets' => true,
            'isSyndicationReader' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => '3',
            'Device_Name' => 'iPhone',
            'Device_Maker' => 'Apple Inc',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'iPhone',
            'Device_Brand_Name' => 'Apple',
            'RenderingEngine_Name' => 'WebKit',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    'issue-516 (standard)' => [
        'ua' => 'AdsBot-Google-Mobile (+http://www.google.com/mobile/adsbot.html) Mozilla (iPhone; U; CPU iPhone OS 3 0 like Mac OS X) AppleWebKit (KHTML, like Gecko) Mobile Safari',
        'properties' => [
            'Comment' => 'AdsBot Google-Mobile for iOS',
            'Browser' => 'AdsBot Google-Mobile',
            'Browser_Maker' => 'Google Inc',
            'Version' => '0.0',
            'Platform' => 'iOS',
            'Device_Type' => 'Mobile Device',
            'Device_Pointing_Method' => 'touchscreen',
        ],
        'lite' => false,
        'standard' => true,
        'full' => false,
    ],
];

<?php

declare(strict_types=1);

return [
    'issue-1227' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_1_1 like Mac OS X; ja-JP) AppleWebKit/537.36 (KHTML, like Gecko)  Version/10.1.1 Mobile/14B100 Safari/537.36 Puffin/5.1.3IP',
        'properties' => [
            'Comment' => 'Puffin 5.1',
            'Browser' => 'Puffin',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'CloudMosa Inc.',
            'Browser_Modus' => 'unknown',
            'Version' => '5.1',
            'Platform' => 'iOS',
            'Platform_Version' => '10.1',
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
    'issue-1227 (standard)' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_1_1 like Mac OS X; ja-JP) AppleWebKit/537.36 (KHTML, like Gecko)  Version/10.1.1 Mobile/14B100 Safari/537.36 Puffin/5.1.3IP',
        'properties' => [
            'Comment' => 'Puffin 5.1',
            'Browser' => 'Puffin',
            'Browser_Maker' => 'CloudMosa Inc.',
            'Version' => '5.1',
            'Platform' => 'iOS',
            'Device_Type' => 'Mobile Device',
            'Device_Pointing_Method' => 'touchscreen',
        ],
        'lite' => false,
        'standard' => true,
        'full' => false,
    ],
];

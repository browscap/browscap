<?php

declare(strict_types=1);

return [
    'issue-430' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.2.2; Lenovo B8000-H Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.94 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome 28.0',
            'Browser' => 'Chrome',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Google Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '28.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.2',
            'Platform_Description' => 'Android OS',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Google Inc',
            'Alpha' => false,
            'Beta' => false,
            'Frames' => true,
            'IFrames' => true,
            'Tables' => true,
            'Cookies' => true,
            'JavaScript' => true,
            'VBScript' => false,
            'JavaApplets' => false,
            'isSyndicationReader' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => '3',
            'Device_Name' => 'Yoga Tablet 10 3G',
            'Device_Maker' => 'Lenovo',
            'Device_Type' => 'Tablet',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'B8000-H',
            'Device_Brand_Name' => 'Lenovo',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    'issue-430 (standard + lite)' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.2.2; Lenovo B8000-H Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.94 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Maker' => 'Google Inc',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Device_Type' => 'Tablet',
            'Device_Pointing_Method' => 'touchscreen',
        ],
        'lite' => true,
        'standard' => true,
        'full' => false,
    ],
];

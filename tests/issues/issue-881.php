<?php

declare(strict_types=1);

return [
    'issue-881-A' => [
        'ua' => 'Mozilla/5.0 (Windows Phone 8.1; ARM; Trident/7.0; Touch; rv:11; IEMobile/11.0) like Android 4.1.2; compatible) like iPhone OS 7_0_3 Mac OS X WebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.99 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'IEMobile 11.0',
            'Browser' => 'IEMobile',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Microsoft Corporation',
            'Browser_Modus' => 'unknown',
            'Version' => '11.0',
            'Platform' => 'WinPhone8.1',
            'Platform_Version' => '8.1',
            'Platform_Description' => 'Windows Phone OS 8.1',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Microsoft Corporation',
            'Alpha' => false,
            'Beta' => false,
            'Frames' => true,
            'IFrames' => true,
            'Tables' => true,
            'Cookies' => true,
            'JavaScript' => true,
            'VBScript' => true,
            'JavaApplets' => true,
            'isSyndicationReader' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => '3',
            'Device_Name' => 'general Mobile Phone',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'general Mobile Phone',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'Trident',
            'RenderingEngine_Version' => '7.0',
            'RenderingEngine_Maker' => 'Microsoft Corporation',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    'issue-881-B' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.4; Galaxy Nexus Build/JRN84D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.111.166 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome 42.0',
            'Browser' => 'Chrome',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Google Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '42.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
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
            'Device_Name' => 'Galaxy Nexus',
            'Device_Maker' => 'Samsung',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'Galaxy Nexus',
            'Device_Brand_Name' => 'Samsung',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    'issue-881-B (standard + lite)' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.4; Galaxy Nexus Build/JRN84D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.111.166 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Maker' => 'Google Inc',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
        ],
        'lite' => true,
        'standard' => true,
        'full' => false,
    ],
    'issue-881-C' => [
        'ua' => 'Mozilla/5.0 (Android Mobile; rv:40.0) Gecko/40.0 Firefox/40.0',
        'properties' => [
            'Comment' => 'Firefox 40.0',
            'Browser' => 'Firefox',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Mozilla Foundation',
            'Browser_Modus' => 'unknown',
            'Version' => '40.0',
            'Platform' => 'Android',
            'Platform_Version' => 'unknown',
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
            'JavaApplets' => true,
            'isSyndicationReader' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => '3',
            'Device_Name' => 'general Mobile Phone',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'general Mobile Phone',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => '40.0',
            'RenderingEngine_Maker' => 'Mozilla Foundation',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    'issue-881-D' => [
        'ua' => 'Mozilla/5.0 (Android 4.4.4; Mobile; rv:40.0) Gecko/40.0 Firefox/40.0',
        'properties' => [
            'Comment' => 'Firefox 40.0',
            'Browser' => 'Firefox',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Mozilla Foundation',
            'Browser_Modus' => 'unknown',
            'Version' => '40.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
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
            'JavaApplets' => true,
            'isSyndicationReader' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => '3',
            'Device_Name' => 'general Mobile Phone',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'general Mobile Phone',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => '40.0',
            'RenderingEngine_Maker' => 'Mozilla Foundation',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
];

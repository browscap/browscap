<?php

declare(strict_types=1);

return [
    'issue-301' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.2; Trident/4.0; Media Center PC 4.0; SLCC1; .NET CLR 3.0.04320)',
        'properties' => [
            'Comment' => 'IE 8.0',
            'Browser' => 'IE',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Microsoft Corporation',
            'Browser_Modus' => 'unknown',
            'Version' => '8.0',
            'Platform' => 'WinXP',
            'Platform_Version' => '5.2',
            'Platform_Description' => 'Windows XP',
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
            'CssVersion' => '2',
            'Device_Name' => 'Windows Desktop',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Desktop',
            'Device_Pointing_Method' => 'mouse',
            'Device_Code_Name' => 'Windows Desktop',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'Trident',
            'RenderingEngine_Version' => '4.0',
            'RenderingEngine_Maker' => 'Microsoft Corporation',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-301 (lite)' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.2; Trident/4.0; Media Center PC 4.0; SLCC1; .NET CLR 3.0.04320)',
        'properties' => [
            'Comment' => 'IE 8.0',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
            'Device_Type' => 'Desktop',
        ],
        'lite' => true,
        'standard' => false,
        'full' => false,
    ],
];

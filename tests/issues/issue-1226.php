<?php

declare(strict_types=1);

return [
    'issue-1226' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:51.0) Gecko/20100101 Firefox/51.0',
        'properties' => [
            'Comment' => 'Firefox 51.0',
            'Browser' => 'Firefox',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Mozilla Foundation',
            'Browser_Modus' => 'unknown',
            'Version' => '51.0',
            'Platform' => 'macOS',
            'Platform_Version' => '10.12',
            'Platform_Description' => 'macOS',
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
            'Device_Name' => 'Macintosh',
            'Device_Maker' => 'Apple Inc',
            'Device_Type' => 'Desktop',
            'Device_Pointing_Method' => 'mouse',
            'Device_Code_Name' => 'Macintosh',
            'Device_Brand_Name' => 'Apple',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => '51.0',
            'RenderingEngine_Maker' => 'Mozilla Foundation',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    'issue-1226 (lite)' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:51.0) Gecko/20100101 Firefox/51.0',
        'properties' => [
            'Comment' => 'Firefox 51.0',
            'Browser' => 'Firefox',
            'Version' => '51.0',
            'Platform' => 'MacOSX',
            'Device_Type' => 'Desktop',
        ],
        'lite' => true,
        'standard' => false,
        'full' => false,
    ],
];

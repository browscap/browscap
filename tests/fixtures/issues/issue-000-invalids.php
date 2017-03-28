<?php
/**
 * This fixture file contains useragents that should be considered "invalid" and thus parse to the Default Browser.
 * These useragents are maintained here in order to prevent new patterns from accidentally picking up these agents.
 *
 * Each entry in this file should contain a PHP comment describing why the agent is invalid for later reference
 * and possibly a source where possible.
 */

$invalidUserAgents = [
    // There was no 3.x release of the Firefox for Android browser. Versioning jumped from 2.x to 4.x, as
    // described here: http://starkravingfinkle.org/blog/2010/09/fennec-4-0-new-and-notable/
    // and here: https://en.wikipedia.org/wiki/Firefox_for_Android#History
    'issue-000-invalids-A' => 'Mozilla/5.0 (Android; U; Android; pl; rv:1.9.2.8) Gecko/20100202 Firefox/3.5.8',
    // For these three strings, there is no "MSIE 6.1".  Internet Explorer 6 had many updates done to it as
    // service pack releases, but this did not alter the version major/minor version, nor the useragent.
    // Versions released: https://en.wikipedia.org/wiki/Internet_Explorer_6#Release_history
    'issue-000-invalids-B' => 'Mozilla/4.0 (compatible; MSIE 6.1; Windows XP)',
    'issue-000-invalids-C' => 'Mozilla/4.0 (compatible; MSIE 6.1; Windows XP; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
    'issue-000-invalids-D' => 'Mozilla/4.0 (compatible; MSIE 6.1; Windows NT)',
    // IE 10 does not run on OSX, modern.ie or VirtualBox based solutions still show as a Windows platform
    'issue-000-invalids-E' => 'Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)',
];

$testCases = [];

foreach ($invalidUserAgents as $issue => $ua) {
    $testCases[$issue] = [
        'ua' => $ua,
        'properties' => [
            'Comment' => 'Default Browser',
            'Browser' => 'Default Browser',
            'Browser_Type' => 'unknown',
            'Browser_Bits' => '0',
            'Browser_Maker' => 'unknown',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'MajorVer' => '0',
            'MinorVer' => '0',
            'Platform' => 'unknown',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'unknown',
            'Platform_Bits' => '0',
            'Platform_Maker' => 'unknown',
            'Alpha' => false,
            'Beta' => false,
            'Win16' => false,
            'Win32' => false,
            'Win64' => false,
            'Frames' => false,
            'IFrames' => false,
            'Tables' => false,
            'Cookies' => false,
            'BackgroundSounds' => false,
            'JavaScript' => false,
            'VBScript' => false,
            'JavaApplets' => false,
            'ActiveXControls' => false,
            'isMobileDevice' => false,
            'isTablet' => false,
            'isSyndicationReader' => false,
            'Crawler' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => '0',
            'AolVersion' => '0',
            'Device_Name' => 'unknown',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'unknown',
            'Device_Pointing_Method' => 'unknown',
            'Device_Code_Name' => 'unknown',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'unknown',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'unknown',
        ],
        'lite' => true,
        'standard' => true,
    ];
}

return $testCases;

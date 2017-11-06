<?php
/**
 * This fixture file contains useragents that should be considered "invalid" and thus parse to the Default Browser.
 * These useragents are maintained here in order to prevent new patterns from accidentally picking up these agents.
 *
 * Each entry in this file should contain a PHP comment describing why the agent is invalid for later reference
 * and possibly a source where possible.
 */

$invalidUserAgents = [
    // There was no 3.x release of the Firefox for Android browser. Versioning jumped from 1.x to 4.x, as
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
    // IE 10 should only be Trident 6+
    'issue-000-invalids-F' => 'Mozilla/5.0 (MSIE 10.0; Windows NT 6.1; Trident/5.0)',
    // IE 11 should only be Trident 7+
    'issue-000-invalids-G' => 'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.0 Win64; x64; Trident/6.0)',
    // There is no Windows NT 9.0: https://en.wikipedia.org/wiki/List_of_Microsoft_Windows_versions#Client_versions
    'issue-000-invalids-H' => 'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)',
    // IE 5.0 should be Mozilla/4.0 not Mozilla/5.0
    'issue-000-invalids-I' => 'Mozilla/5.0 (compatible; MSIE 5.0; Windows 98; Trident/5.1)',
    // There was no IE 4.5 on Windows (there was a Mac 4.5), only 4.0 and 4.01
    // https://en.wikipedia.org/wiki/Internet_Explorer_4#Versions
    // https://en.wikipedia.org/wiki/Internet_Explorer_for_Mac#Internet_Explorer_4.0_for_Macintosh
    'issue-000-invalids-J' => 'Mozilla/4.0 (compatible; MSIE 4.5; Windows NT 5.2; .NET CLR 3.5.30729)',
    // There is no IE 9.11 (Like IE 6 mentioned above, MS typically doesn't bump the minor versions of their browsers)
    'issue-000-invalids-K' => 'Mozilla/5.0 (compatible; MSIE 9.11; Windows NT 6.2; Trident/5.0)',
    // Seamonkey is Firefox/Gecko based, not Safari/WebKit
    'issue-000-invalids-L' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Safari/537.36 Firefox/42.0 SeaMonkey/2.39a1',
    // Firefox does not use the "Mozilla/6.0" prefix
    'issue-000-invalids-M' => 'Mozilla/6.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1',
    // In addition to the prefix being wrong (like above), the Gecko version and Firefox version don't match
    'issue-000-invalids-N' => 'Mozilla/6.0 (Macintosh; I; Intel Mac OS X 11_7_9; de-LI; rv:1.9b4) Gecko/2012010317 Firefox/10.0a4',
    // This is probably supposed to be a Firefox useragent, but is lacking the "Firefox" identifier, which is invalid
    'issue-000-invalids-O' => 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:29.0) Gecko/20100101 /',
    // iCab is only available for Macintosh platforms (and iOS), From the developer's FAQ (http://www.icab.de/faq.html#q12):
    // Will there be a version of iCab for Windows or Linux?
    //      No, there's no version for Windows or Linux planned
    'issue-000-invalids-P' => 'iCab/4.0  (Windows; U; Windows NT 6.0; en-gb)',
    // There is no AppleWebKit in this UA, which all Chrome UAs should have
    'issue-000-invalids-Q' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de-DE) Chrome/4.0.223.3 Safari/532.2',
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
            'Platform' => 'unknown',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'unknown',
            'Platform_Bits' => '0',
            'Platform_Maker' => 'unknown',
            'Alpha' => false,
            'Beta' => false,
            'isMobileDevice' => false,
            'isTablet' => false,
            'isSyndicationReader' => false,
            'Crawler' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
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
        'full' => true,
    ];
}

return $testCases;

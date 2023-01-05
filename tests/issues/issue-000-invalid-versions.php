<?php

declare(strict_types=1);

return [
    // Mozilla removed support for Windows 98 in Firefox 3.0 (2.0.x is the last version that supported Windows 98)
    // https://bugzilla.mozilla.org/show_bug.cgi?id=330276
    'issue-000-invalid-versions-A' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Win98; bg; rv:1.9.0.19) Gecko/20090624 Firefox/3.1b3',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win98',
            'Platform_Version' => '98',
            'Platform_Description' => 'Windows 98',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above (no Windows 98 support past Firefox 2.0.x)
    'issue-000-invalid-versions-B' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Win98; ca; rv:1.7.13) Gecko/20090715 Firefox/3.5.1',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win98',
            'Platform_Version' => '98',
            'Platform_Description' => 'Windows 98',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // NT4.x support was removed at the same time that Windows 98/ME support was removed
    'issue-000-invalid-versions-C' => [
        'ua' => 'Mozilla/5.0 (Windows; U; WinNT4.0; fr; rv:2.0b3pre) Gecko/20100722 Firefox/3.6.8',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-D' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; rv:11.0) Gecko/20100101 Firefox/11.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-E' => [
        'ua' => 'Mozilla/5.0 (Windows; U; WinNT4.0; bg; rv:1.8.1.4) Gecko/20100401 Firefox/4.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-F' => [
        'ua' => 'Mozilla/5.0 (Windows; U; WinNT4.0; fa; rv:1.8.0.5) Gecko/20090407 Firefox/3.1b3',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-G' => [
        'ua' => 'Mozilla/5.0 (Windows; U; WinNT4.0; bg; rv:1.8.1.17pre) Gecko/20100526 Firefox/3.7a5pre',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-H' => [
        'ua' => 'Mozilla/5.0 (Windows; U; WinNT4.0; ko; rv:1.9.1.4) Gecko/20090716 Firefox/3.5.1',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-I' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; rv:25.0) Gecko/20100101 Firefox/25.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-J' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.10; rv:19.0) Gecko/20100101 Firefox/19.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.1',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-K' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; rv:43.0) Gecko/20100101 Firefox/43.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-L' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; rv:40.0) Gecko/20100101 Firefox/40.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-M' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.10; rv:31.0) Gecko/20100101 Firefox/31.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.1',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-N' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; rv:50.0) Gecko/20100101 Firefox/50.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-O' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.10; rv:49.0) Gecko/20100101 Firefox/49.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.1',
            'Platform_Description' => 'Windows NT',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Mozilla dropped Windows 2000 support as of Firefox 13:
    // https://blog.mozilla.org/futurereleases/2012/03/23/upcoming-firefox-support-changes/
    // https://support.mozilla.org/t5/Documents-Archive/Firefox-no-longer-works-with-Windows-2000/ta-p/11956
    'issue-000-invalid-versions-P' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0; rv:21.0) Gecko/20100101 Firefox/21.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win2000',
            'Platform_Version' => '5.0',
            'Platform_Description' => 'Windows 2000',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-Q' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0; rv:35.0) Gecko/20100101 Firefox/35.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win2000',
            'Platform_Version' => '5.0',
            'Platform_Description' => 'Windows 2000',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-R' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win2000',
            'Platform_Version' => '5.0',
            'Platform_Description' => 'Windows 2000',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-S' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0; rv:49.0) Gecko/20100101 Firefox/49.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win2000',
            'Platform_Version' => '5.0',
            'Platform_Description' => 'Windows 2000',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-T' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win2000',
            'Platform_Version' => '5.0',
            'Platform_Description' => 'Windows 2000',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Firefox 4.0 and up don't suppport OSX 10.4 (https://www.mozilla.org/en-US/firefox/4.0/system-requirements/)
    // Firefox 3.6 is last to support 10.4 (https://support.mozilla.org/t5/Documents-Archive/Firefox-no-longer-works-with-Mac-OS-X-10-4-or-PowerPC-processors/ta-p/12222)
    'issue-000-invalid-versions-U' => [
        'ua' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10.4; ru; rv:1.9.2a1pre) Gecko/20100401 Firefox/4.0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.4',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-V' => [
        'ua' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10.4; en-US; rv:48.0) Gecko/20110420 Firefox/48.0',
        'properties' => [
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.4',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // OSX 10.5 was dropped in Firefox 17:
    // https://support.mozilla.org/t5/Documents-Archive/Firefox-no-longer-works-with-Mac-OS-X-10-5/ta-p/26546
    'issue-000-invalid-versions-W' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.5; rv:48.0) Gecko/20100101 Firefox/48.0',
        'properties' => [
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.5',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // OSX 10.6, 10.7 and 10.8 were dropped in Firefox 49
    // https://support.mozilla.org/t5/Install-and-Update/Firefox-support-has-ended-for-Mac-OS-X-10-6-10-7-and-10-8/ta-p/32725
    'issue-000-invalid-versions-X' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:45.0) Gecko/20100101 Firefox/49.0',
        'properties' => [
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.6',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-Y' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:45.0) Gecko/20100101 Firefox/49.0',
        'properties' => [
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.7',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-Z' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:45.0) Gecko/20100101 Firefox/49.0',
        'properties' => [
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.8',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Invalid Firefox version, see #1381 for more discussion about this.
    'issue-000-invalid-versions-AA' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:41.0) Gecko/20100101 Firefox/AA5D',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'Windows 10',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AB' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:41.0) Gecko/20100101 Firefox/77AB',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'Windows 10',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AC' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:41.0) Gecko/20100101 Firefox/4DF0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'Windows 10',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AD' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:41.0) Gecko/20100101 Firefox/3FB0',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'Windows 10',
            'Platform_Bits' => '64',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Firefox wasn't called Firefox until version 0.8 (was known as Phoenix or Firebird previously)
    // https://en.wikipedia.org/wiki/Firefox_version_history#Release_history
    // This should have "Firebird" in place of Firefox
    'issue-000-invalid-versions-AE' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Win 9x 4.90; en-US; rv:1.7.5) Gecko/20050101 Firefox/0.6.4',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'WinME',
            'Platform_Version' => 'ME',
            'Platform_Description' => 'Windows ME',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above, but should be "Phoenix" instead of Firefox
    'issue-000-invalid-versions-AF' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Win98; en-US; rv:1.7) Gecko/20041122 Firefox/0.5.6+',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win98',
            'Platform_Version' => '98',
            'Platform_Description' => 'Windows 98',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // There was no Firefox 3.8 release (alpha or otherwise). There was a 3.7 alpha release, but the version was
    // changed to 4.0 with the first beta release.
    // https://en.wikipedia.org/wiki/History_of_Firefox#Version_4.0
    'issue-000-invalid-versions-AG' => [
        'ua' => 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) Gecko/2008092313 Ubuntu/9.25 (jaunty) Firefox/3.8',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Linux',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Linux',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Chrome was never released for anything less than Windows XP officially
    // https://en.wikipedia.org/wiki/Google_Chrome#Public_release
    'issue-000-invalid-versions-AH' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AI' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Google Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AJ' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AK' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.9 Safari/533.4',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Windows XP and Vista were dropped in Chrome 50
    // https://arstechnica.com/gadgets/2016/04/chrome-50-ends-support-for-windows-xp-os-x-10-6-other-old-versions/
    'issue-000-invalid-versions-AL' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/536.11',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'WinXP',
            'Platform_Version' => '5.1',
            'Platform_Description' => 'Windows XP',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AM' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.75 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'WinVista',
            'Platform_Version' => '6.0',
            'Platform_Description' => 'Windows Vista',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AN' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'WinXP',
            'Platform_Version' => '5.2',
            'Platform_Description' => 'Windows XP',
            'Platform_Bits' => '64',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AO' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'WinXP',
            'Platform_Version' => '5.2',
            'Platform_Description' => 'Windows XP',
            'Platform_Bits' => '32',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AP' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'WinVista',
            'Platform_Version' => '6.0',
            'Platform_Description' => 'Windows Vista',
            'Platform_Bits' => '64',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AQ' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2778.54 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'WinXP',
            'Platform_Version' => '5.1',
            'Platform_Description' => 'Windows XP',
            'Platform_Bits' => '64',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // OSX 10.6, 10.7 and 10.8 were also dropped in Chrome 50
    // https://arstechnica.com/gadgets/2016/04/chrome-50-ends-support-for-windows-xp-os-x-10-6-other-old-versions/
    'issue-000-invalid-versions-AR' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.6',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false, // will detect "Chrome 55.0 on Mac OS X 10.0"
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AS' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.8',
            'Platform_Description' => 'Mac OS X',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false, // will detect "Chrome 55.0 on Mac OS X 10.0"
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AT' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.7',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false, // will detect "Chrome 55.0 on Mac OS X 10.0"
        'standard' => false,
        'full' => true,
    ],
    // OSX 10.5 was dropped in Chrome 22
    // https://www.engadget.com/2012/07/10/google-drops-os-x-leopard-support-for-chrome-22-dev-release/
    'issue-000-invalid-versions-AU' => [
        'ua' => 'Mozilla/5.0 (Macintosh Intel Mac OS X 10_5_8) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/22.0.1183.0 Safari/537.1',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.5',
            'Platform_Description' => 'Mac OS X',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Opera 6.03 was the last release for Mac OS 8/9
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Release_compatibility
    'issue-000-invalid-versions-AV' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC) Opera 7.51  [en]',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacPPC',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AW' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC; en) Opera 9.24',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacPPC',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AX' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC) Opera 7.54  [en]',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacPPC',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Opera 10.63 was the last version to support Windows 9.x/NT 4
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Version_10
    'issue-000-invalid-versions-AY' => [
        'ua' => 'Mozilla/4.0 (Windows 98; US) Opera 12.16 [en]',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Win98',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-AZ' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36 OPR/15.0.1147.153',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BA' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36 OPR/32.0.1948.25',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BB' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.10) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36 OPR/32.0.1948.69',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.1',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BC' => [
        'ua' => 'Opera/9.80 (Windows 95 U Edition Yx ru) Presto/2.10.289 Version/12.02',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Opera Software ASA',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'Platform' => 'Win95',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BD' => [
        'ua' => 'Opera/9.80 (Windows ME; U; en) Presto/2.10.289 Version/12.02',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinME',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // There is no 9.80/9.8 release of Opera. The "9.80" version number is used as a prefix to the agent in later
    // versions, but there was no 9.80 (9.64 was the last 9.x release)
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Version_9
    // http://arc.opera.com/pub/opera/win/
    'issue-000-invalid-versions-BE' => [
        'ua' => 'Mozilla/5.0 (compatible; Opera/9.80 Presto/2.8.131 Version/9.80; Windows NT 6.1; Trident/6.0)',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Win7',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BF' => [
        'ua' => 'Mozilla/5.0 (compatible; Opera/9.80 Presto/2.8.131 Version/9.80; Windows NT 6.3; Trident/6.0)',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Win8.1',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // The first Opera release for Mac was 5.0
    // https://web.archive.org/web/20010204004000/http://www.opera.com:80/pressreleases/20010201.html
    // http://arc.opera.com/pub/opera/mac/
    'issue-000-invalid-versions-BG' => [
        'ua' => 'Opera/4.02 (Macintosh; U; bg)',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Browser_Type' => 'Browser',
            'Version' => '0.0',
            'Device_Name' => 'Macintosh',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // There was no 12.50 release of Opera (12.18 was the last release (on Windows, 12.16 on Linux))
    // http://arc.opera.com/pub/opera/linux/
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Version_12
    'issue-000-invalid-versions-BH' => [
        'ua' => 'Opera/9.80 (Linux i686) Presto/2.12.407 Version/12.50',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Linux',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // There was no 9.99 version of Opera
    'issue-000-invalid-versions-BI' => [
        'ua' => 'Opera/9.99 (X11; U; sk)',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Linux',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // There was no 7.60 version of Opera released, the last version in the 7.x series was 7.54
    // http://arc.opera.com/pub/opera/linux/
    // http://arc.opera.com/pub/opera/win/
    // Possibly 7.55 based on the timeline here:
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Timeline_of_releases
    // There was a 7.6 technology preview, but this was never released
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Version_7
    'issue-000-invalid-versions-BJ' => [
        'ua' => 'Opera 7.60 (Linux 2.4.10-4GB i686; U)',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Linux',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BK' => [
        'ua' => 'Opera 7.60 (Windows NT 4.0; U) [en] via HTTP/1.0 l33t0-HaX0r.hiddenip.com/',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinNT',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BL' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 7.60',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinXP',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BM' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; en) Opera 7.60',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Win98',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // There was no 10.70 release of Opera, 10.63 was the last 10.x release
    // http://arc.opera.com/pub/opera/mac/
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Timeline_of_releases
    'issue-000-invalid-versions-BN' => [
        'ua' => 'Opera/9.80 (Macintosh; Intel Mac OS X 10_6_5; U; nv) Presto/2.6.34 Version/10.70',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.6',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BO' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.2; U; ru; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.70',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinXP',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BP' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.1; U; zh-cn; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.70',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinXP',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // There was no 9.70 release of Opera, 9.64 was the last 9.x release
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Timeline_of_releases
    // http://arc.opera.com/pub/opera/win/
    // http://arc.opera.com/pub/opera/linux/
    'issue-000-invalid-versions-BQ' => [
        'ua' => 'Mozilla/5.0 (Linux i686 ; U; en; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 9.70',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Linux',
            'Platform_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BR' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Linux i686 ; en) Opera 9.70',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Linux',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Windows 2000 support was dropped after Opera version 12.02
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Release_compatibility
    'issue-000-invalid-versions-BS' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 5.0) Opera 12.12',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'Win2000',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // The last version of Opera for OSX 10.6 was 25.0
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Release_compatibility
    'issue-000-invalid-versions-BT' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36 OPR/33.0.1990.58',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.6',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // The last version of Opera for OSX 10.7/10.8 was 36.0
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Release_compatibility
    'issue-000-invalid-versions-BU' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36 OPR/37.0.2178.54',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.7',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BV' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36 OPR/37.0.2178.54',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.8',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // The last version of Opera to support Windows Vista was 36.0
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Release_compatibility
    'issue-000-invalid-versions-BW' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36 OPR/37.0.2178.54',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinVista',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BX' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36 OPR/37.0.2178.54',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'WinVista',
            'Platform_Bits' => '64',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Opera dropped FreeBSD support after version 12.16
    // https://en.wikipedia.org/wiki/History_of_the_Opera_web_browser#Release_compatibility
    'issue-000-invalid-versions-BY' => [
        'ua' => 'Mozilla/5.0 (FreeBSD amd64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36 OPR/25.0.1614.68',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'FreeBSD',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-BZ' => [
        'ua' => 'Mozilla/5.0 (FreeBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.94 Safari/537.36 OPR/27.0.1689.66',
        'properties' => [
            'Comment' => 'Opera Generic',
            'Browser' => 'Opera',
            'Version' => '0.0',
            'Platform' => 'FreeBSD',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Iron is based on Chromium, which, as we know, didn't support anything less than Windows XP at launch, apparently
    // Iron is no different. The first version that archive.org has a snapshot for (0.4) shows "XP,Vista" as the supported
    // Windows versions: https://web.archive.org/web/20081202170334/http://www.srware.net/en/software_srware_iron_download.php
    'issue-000-invalid-versions-CA' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1150.1 Iron/20.0.1150.1 Safari/536.11',
        'properties' => [
            'Comment' => 'Iron Generic',
            'Browser' => 'Iron',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CB' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0) AppleWebKit/537.36 (KHTML, like Gecko) Iron/30.0.1650.0  Chrome/30.0.1650.0 Safari/537.36',
        'properties' => [
            'Comment' => 'Iron Generic',
            'Browser' => 'Iron',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CC' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US) AppleWebKit/532.0 (KHTML, like Gecko) Iron/3.0.197.0 Safari/532.0',
        'properties' => [
            'Comment' => 'Iron Generic',
            'Browser' => 'Iron',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CD' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Iron/5.0.381.0 Chrome/5.0.381 Safari/533.4',
        'properties' => [
            'Comment' => 'Iron Generic',
            'Browser' => 'Iron',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CE' => [
        'ua' => 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US) AppleWebKit/534.7 (KHTML, like Gecko) Iron/7.0.520.1 Chrome/7.0.520.1 Safari/534.7',
        'properties' => [
            'Comment' => 'Iron Generic',
            'Browser' => 'Iron',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CF' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.0 WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Iron/26.0.1450.0 Chrome/26.0.1450.0 Safari/537.36',
        'properties' => [
            'Comment' => 'Iron Generic',
            'Browser' => 'Iron',
            'Version' => '0.0',
            'Platform' => 'Win32',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'Windows',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // At version 2.0, Qupzilla switched to using QT WebEngine instead of QT WebKit: https://github.com/QupZilla/qupzilla/releases/tag/v2.0.0
    // Which apparently doesn't support Windows XP: https://github.com/QupZilla/qupzilla/issues/2220
    'issue-000-invalid-versions-CG' => [
        'ua' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) QupZilla/2.0.1 Chrome/45.0.2454.101 Safari/537.36',
        'properties' => [
            'Comment' => 'QupZilla Generic',
            'Browser' => 'QupZilla',
            'Version' => '0.0',
            'Platform' => 'WinXP',
            'Platform_Version' => '5.1',
            'Platform_Description' => 'Windows XP',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // There was no Chrome 19 - 24 for Android. Chrome was released at version 18, and was later bumped to 25 to sync
    // up with the desktop version number.
    // https://en.wikipedia.org/wiki/Google_Chrome_version_history
    // https://thenextweb.com/google/2013/01/10/google-launches-chrome-beta-channel-for-android-4-0-phones-and-tablets-releases-version-25/#.tnw_34r65q5P
    // The 19.77.34.5 version may be "Chrome Content Shell", as detected by Whichbrowser:
    // https://github.com/WhichBrowser/Parser/blob/fb23f0b64794bce9a3b0542e2fd3e5a4be74fb2b/src/Analyser/Header/Useragent/Browser.php#L308
    'issue-000-invalid-versions-CH' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CI' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 5.0; SM-N900 Build/LRX21V) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '5.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CJ' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.0.1; GT-P5100 Build/ITL41F) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/22.0.677.257 Mobile Safari/535.19',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CK' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.2; SM-P600 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CL' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.4; Nexus 5 Build/KTU84Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CM' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.4; HTC One_M8 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CN' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.2; SCH-I545 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CO' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/19.77.34.5 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.4',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Chrome 18 was the first stable release for android: https://www.engadget.com/2012/06/27/google-chrome-for-android/
    // Chrome 16 was a beta: https://chromereleases.googleblog.com/2012/02/chrome-for-android-beta-1.html
    // nothing else previous to that
    'issue-000-invalid-versions-CP' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 5.1.1; Lenovo S920_ROW Build/LMY47V) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/14.0.1019.81 Mobile Safari/535.19',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '5.1',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above, there's no evidence that there was a version 17 Beta
    'issue-000-invalid-versions-CQ' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.3; GT-I9100 Build/JSS15J) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/17.0.1461.62 Mobile Safari/535.19',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.3',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CR' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.1.2; GT-N8010 Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.55 Safari/534.30',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.1',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CS' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.0.4; MTC 970H Build/JRO03C) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/13.0.1055.233 Safari/535.19',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Chrome 5 was the first Linux (and Mac) release of Chrome: https://en.wikipedia.org/wiki/Google_Chrome_version_history
    // https://chrome.googleblog.com/2010/05/new-chrome-stable-release-welcome-mac.html
    // Except for a Beta release of 4: https://chromereleases.googleblog.com/2009/12/beta-update-linux-mac-and-windows.html
    'issue-000-invalid-versions-CT' => [
        'ua' => 'Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/532.0 (KHTML, like Gecko) Chrome/3.0.195.1 Safari/532.0',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Version' => '0.0',
            'Platform' => 'Linux',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Chrome 37 was the first Native 64-bit (Win64; x64) version of the browser on Windows:
    // https://blog.chromium.org/2014/08/64-bits-of-awesome-64-bit-windows_26.html
    'issue-000-invalid-versions-CU' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/534.34 (KHTML, like Gecko) Chrome/11.0.696.7 Safari/534.34',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Bits' => '64',
            'Version' => '0.0',
            'Platform' => 'Win8',
            'Platform_Version' => '6.2',
            'Platform_Description' => 'Windows 8',
            'Platform_Bits' => '64',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CV' => [
        'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.75 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Bits' => '64',
            'Version' => '0.0',
            'Platform' => 'Win10',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'Windows 10',
            'Platform_Bits' => '64',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CW' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/535.36 (KHTML, like Gecko) Chrome/31.0.1004.71 Safari/535.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Bits' => '64',
            'Version' => '0.0',
            'Platform' => 'Win7',
            'Platform_Version' => '6.1',
            'Platform_Description' => 'Windows 7',
            'Platform_Bits' => '64',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CX' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Bits' => '64',
            'Version' => '0.0',
            'Platform' => 'Win8.1',
            'Platform_Version' => '6.3',
            'Platform_Description' => 'Windows 8.1',
            'Platform_Bits' => '64',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // Same as above
    'issue-000-invalid-versions-CY' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.0; Win64; x64) AppleWebKit/535.45 (KHTML, like Gecko) Chrome/17.0.1700.48 Safari/535.45',
        'properties' => [
            'Comment' => 'Chrome Generic',
            'Browser' => 'Chrome',
            'Browser_Bits' => '64',
            'Version' => '0.0',
            'Platform' => 'WinVista',
            'Platform_Version' => '6.0',
            'Platform_Description' => 'Windows Vista',
            'Platform_Bits' => '64',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Windows versions less than XP were dropped as of version 13.0:
    // http://website-archive.mozilla.org/www.mozilla.org/thunderbird_releasenotes/en-US/thunderbird/13.0/system-requirements/
    // http://website-archive.mozilla.org/www.mozilla.org/thunderbird_releasenotes/en-US/thunderbird/13.0/releasenotes/
    'issue-000-invalid-versions-CZ' => [
        'ua' => 'Mozilla/5.0 (Windows NT 4.0; rv:17.0) Gecko/20130107 Thunderbird/17.0.2',
        'properties' => [
            'Comment' => 'Thunderbird Generic',
            'Browser' => 'Thunderbird',
            'Browser_Bits' => '32',
            'Version' => '0.0',
            'Platform' => 'WinNT',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Windows NT',
            'Platform_Bits' => '32',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 6.0 was most likely not available on iOS 7.1.2 (it would have come with Safari 7.0).
    // I don't have a concrete source for this, but there are a couple of things wrong with this UA
    // 1) The AppleWebKit version "537.51.2" matches what is seen in Safari 7.0 UAs, not 6.0.
    // 2) The Safari version for 6.0 is "8536.25" not "537.51.2"
    // That said, this one does return Safari 6.0 on iOS, but it says it's iOS 7.0, not 7.1.
    // 7.0 was left in for Safari 6.0 due to Beta releases of iOS containing the previous version's Safari until
    // it's updated before general release.
    'issue-000-invalid-versions-DA' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Mobile/11D257 Version/6.0 Safari/537.51.2',
        'properties' => [
            'Comment' => 'Mobile Safari 6.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '6.0',
            'Platform' => 'iOS',
            'Platform_Version' => '7.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 8.0 would not have been available on iOS 7.1.1 (it would have released with iOS 8.0, and it's not
    // possible to update Safari outside of an iOS update). There are also a couple of things wrong with this UA,
    // which implies that it's probably spoofed.
    // 1) The iOS build "12B435" (in the mobile tag) belongs to iOS version 8.1.1, not 7.1.1:
    // https://ipsw.me/8.1.1
    // http://iosindex.com/iphone/4s/8.1.1-12B435
    // 2) The Safari build number for 8.0 is "600.1.4" not "500.4"
    'issue-000-invalid-versions-DB' => [
        'ua' => 'Mozilla/5.0 (iPad; CPU OS 7_1_1 like Mac OS X) AppleWebKit/500.14 (KHTML, like Gecko) Version/8.0 Mobile/12B435 Safari/500.4',
        'properties' => [
            'Comment' => 'Mobile Safari Generic',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'Platform' => 'iOS',
            'Platform_Version' => '7.1',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // It's not possible to run an older version of Safari on newer versions of iOS. iOS 10.x would come with Safari 10, not 8.0.
    // Also the WebKit version here (602.3.12) applies to older version of Safari (10.0 in this case), 8.0 was at 600.1.3 - 601.1.37
    'issue-000-invalid-versions-DC' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2 like Mac OS X) AppleWebKit/602.3.12 (KHTML, like Gecko) Mobile/14C92 Version/8.0 Safari/600.1.4',
        'properties' => [
            'Comment' => 'Mobile Safari Generic',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'Platform' => 'iOS',
            'Platform_Version' => '10.2',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 8.0 would not have been available on iOS 9.1.1 (Safari 9.0 was in use by this release)
    // Also, the iOS build number here (12B435) is for iOS 8.1.1, not 9.1.1
    // https://ipsw.me/8.1.1
    // http://iosindex.com/iphone/4s/8.1.1-12B435
    // This gets caught as iOS 9.0 since 9.0 exists in the Safari 8.0 file due to beta iOS releases.
    'issue-000-invalid-versions-DD' => [
        'ua' => 'Mozilla/5.0 (iPod touch; CPU iPhone OS 9_1_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B435 Safari/600.1.4',
        'properties' => [
            'Comment' => 'Mobile Safari 8.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '8.0',
            'Platform' => 'iOS',
            'Platform_Version' => '9.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same issue as above, just for iPhone instead of iPod
    'issue-000-invalid-versions-DE' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B435 Safari/600.1.4',
        'properties' => [
            'Comment' => 'Mobile Safari 8.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '8.0',
            'Platform' => 'iOS',
            'Platform_Version' => '9.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 10.0 isn't available on iOS 9.3 (would have released with iOS 10.x, and isn't separately updateable)
    // Also, the iOS build here (14B100) is for iOS 10.1.1, not 9.3.5:
    // https://ipsw.me/10.1.1
    // http://iosindex.com/ios/iphone/7g-plus/10.1.1-14B100
    'issue-000-invalid-versions-DF' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9.3.5 like Mac OS X) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0 Mobile/14B100 Safari/602.1',
        'properties' => [
            'Comment' => 'Mobile Safari 10.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '10.0',
            'Platform' => 'iOS',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 9.0 came out with iOS 9.0 and is not updateable on older iOS versions
    // Also, the iOS build here (13B143) belongs to iOS 9.1, not 8.3
    // https://ipsw.me/9.1
    // http://iosindex.com/ios/iphone/6s/9.1-13B143
    'issue-000-invalid-versions-DG' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4.15.16 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        'properties' => [
            'Comment' => 'Mobile Safari 9.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '9.0',
            'Platform' => 'iOS',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 9.0 was not available on iOS 8.1.1
    // The iOS build number (13B143) is for iOS 9.1 not 8.1.1
    // https://ipsw.me/9.1
    // http://iosindex.com/ios/iphone/6g/9.1-13B143
    'issue-000-invalid-versions-DH' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_1 like Mac OS X) AppleWebKit/600.1.4.12.4 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        'properties' => [
            'Comment' => 'Mobile Safari 9.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '9.0',
            'Platform' => 'iOS',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 9.0 was not available on iOS 7.1.2
    // The iOS build number (13B143) is for iOS 9.1 not 7.1.2
    // https://ipsw.me/9.1
    // http://iosindex.com/ios/iphone/6g/9.1-13B143
    'issue-000-invalid-versions-DI' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        'properties' => [
            'Comment' => 'Mobile Safari 9.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '9.0',
            'Platform' => 'iOS',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 8.0 was not available on iOS 7.0.5
    // The iOS build number (12F70) is for iOS 8.3 not 7.0.5
    // https://ipsw.me/8.3
    // http://iosindex.com/iphone/5s-global/8.3-12F70
    'issue-000-invalid-versions-DJ' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_5 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4',
        'properties' => [
            'Comment' => 'Mobile Safari 8.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '8.0',
            'Platform' => 'iOS',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Safari 9.0 is not available on iOS 10.3 (Safari 10.0 would have come with 10.3)
    // The iOS build number (13B143) is for 9.1 not 10.3
    // https://ipsw.me/9.1
    // http://iosindex.com/ios/iphone/6s/9.1-13B143
    // This parses as iOS 10.0 since 10.0 is left in the Safari 9.0 file for the beta releases
    'issue-000-invalid-versions-DK' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3 like Mac OS X) AppleWebKit/603.1.30.1.33 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        'properties' => [
            'Comment' => 'Mobile Safari 9.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '9.0',
            'Platform' => 'iOS',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Identical to the one above, but instead of iOS 10.3, this one has iOS 10.2 defined in the UA
    'issue-000-invalid-versions-DL' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2 like Mac OS X) AppleWebKit/602.3.12 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        'properties' => [
            'Comment' => 'Mobile Safari 9.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '9.0',
            'Platform' => 'iOS',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // Similar to the ones above, but this one has "13C75" as the build number, which is for iOS 9.2, not 10.1
    // https://ipsw.me/9.2
    // http://iosindex.com/ios/iphone/6g/9.2-13C75
    // Parses as iOS 10.0
    'issue-000-invalid-versions-DM' => [
        'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13C75 Safari/601.1',
        'properties' => [
            'Comment' => 'Mobile Safari 9.0',
            'Browser' => 'Safari',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Apple Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '9.0',
            'Platform' => 'iOS',
            'Platform_Version' => '10.0',
            'Platform_Description' => 'iPod, iPhone & iPad',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Apple Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // This one is here because of the device and android platform combination. The Samsung GT-I9300 (Galaxy S III)
    // released with Android 4.0 and is upgradeable (officially) to Android 4.4. It shouldn't be seen with Android 2.3.
    // https://en.wikipedia.org/wiki/Samsung_Galaxy_S_III
    // This device seems like it could be a chinese GT-I9300 running MocorDroid (custom android ROM):
    // https://forum.xda-developers.com/showthread.php?t=2141663
    'issue-000-invalid-versions-DN' => [
        'ua' => 'Mozilla/5.0 (Linux; U; Android 2.3.6; en-US; GT-I9300 Build/MocorDroid2.2.2) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.2.0.535 U3/0.8.0 Mobile Safari/534.30',
        'properties' => [
            'Comment' => 'UC Browser 10.2 for Android',
            'Browser' => 'UC Browser',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'UCWeb Inc.',
            'Browser_Modus' => 'unknown',
            'Version' => '10.2',
            'Platform' => 'Android',
            'Platform_Version' => '2.3',
            'Platform_Description' => 'Android OS',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Google Inc',
            'Device_Name' => 'general Mobile Phone',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'general Mobile Phone',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'U3',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'UCWeb Inc.',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // This device (Galaxy S III Mini) released with Android 4.1 (and is not officially upgradeable beyond 4.1.2)
    // https://en.wikipedia.org/wiki/Samsung_Galaxy_S_III_Mini
    'issue-000-invalid-versions-DO' => [
        'ua' => 'Mozilla/5.0 (Linux; Android 4.0.3; GT-I8190 Build/IMM76D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.102 Mobile Safari/537.36',
        'properties' => [
            'Comment' => 'Chrome 38.0',
            'Browser' => 'Chrome',
            'Browser_Type' => 'Browser',
            'Browser_Bits' => '32',
            'Browser_Maker' => 'Google Inc',
            'Browser_Modus' => 'unknown',
            'Version' => '38.0',
            'Platform' => 'Android',
            'Platform_Version' => '4.0',
            'Platform_Description' => 'Android OS',
            'Platform_Bits' => '32',
            'Platform_Maker' => 'Google Inc',
            'Device_Name' => 'general Mobile Phone',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'Mobile Phone',
            'Device_Pointing_Method' => 'touchscreen',
            'Device_Code_Name' => 'general Mobile Phone',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'Blink',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Maker' => 'Google Inc',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // IE 6 is not available on anything newer than Windows XP
    // https://en.wikipedia.org/wiki/Internet_Explorer_6
    // Vista shipped with IE 7.0
    'issue-000-invalid-versions-DP' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 6.0; Trident/5.1)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 6.0 not available on anything newer than Windows XP)
    // Windows 7 shipped with IE 8.0
    'issue-000-invalid-versions-DQ' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.1; Win64; x64; Trident/4.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0E; .NET4.0C)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 6.0 not available on anything newer than Windows XP)
    // Vista shipped with IE 7.0
    'issue-000-invalid-versions-DR' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 6.0; WOW64; Trident/5.0)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 6.0 not available on anything newer than Windows XP)
    // Vista shipped with IE 7.0
    'issue-000-invalid-versions-DS' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.0; Trident/5.0; SV1; BTRS102494; MRSPUTNIK 2, 4, 1, 44; .NET CLR 1.1.4322)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 6.0 not available on anything newer than Windows XP)
    // Windows 7 shipped with IE 8.0
    'issue-000-invalid-versions-DT' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 6.0 not available on anything newer than Windows XP)
    // Windows 7 shipped with IE 8.0
    'issue-000-invalid-versions-DU' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.1; Trident/4.0; 0B573FA1)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 6.0 not available on anything older than WinNT4)
    'issue-000-invalid-versions-DV' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT)',
        'properties' => [
            'Comment' => 'IE 6.0',
            'Browser' => 'IE',
            'Version' => '6.0',
            'Platform' => 'Win32',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
    // IE 7.0 is only available on Vista and XP (dropped support for older versions of Windows,
    // not installable on newer versions)
    // https://en.wikipedia.org/wiki/Internet_Explorer_7
    'issue-000-invalid-versions-DW' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.0; SV1)',
        'properties' => [
            'Comment' => 'IE 7.0',
            'Browser' => 'IE',
            'Version' => '7.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 7 doesn't support anything older than XP)
    'issue-000-invalid-versions-DX' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.0)',
        'properties' => [
            'Comment' => 'IE 7.0',
            'Browser' => 'IE',
            'Version' => '7.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 7 cannot be installed on anything newer than Windows Vista)
    'issue-000-invalid-versions-DY' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; SV1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; Alexa Toolbar; SE 2.X MetaSr 1.0)',
        'properties' => [
            'Comment' => 'IE 7.0',
            'Browser' => 'IE',
            'Version' => '7.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // IE 8.0 is only available on Windows XP, Windows Vista and Windows 7
    // https://support.microsoft.com/en-us/help/11531/internet-explorer-system-requirements
    // https://en.wikipedia.org/wiki/Internet_Explorer_8
    'issue-000-invalid-versions-DZ' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; InfoPath.2; .NET4.0C; .NET4.0E)',
        'properties' => [
            'Comment' => 'IE 8.0',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 8.0 only available on Windows XP, Windows Vista, Windows 7)
    'issue-000-invalid-versions-EA' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.0; Trident/4.0)',
        'properties' => [
            'Comment' => 'IE 8.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 8.0 only available on Windows XP, Windows Vista, Windows 7)
    'issue-000-invalid-versions-EB' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 3.0.04506.30) 88XI4n5O',
        'properties' => [
            'Comment' => 'IE 8.0',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 8.0 only available on Windows XP, Windows Vista, Windows 7)
    'issue-000-invalid-versions-EC' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E; SE 2.x)',
        'properties' => [
            'Comment' => 'IE 8.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 8.0 only available on Windows XP, Windows Vista, Windows 7)
    'issue-000-invalid-versions-ED' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.0; .NET CLR 3.5.30729)',
        'properties' => [
            'Comment' => 'IE 8.0',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 8.0 only available on Windows XP, Windows Vista, Windows 7)
    'issue-000-invalid-versions-EE' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0) Chrome/20.0.1229.79',
        'properties' => [
            'Comment' => 'IE 8.0',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 8.0 only available on Windows XP, Windows Vista, Windows 7)
    'issue-000-invalid-versions-EF' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; Trident/4.0; MRSPUTNIK 2, 4, 0, 471; GTB7.4; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; InfoPath.1; BRI/1)',
        'properties' => [
            'Comment' => 'IE 8.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '8.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // IE 9.0 is only available for Windows 7 and Windows Vista
    // https://support.microsoft.com/en-us/help/11531/internet-explorer-system-requirements
    // https://en.wikipedia.org/wiki/Internet_Explorer_9
    // It's not possible to downgrade later versions of windows to IE 9
    // https://answers.microsoft.com/en-us/ie/forum/ie10-windows_8/need-to-install-ie9-on-windows-8/865180b7-c2b2-4fec-ac9e-255663e8ea64
    'issue-000-invalid-versions-EG' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 5.1; Trident/4.0; BTRS105842; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)',
        'properties' => [
            'Comment' => 'IE 9.0',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EH' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.2 Win64; x64; Trident/6.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729',
        'properties' => [
            'Comment' => 'IE 9.0',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EI' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET CLR 1.1.4322; InfoPath.3; .NET4.0C; .NET4.0E; 360SE)',
        'properties' => [
            'Comment' => 'IE 9.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EJ' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/5.0; InfoPath.2; .NET CLR 3.0.04506.30; .NET4.0C; .NET4.0E; ; NCLIENT50_AAP5EEE161933B)',
        'properties' => [
            'Comment' => 'IE 9.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EK' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; 360SE)',
        'properties' => [
            'Comment' => 'IE 9.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EL' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.2; Trident/5.0; MAAU)',
        'properties' => [
            'Comment' => 'IE 9.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EM' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; Trident/5.0; InfoPath.2; .NET4.0C; .NET4.0E; Tablet PC 2.0)',
        'properties' => [
            'Comment' => 'IE 9.0',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 9.0 only available on Windows 7 and Windows Vista)
    'issue-000-invalid-versions-EN' => [
        'ua' => 'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 6.2; Win64; x64; Trident/5.0; .NET CLR 3.3.50784; Media Center PC 5.0; Zune 4.2; .NET4.0E; en-IN)',
        'properties' => [
            'Comment' => 'IE 9.0',
            'Browser' => 'IE',
            'Version' => '9.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // IE 10.0 is only available on Windows 7 and Windows 8
    // https://support.microsoft.com/en-us/help/11531/internet-explorer-system-requirements
    // https://en.wikipedia.org/wiki/Internet_Explorer_10
    'issue-000-invalid-versions-EO' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.3; WOW64; Trident/6.0; AF01A5; ; NCLIENT50_AAPC9158A9C5CE)',
        'properties' => [
            'Comment' => 'IE 10.0',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-EP' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.3; Trident/6.0)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-EQ' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.3; Win64; IA64; Trident/6.0)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-ER' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.0 Trident/6.0; ARM; Touch; WPDesktop)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-ES' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 6.0; SV1; 360SE)',
        'properties' => [
            'Comment' => 'IE 10.0',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-ET' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/6.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; InfoPath.3; .NET CLR 1.1.4322; 360SE)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-EU' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.0WOW64; Trident/6.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729',
        'properties' => [
            'Comment' => 'IE 10.0',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-EV' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/6.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E; InfoPath.3)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-EW' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; WOW64; Trident/6.0)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 10 only available on Windows 7 and Windows 8)
    'issue-000-invalid-versions-EX' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.2; WOW64; Trident/6.0; .NET4.0E; .NET4.0C; InfoPath.3; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729)',
        'properties' => [
            'Comment' => 'IE 10.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '10.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // IE 11.0 is only available on Windows 7, Windows 8.1 and Windows 10 (not Windows 8)
    // https://support.microsoft.com/en-us/help/11531/internet-explorer-system-requirements
    // https://en.wikipedia.org/wiki/Internet_Explorer_11
    'issue-000-invalid-versions-EY' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.2; WOW64; Trident/7.0; rv:11.0) like Gecko',
        'properties' => [
            'Comment' => 'IE 11.0',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-EZ' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; WOW64; Trident/7.0)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FA' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; WOW64; Trident/8.0; LCJB)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FB' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; Win64; x64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FC' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; Trident/7.0)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FD' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.2; WOW64; Trident/7.0; Touch; ASU2JS; rv:11.0) like Gecko',
        'properties' => [
            'Comment' => 'IE 11.0',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FE' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FF' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; Trident/8.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; SLCC2)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FG' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.2; Win64; x64; Trident/7.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; Tablet PC 2.0; GWX:QUALIFIED; MASAJS)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FH' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/7.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; Tablet PC 2.0; GWX:QUALIFIED; SE 2.x)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FI' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; Trident/8.0; MATB)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FJ' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/8.0; .NET4.0C; .NET4.0E; InfoPath.3)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FK' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Win64; x64; Trident/7.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; HPDTDFJS; McAfee; GWX:RESERVED)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FL' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; WOW64; Trident/7.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; HPDTDFJS)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FM' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; Win64; x64; Trident/8.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; SLCC2; Media Center PC 6.0; Tablet PC 2.0)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FN' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/8.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; Tablet PC 2.0; InfoPath.3; TNJB)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FO' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 11.0; Windows NT 6.2; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 1.1.4322; MAXTHON)',
        'properties' => [
            'Comment' => 'IE 11.0',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FP' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/8.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; Tablet PC 2.0; MAARJS)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FQ' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; WOW64; Trident/8.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; MALNJS)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FR' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/7.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E; GWX:QUALIFIED)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FS' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/8.0; DonutRAPT/135; ; 10sdong_AAP34CC8A61AE9)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 10.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FT' => [
        'ua' => 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; WOW64; Trident/7.0; .NET4.0E; .NET4.0C; InfoPath.3; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; 360SE)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FU' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.2; WOW64; Trident/7.0; .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729; HPDTDFJS)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 7.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FV' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; Win64; x64; Trident/7.0; MAFSJS)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 9.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FW' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.0; WOW64; Trident/7.0;  .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 10.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FX' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.0 Win64; x64; Trident/7.0;  .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 10.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FY' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2 Trident/7.0;  .NET4.0E; .NET4.0C; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 10.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win32',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // Same as above (IE 11 only available on Windows 7, Windows 8.1 and Windows 10)
    'issue-000-invalid-versions-FZ' => [
        'ua' => 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.2; Win64; x64; Trident/7.0; .NET4.0E; .NET4.0C; Media Center PC 6.0; .NET CLR 3.5.30729; .NET CLR 2.0.50727; .NET CLR 3.0.30729)',
        'properties' => [
            'Comment' => 'IE 11.0 in IE 8.0 Compatibility Mode',
            'Browser' => 'IE',
            'Version' => '11.0',
            'Platform' => 'Win64',
        ],
        'lite' => true,
        'standard' => true,
        'full' => true,
    ],
    // there is no version 40.1 of Firefox
    'issue-000-invalid-versions-GA' => [
        'ua' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
        'properties' => [
            'Comment' => 'Firefox Generic',
            'Browser' => 'Firefox',
            'Version' => '0.0',
            'Platform' => 'Win7',
            'Platform_Version' => '6.1',
            'Platform_Description' => 'Windows 7',
            'RenderingEngine_Name' => 'Gecko',
            'RenderingEngine_Version' => 'unknown',
        ],
        'lite' => false,
        'standard' => true,
        'full' => true,
    ],
    // PowerPC support was dropped in SeaMonkey 2.1: https://www.seamonkey-project.org/releases/seamonkey2.1/
    'issue-000-invalid-versions-GB' => [
        'ua' => 'Mozilla/5.0 (Macintosh; PPC Mac OS X 10.5; rv:15.0) Gecko/20120910 SeaMonkey/2.12.1',
        'properties' => [
            'Comment' => 'SeaMonkey Generic',
            'Browser' => 'SeaMonkey',
            'Version' => '0.0',
            'Platform' => 'MacOSX',
            'Platform_Version' => '10.5',
            'Platform_Description' => 'Mac OS X for Power PC',
        ],
        'lite' => false,
        'standard' => false,
        'full' => true,
    ],
];

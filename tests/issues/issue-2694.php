<?php
 declare(strict_types = 1);

 return [
    'issue-2694-A' => [
        'ua' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Safari/605.1.15',
        'properties' => [
            "Platform" => "macOS",
            "Platform_Version" => "10.15",
            "Browser_Version" => "Safari",
            "Browser_Version" => "15.6",
        ],
        'lite' => false,
        'standard' => true,
        'full' => false,
    ],
     'issue-2694-B' => [
         'ua' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Mobile/15E148 Safari/604.1',
         'properties' => [
             "Platform"=> "iOS",
             'Platform_Version' => '15.6',
             'Platform_Maker' => 'Apple Inc',
             "Browser_Version" => "Safari",
             "Browser_Version" => "15.6",
             "Comment" => "Mobile Safari 15.6",
             "Device_Name" => "iPhone",
         ],
         'lite' => false,
         'standard' => true,
         'full' => true,
     ],
     'issue-2694-C' => [
         'ua' => 'Mozilla/5.0 (iPad; CPU OS 15_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Mobile/15E148 Safari/604.1',
         'properties' => [
             "Platform"=> "ipadOS",
             'Platform_Version' => '15.6',
             'Platform_Maker' => 'Apple Inc',
             "Browser_Version" => "Safari",
             "Browser_Version" => "15.6",
             "Comment" => "Mobile Safari 15.6",
             "Device_Name" => "iPad",
         ],
         'lite' => false,
         'standard' => true,
         'full' => true,
     ]
 ];
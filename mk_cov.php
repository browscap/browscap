<?php

ini_set('memory_limit', -1);

require_once __DIR__ . '/vendor/autoload.php';

use Seld\JsonLint\Lexer;

$symbols = [
    'error'                 => 2,
    'JSONString'            => 3,
    'STRING'                => 4,
    'JSONNumber'            => 5,
    'NUMBER'                => 6,
    'JSONNullLiteral'       => 7,
    'NULL'                  => 8,
    'JSONBooleanLiteral'    => 9,
    'TRUE'                  => 10,
    'FALSE'                 => 11,
    'JSONText'              => 12,
    'JSONValue'             => 13,
    'EOF'                   => 14,
    'JSONObject'            => 15,
    'JSONArray'             => 16,
    '{'                     => 17,
    '}'                     => 18,
    'JSONMemberList'        => 19,
    'JSONMember'            => 20,
    ':'                     => 21,
    ','                     => 22,
    '['                     => 23,
    ']'                     => 24,
    'JSONElementList'       => 25,
    '$accept'               => 0,
    '$end'                  => 1,
];

$symbolLookup = array_flip($symbols);

$ids = explode("\n", file_get_contents('./patternids.txt'));
$covered = explode("\n", file_get_contents('./results.txt'));

function isCovered($id, $covered) {
    $id = str_replace('\/', '/', $id);
    list($u, $c, $d, $p) = explode('::', $id);

    $u = substr($u, 1);
    $c = substr($c, 1);
    $p = preg_quote(substr($p, 1), '/');
    $d = preg_quote(substr($d, 1), '/');

    $count = 0;

    if (strlen($p) == 0) {
        $p = '.*?';
    }
    if (strlen($d) == 0) {
        $d = '.*?';
    }

    $regex = sprintf('/^u%d::c%d::d%s::p%s$/', $u, $c, $d, $p);

    foreach ($covered as $patternId) {
        if (preg_match($regex, $patternId)) {
            $count++;
        }
    }

    return $count;
}

$files = [];

foreach ($ids as $idLine) {
    $file = substr($idLine, 0, strpos($idLine, '::'));

    if (!isset($files[$file])) {
        $files[$file] = ['ids' => [], 'covered' => []];
    }

    //$files[$file]['ids']++;
    $files[$file]['ids'][] = substr($idLine, strpos($idLine, '::') + 2);
}

foreach ($covered as $idLine) {
    $file = substr($idLine, 0, strpos($idLine, '::'));

    if (!isset($files[$file])) {
        $files[$file] = ['ids' => [], 'covered' => []];
    }

    //$files[$file]['ids']++;
    $files[$file]['covered'][] = substr($idLine, strpos($idLine, '::') + 2);
}

$coverage = [];

$funcCount = 0;

$i = 0;

foreach ($files as $file => $data) {

    $coveredLines = [];
    $touchedLines = [];

    $coverage[$file] = [
        'path' => $file,
        'statementMap' => [],
        'fnMap' => [],
        'branchMap' => [],
        's' => [],
        'b' => [],
        'f' => [],
        'l' => [],
    ];

    $contents = file_get_contents($file);
    $lines = explode("\n", $contents);

    $u = null;
    $d = '';
    $c = null;
    $p = '';

    $ignores = [];
    $sectionBranches = [];

    $state = '';

    $lexer = new Lexer();
    $lexer->setInput($contents);

    do {
        $code = $lexer->lex();
        $type = $symbolLookup[$code];
        $content = $lexer->yytext;
        $line = $lines[$lexer->yylineno];

        switch ($state) {
            case '':
                if ($type == '{') {
                    $state = 'inDivision';
                    continue;
                }
                break;
            case 'inDivision':
                if ($type == 'STRING' && $content == 'userAgents') {
                    $state = 'inUaGroup';
                    continue;
                }
                break;
            case 'inUaGroup':
                if ($type == '{') {
                    $state = 'inEachUa';
                    if ($u === null) {
                        $u = 0;
                    } else {
                        $u++;
                    }
                }
                if ($type == '}') {
                    $state = 'inDivision';
                    $u = null;
                }
                break;
            case 'inEachUa':
                if (!isset($ignores[$state]['}'])) {
                    $ignores[$state]['}'] = 0;
                }

                if ($type == '}' && $ignores[$state]['}'] == 0) {
                    $state = 'inUaGroup';
                    $ignores[$state]['}'] = 0;
                    continue;
                }
                if ($type == '{') {
                    $ignores[$state]['}']++;
                }
                if ($type == '}') {
                    $ignores[$state]['}']--;
                }
                if ($type == 'STRING' && $content == 'children') {
                    $state = 'inChildGroup';
                }
                break;
            case 'inChildGroup':
                if ($type == '{') {
                    $state = 'inEachChild';
                    if ($c === null) {
                        $c = 0;
                    } else {
                        $c++;
                    }
                }
                if ($type == '}') {
                    $state = 'inEachUa';
                    $c = null;
                }
                break;
            case 'inEachChild':
                if (!isset($ignores[$state]['}'])) {
                    $ignores[$state]['}'] = 0;
                }

                if ($type == '}' && $ignores[$state]['}'] == 0) {
                    $state = 'inChildGroup';

                    if ($collectFunctionEnd == true) {
                        $coverage[$file]['statementMap'][] = [
                            'start' => $collectFunctionStart,
                            'end' => [
                                'line' => $lexer->yylineno + 1,
                                'column' => strpos($line, $content) + strlen($content),
                            ]
                        ];
                        $coverage[$file]['s'][] += isCovered('u' . $u . '::c' . $c . '::d::p', $files[$file]['covered']);
                        $collectFunctionEnd = false;
                    }

                    continue;
                }

                if ($type == '{') {
                    $ignores[$state]['}']++;
                }
                if ($type == '}') {
                    $ignores[$state]['}']--;
                }

                if ($type == 'STRING' && $content == 'platforms') {
                    $state = 'inPlatforms';
                } elseif ($type == 'STRING' && $content == 'devices') {
                    // The closing } is caught inside the devices group
                    $ignores[$state]['}']--;
                    $state = 'inDevices';
                } elseif ($type == 'STRING' && $content == 'match') {
                    $collectMatchPosition = true;
                } elseif ($type == 'STRING' && $collectMatchPosition) {
                    $coverage[$file]['fnMap'][] = [
                        'name' => '(anonymous_' . $funcCount . ')',
                        'line' => $lexer->yylineno + 1,
                        'loc' => [
                            'start' => [
                                'line' => $lexer->yylineno + 1,
                                'column' => strpos($line, '"' . $content . '"'),
                            ],
                            'end' => [
                                'line' => $lexer->yylineno + 1,
                                'column' => strpos($line, '"' . $content . '"') + strlen($content) + 2,
                            ]
                        ],
                    ];
                    $coverage[$file]['f'][] = isCovered('u' . $u . '::c' . $c . '::d::p', $files[$file]['covered']);
                    @$touchedLines[$lexer->yylineno + 1]++;
                    if (isCovered('u' . $u . '::c' . $c . '::d::p', $files[$file]['covered']) > 0) {
                        @$coveredLines[$lexer->yylineno + 1]++;
                    }
                    $funcCount++;
                    $collectMatchPosition = false;
                    $collectFunctionEnd = true;
                    $collectFunctionStart = ['line' => $lexer->yylineno + 1, 'column' => strpos($line, '"' . $content . '"')];
                }
                break;
            case 'inPlatforms':
                if ($type == 'STRING') {
                    $p = $content;
                    $id = 'u' . $u . '::c' . $c . '::d::p' . $p;
                    $coverCount = isCovered(
                        $id,
                        $files[$file]['covered']
                    );

                    $sectionBranches[] = [
                        'start' => [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $content . '"'),
                        ],
                        'end' => [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $content . '"') + strlen($content) + 2,
                        ],
                        'coverCount' => $coverCount,
                    ];
                }

                if ($type == ']') {
                    $state = 'inEachChild';
                    $p = '';
                    $locations = [];

                    foreach ($sectionBranches as $branch) {
                        $locations[] = $branch;
                    }

                    $branch = [
                        'line' => $lexer->yylineno + 1,
                        'type' => 'switch',
                        'locations' => $locations,
                    ];

                    $coverArray = [];

                    foreach ($locations as $location) {
                        $coverage[$file]['statementMap'][] = [
                            'start' => $location['start'],
                            'end' => $location['end'],
                        ];
                        @$touchedLines[$location['start']['line']]++;
                        $coverage[$file]['s'][] += $location['coverCount'];
                        if ($location['coverCount']) {
                            @$coveredLines[$location['start']['line']]++;
                        }
                        $coverArray[] = $location['coverCount'];
                    }

                    $coverage[$file]['b'][] = $coverArray;

                    foreach (array_keys($branch['locations']) as $index) {
                        unset($branch['locations'][$index]['coverCount']);
                    }

                    $coverage[$file]['branchMap'][] = $branch;

                    $sectionBranches = [];
                }
                break;
            case 'inDevices':
                if ($type == 'STRING' && substr($line, strpos($line, '"' . $content . '"') + strlen('"' . $content . '"'), 1) == ':') {
                    $d = $content;

                    $id = 'u' . $u . '::c' . $c . '::d' . $d . '::p';
                    $coverCount = isCovered(
                        $id,
                        $files[$file]['covered']
                    );

                    $sectionBranches[] = [
                        'start' => [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $content . '"'),
                        ],
                        'end' => [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $content . '"') + strlen($content) + 2,
                        ],
                        'coverCount' => $coverCount,
                    ];
                }

                if ($type == '}') {
                    $state = 'inEachChild';
                    $d = '';
                    $locations = [];

                    foreach ($sectionBranches as $branch) {
                        $locations[] = $branch;
                    }

                    $branch = [
                        'line' => $lexer->yylineno + 1,
                        'type' => 'switch',
                        'locations' => $locations,
                    ];

                    $coverArray = [];

                    foreach ($locations as $location) {
                        $coverage[$file]['statementMap'][] = [
                            'start' => $location['start'],
                            'end' => $location['end'],
                        ];
                        @$touchedLines[$location['start']['line']]++;
                        $coverage[$file]['s'][] += $location['coverCount'];
                        if ($location['coverCount']) {
                            @$coveredLines[$location['start']['line']]++;
                        }
                        $coverArray[] = $location['coverCount'];
                    }

                    $coverage[$file]['b'][] = $coverArray;

                    foreach (array_keys($branch['locations']) as $index) {
                        unset($branch['locations'][$index]['coverCount']);
                    }

                    $coverage[$file]['branchMap'][] = $branch;

                    $sectionBranches = [];
                }
                break;
        }
    } while ($code !== 1);

    foreach ($touchedLines as $lineNum => $count) {
        if ($count > 0) {
            $coverage[$file]['l'][$lineNum] = 0;

            if (isset($coveredLines[$lineNum])) {
                $coverage[$file]['l'][$lineNum] += $coveredLines[$lineNum];
            }
        }
    }

    // This re-indexes the arrays to be 1 based instead of 0, which will make them be JSON objects rather
    // than arrays, which is how they're expected to be in the coverage JSON file
    $coverage[$file]['fnMap'] = array_filter(array_merge([0], $coverage[$file]['fnMap']));
    $coverage[$file]['statementMap'] = array_filter(array_merge([0], $coverage[$file]['statementMap']));
    $coverage[$file]['branchMap'] = array_filter(array_merge([0], $coverage[$file]['branchMap']));
    array_unshift($coverage[$file]['b'], '');
    unset($coverage[$file]['b'][0]);
    array_unshift($coverage[$file]['f'], '');
    unset($coverage[$file]['f'][0]);
    array_unshift($coverage[$file]['s'], '');
    unset($coverage[$file]['s'][0]);

    $i++;

    //if ($i >= 50) {
    //    print json_encode($coverage, JSON_UNESCAPED_SLASHES);
    //    exit;
    //}
}

print json_encode($coverage);

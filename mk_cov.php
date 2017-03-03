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

function isPatternCovered($id, $covered, $available)
{
    list($u, $c) = explode('::', $id);
    $u = substr($u, 1);
    $c = substr($c, 1);

    $regex = sprintf('/^u%d::c%d::.*$/', $u, $c);

    foreach ($available as $patternId) {
        if (preg_match($regex, $patternId)) {
            if (in_array($patternId, $covered)) {
                return 1;
            }
        }
    }

    return 0;
}

$files = [];

foreach ($ids as $idLine) {
    $file = substr($idLine, 0, strpos($idLine, '::'));

    if (!isset($files[$file])) {
        $files[$file] = ['ids' => [], 'covered' => []];
    }

    $files[$file]['ids'][] = substr($idLine, strpos($idLine, '::') + 2);
}

foreach ($covered as $idLine) {
    $file = substr($idLine, 0, strpos($idLine, '::'));

    if (!isset($files[$file])) {
        $files[$file] = ['ids' => [], 'covered' => []];
    }

    $files[$file]['covered'][] = substr($idLine, strpos($idLine, '::') + 2);
}

$coverage = [];
$funcCount = 0;

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
    $waitForColon = false;

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
                    $ignores[$state]['}'] = 0;
                    $state = 'inUaGroup';
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
                } elseif ($type == ']') {
                    $state = 'inEachUa';
                    $c = null;
                }
                break;
            case 'inEachChild':
                if (!isset($ignores[$state]['}'])) {
                    $ignores[$state]['}'] = 0;
                }

                if ($type == '}' && $ignores[$state]['}'] == 0) {
                    $ignores[$state]['}'] = 0;
                    $state = 'inChildGroup';

                    if ($collectFunctionEnd == true) {
                        $coverage[$file]['statementMap'][] = [
                            'start' => $collectFunctionStart,
                            'end' => [
                                'line' => $lexer->yylineno + 1,
                                'column' => strpos($line, $content) + strlen($content),
                            ]
                        ];
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
                    $state = 'inDevices';
                    $waitForColon = false;
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
                    $functionCoverage = isCovered('u' . $u . '::c' . $c . '::d::p', $files[$file]['covered']);
                    $coverage[$file]['f'][] = $functionCoverage;
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
                    $coverCount = isCovered($id, $files[$file]['covered']);

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
                        //$coverage[$file]['s'][] = $location['coverCount'];
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
                if ($type == 'STRING' && $waitForColon == false) {
                    $deviceKey = $content;
                    $waitForColon = true;
                }

                if ($type == ':' && $waitForColon == true) {
                    $d = $deviceKey;

                    $id = 'u' . $u . '::c' . $c . '::d' . $d . '::p';

                    $coverCount = isCovered($id, $files[$file]['covered']);

                    $sectionBranches[] = [
                        'start' => [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $d . '"'),
                        ],
                        'end' => [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $d . '"') + strlen($d) + 2,
                        ],
                        'coverCount' => $coverCount,
                    ];

                    $waitForColon = false;
                    $deviceKey = '';
                }

                if ($type == ',' && $waitForColon == true) {
                    $waitForColon = false;
                    $deviceKey = '';
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
                        //$coverage[$file]['s'][] = $location['coverCount'];
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

    foreach ($coverage[$file]['fnMap'] as $index => $function) {
        if ($coverage[$file]['f'][$index] > 0) {
            $startLine = $function['loc']['start']['line'];
            $endLine = $function['loc']['end']['line'];

            for ($i = $startLine; $i <= $endLine; $i++) {
                if (!isset($coverage[$file]['l'][$i])) {
                    $coverage[$file]['l'][$i] = 0;
                }
                $coverage[$file]['l'][$i] += $coverage[$file]['f'][$index];
            }
        }
    }

    foreach ($coverage[$file]['branchMap'] as $index => $branch) {
        if (!empty($coverage[$file]['b'][$index])) {
            foreach ($branch['locations'] as $subIndex => $location) {
                $startLine = $branch['locations'][$subIndex]['start']['line'];
                $endLine = $branch['locations'][$subIndex]['end']['line'];

                for ($i = $startLine; $i <= $endLine; $i++) {
                    if (!isset($coverage[$file]['l'][$i])) {
                        $coverage[$file]['l'][$i] = 0;
                    }

                    $coverage[$file]['l'][$i] += $coverage[$file]['b'][$index][$subIndex];
                }
            }
        }
    }

    foreach ($coverage[$file]['statementMap'] as $index => $statement) {
        $startLine = $statement['start']['line'];
        $endLine = $statement['end']['line'];
        $covered = true;

        for ($i = $startLine; $i <= $endLine; $i++) {
            // If all lines of statement are covered, we'll mark the statement as covered
            if (!isset($coverage[$file]['l'][$i]) || $coverage[$file]['l'][$i] < 1) {
                $covered = false;
                break;
            }
        }

        if ($covered === true) {
            $coverage[$file]['s'][$index] = 1;
        } else {
            $coverage[$file]['s'][$index] = 0;
        }
    }

    ksort($coverage[$file]['l']);

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
}

file_put_contents('coverage.json', str_replace('[]', '{}', json_encode($coverage, JSON_UNESCAPED_SLASHES)));

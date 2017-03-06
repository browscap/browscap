<?php

namespace Browscap\Coverage;

use Seld\JsonLint\Lexer;

class Processor
{
    private $resourceDir;

    // These are available in the JSON Parser part of the JSON Lint package we're using
    // to Lexically analyze the JSON file, but they're a private property so can't re-use them
    private $symbols = [
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

    private $symbolLookup = [];

    private $coveredIds = [];

    private $coverage = [];

    private $funcCount = 0;

    public function __construct($resourceDir)
    {
        $this->resourceDir = $resourceDir;

        $this->symbolLookup = array_flip($this->symbols);
    }

    public function process(array $coveredIds)
    {
        $this->groupIdsByFile($coveredIds);

        $iterator = new \RecursiveDirectoryIterator($this->resourceDir);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() !== 'json') {
                continue;
            }

            $this->processFile(substr($file->getPathname(), strpos($file->getPathname(), 'resources/')));
        }
    }

    public function write($fileName)
    {
        file_put_contents(
            $fileName,
            // str_replace here is to convert empty arrays into empty JS objects, which is expected by
            // codecov.io. Owner of the service said he was going to patch it, haven't tested since
            str_replace('[]', '{}', json_encode($this->coverage, JSON_UNESCAPED_SLASHES))
        );
    }

    private function processFile($file)
    {
        if (!isset($this->coveredIds[$file])) {
            $this->coveredIds[$file] = [];
        }

        $this->coverage[$file] = [
            'path' => $file,
            'statementMap' => [],
            'fnMap' => [],
            'branchMap' => [],
            's' => [],
            'b' => [],
            'f' => [],
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
        $collectMatchPosition = false;

        $state = '';

        $lexer = new Lexer();
        $lexer->setInput($contents);

        do {
            $code = $lexer->lex();
            $type = $this->symbolLookup[$code];
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
                            $this->coverage[$file]['fnMap'][] = [
                                'name' => '(anonymous_' . $this->funcCount . ')',
                                'decl' => $collectFunctionDecl,
                                'loc' => [
                                    'start' => $collectFunctionDecl['start'],
                                    'end' => [
                                        'line' => $lexer->yylineno + 1,
                                        'column' => strpos($line, $content) + strlen($content),
                                    ]
                                ],
                            ];
                            $functionCoverage = $this->getCoverageCount('u' . $u . '::c' . $c . '::d::p', $this->coveredIds[$file]);
                            $this->coverage[$file]['f'][] = $functionCoverage;
                            $this->funcCount++;
                            $this->coverage[$file]['statementMap'][] = [
                                'start' => $collectFunctionDecl['start'],
                                'end' => [
                                    'line' => $lexer->yylineno + 1,
                                    'column' => strpos($line, $content) + strlen($content),
                                ]
                            ];
                            $this->coverage[$file]['s'][] = $functionCoverage;
                            $this->coverage[$file]['statementMap'][] = [
                                'start' => $collectFunctionDecl['start'],
                                'end' => $collectFunctionDecl['end'],
                            ];
                            $this->coverage[$file]['s'][] = $functionCoverage;

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
                        $collectMatchPosition = false;
                        $collectFunctionEnd = true;
                        $collectFunctionDecl = [
                            'start' => ['line' => $lexer->yylineno + 1, 'column' => strpos($line, '"' . $content . '"')],
                            'end' => ['line' => $lexer->yylineno + 1, 'column' => strpos($line, '"' . $content . '"') + strlen($content) + 2],
                        ];
                    }
                    break;
                case 'inPlatforms':
                    if ($type == 'STRING') {
                        $p = $content;
                        $id = 'u' . $u . '::c' . $c . '::d::p' . $p;
                        $coverCount = $this->getCoverageCount($id, $this->coveredIds[$file]);

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
                            'type' => 'switch',
                            'locations' => $locations,
                            'loc' => ['start' => $locations[0]['start'], 'end' => $locations[count($locations) - 1]['end']],
                        ];

                        $coverArray = [];

                        foreach ($locations as $location) {
                            $this->coverage[$file]['statementMap'][] = [
                                'start' => $location['start'],
                                'end' => $location['end'],
                            ];
                            $this->coverage[$file]['s'][] = $location['coverCount'];
                            $coverArray[] = $location['coverCount'];
                        }

                        $this->coverage[$file]['b'][] = $coverArray;

                        foreach (array_keys($branch['locations']) as $index) {
                            unset($branch['locations'][$index]['coverCount']);
                        }

                        $this->coverage[$file]['branchMap'][] = $branch;

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

                        $coverCount = $this->getCoverageCount($id, $this->coveredIds[$file]);

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
                            'type' => 'switch',
                            'locations' => $locations,
                            'loc' => ['start' => $locations[0]['start'], 'end' => $locations[count($locations) - 1]['end']],
                        ];

                        $coverArray = [];

                        foreach ($locations as $location) {
                            $this->coverage[$file]['statementMap'][] = [
                                'start' => $location['start'],
                                'end' => $location['end'],
                            ];
                            $this->coverage[$file]['s'][] = $location['coverCount'];
                            $coverArray[] = $location['coverCount'];
                        }

                        $this->coverage[$file]['b'][] = $coverArray;

                        foreach (array_keys($branch['locations']) as $index) {
                            unset($branch['locations'][$index]['coverCount']);
                        }

                        $this->coverage[$file]['branchMap'][] = $branch;

                        $sectionBranches = [];
                    }
                    break;
            }
        } while ($code !== 1);

        // This re-indexes the arrays to be 1 based instead of 0, which will make them be JSON objects rather
        // than arrays, which is how they're expected to be in the coverage JSON file
        $this->coverage[$file]['fnMap'] = array_filter(array_merge([0], $this->coverage[$file]['fnMap']));
        $this->coverage[$file]['statementMap'] = array_filter(array_merge([0], $this->coverage[$file]['statementMap']));
        $this->coverage[$file]['branchMap'] = array_filter(array_merge([0], $this->coverage[$file]['branchMap']));
        array_unshift($this->coverage[$file]['b'], '');
        unset($this->coverage[$file]['b'][0]);
        array_unshift($this->coverage[$file]['f'], '');
        unset($this->coverage[$file]['f'][0]);
        array_unshift($this->coverage[$file]['s'], '');
        unset($this->coverage[$file]['s'][0]);
    }

    private function groupIdsByFile($ids)
    {
        foreach ($ids as $id) {
            $file = substr($id, 0, strpos($id, '::'));

            if (!isset($this->coveredIds[$file])) {
                $this->coveredIds[$file] = [];
            }

            $this->coveredIds[$file][] = substr($id, strpos($id, '::') + 2);
        }
    }

    private function getCoverageCount($id, $covered)
    {
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
}

<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Coverage;

use Seld\JsonLint\Lexer;

/**
 * Class Processor
 *
 * @category   Browscap
 * @author     Jay Klehr <jay.klehr@gmail.com>
 */
class Processor
{
    /**
     * @var string
     */
    private $resourceDir;

    /**
     * These are available in the JSON Parser part of the JSON Lint package we're using
     * to Lexically analyze the JSON file, but they're a private property so can't re-use them directly
     *
     * @var array
     */
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

    /**
     * @var array
     */
    private $symbolLookup = [];

    /**
     * @var array
     */
    private $coveredIds = [];

    /**
     * @var array
     */
    private $coverage = [];

    /**
     * @var int
     */
    private $funcCount = 0;

    /**
     * Create a new Coverage Processor for the specified directory
     *
     * @param string $resourceDir
     */
    public function __construct($resourceDir)
    {
        $this->resourceDir = $resourceDir;

        $this->symbolLookup = array_flip($this->symbols);
    }

    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param array $coveredIds
     */
    public function process(array $coveredIds)
    {
        $this->setCoveredPatternIds($coveredIds);

        $iterator = new \RecursiveDirectoryIterator($this->resourceDir);

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            /** @var $file \SplFileInfo */
            if (!$file->isFile() || $file->getExtension() !== 'json') {
                continue;
            }

            $patternFileName = substr($file->getPathname(), strpos($file->getPathname(), 'resources/'));

            if (!isset($this->coveredIds[$patternFileName])) {
                $this->coveredIds[$patternFileName] = [];
            }

            $this->coverage[$patternFileName] = $this->processFile(
                $patternFileName,
                file_get_contents($file->getPathname()),
                $this->coveredIds[$patternFileName]
            );
        }
    }

    /**
     * Write the coverage data in JSON format to specified filename
     *
     * @param string $fileName
     */
    public function write($fileName)
    {
        file_put_contents(
            $fileName,
            // str_replace here is to convert empty arrays into empty JS objects, which is expected by
            // codecov.io. Owner of the service said he was going to patch it, haven't tested since
            str_replace('[]', '{}', json_encode($this->coverage, JSON_UNESCAPED_SLASHES))
        );
    }

    /**
     * Stores passed in pattern ids, grouping them by file first
     *
     * @param array $coveredIds
     */
    public function setCoveredPatternIds(array $coveredIds)
    {
        $this->coveredIds = $this->groupIdsByFile($coveredIds);
    }

    /**
     * Returns the grouped pattern ids previously set
     *
     * @return array
     */
    public function getCoveredPatternIds()
    {
        return $this->coveredIds;
    }

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param string $file
     * @param string $contents
     * @param array $coveredIds
     * @return array
     */
    public function processFile($file, $contents, array $coveredIds)
    {
        $coverage = [
            'path' => $file,
            'statementMap' => [],
            'fnMap' => [],
            'branchMap' => [],
            's' => [],
            'b' => [],
            'f' => [],
        ];

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
                            $coverage['fnMap'][] = [
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
                            $functionCoverage = $this->getCoverageCount('u' . $u . '::c' . $c . '::d::p', $coveredIds);
                            $coverage['f'][] = $functionCoverage;
                            $this->funcCount++;
                            $coverage['statementMap'][] = [
                                'start' => $collectFunctionDecl['start'],
                                'end' => [
                                    'line' => $lexer->yylineno + 1,
                                    'column' => strpos($line, $content) + strlen($content),
                                ]
                            ];
                            $coverage['s'][] = $functionCoverage;
                            $coverage['statementMap'][] = [
                                'start' => $collectFunctionDecl['start'],
                                'end' => $collectFunctionDecl['end'],
                            ];
                            $coverage['s'][] = $functionCoverage;

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
                        $platformDefinitionStart = [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $content . '"')
                        ];
                    } elseif ($type == 'STRING' && $content == 'devices') {
                        $state = 'inDevices';
                        $waitForColon = false;
                        $deviceDefinitionStart = [
                            'line' => $lexer->yylineno + 1,
                            'column' => strpos($line, '"' . $content . '"')
                        ];
                    } elseif ($type == 'STRING' && $content == 'match') {
                        $collectMatchPosition = true;
                    } elseif ($type == 'STRING' && $collectMatchPosition) {
                        $collectMatchPosition = false;
                        $collectFunctionEnd = true;
                        $collectFunctionDecl = [
                            'start' => [
                                'line' => $lexer->yylineno + 1,
                                'column' => strpos($line, '"' . $content . '"')
                            ],
                            'end' => [
                                'line' => $lexer->yylineno + 1,
                                'column' => strpos($line, '"' . $content . '"') + strlen($content) + 2
                            ],
                        ];
                    }
                    break;
                case 'inPlatforms':
                    if ($type == 'STRING') {
                        $p = $content;
                        $id = 'u' . $u . '::c' . $c . '::d::p' . $p;
                        $coverCount = $this->getCoverageCount($id, $coveredIds);

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
                        $coverage['statementMap'][] = [
                            'start' => $platformDefinitionStart,
                            'end' => ['line' => $lexer->yylineno + 1, 'column' => strpos($line, ']') + 1]
                        ];
                        $coverage['s'][] = array_sum(array_column($locations, 'coverCount'));

                        $coverArray = [];

                        foreach ($locations as $location) {
                            $coverage['statementMap'][] = [
                                'start' => $location['start'],
                                'end' => $location['end'],
                            ];
                            $coverage['s'][] = $location['coverCount'];
                            $coverArray[] = $location['coverCount'];
                        }

                        $coverage['b'][] = $coverArray;

                        foreach (array_keys($branch['locations']) as $index) {
                            unset($branch['locations'][$index]['coverCount']);
                        }

                        $coverage['branchMap'][] = $branch;

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

                        $coverCount = $this->getCoverageCount($id, $coveredIds);

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
                        // Log the entire branch statement as a statement
                        $coverage['statementMap'][] = [
                            'start' => $deviceDefinitionStart,
                            'end' => ['line' => $lexer->yylineno + 1, 'column' => strpos($line, '}') + 1]
                        ];
                        $coverage['s'][] = array_sum(array_column($locations, 'coverCount'));

                        $coverArray = [];

                        foreach ($locations as $location) {
                            $coverage['statementMap'][] = [
                                'start' => $location['start'],
                                'end' => $location['end'],
                            ];
                            $coverage['s'][] = $location['coverCount'];
                            $coverArray[] = $location['coverCount'];
                        }

                        $coverage['b'][] = $coverArray;

                        foreach (array_keys($branch['locations']) as $index) {
                            unset($branch['locations'][$index]['coverCount']);
                        }

                        $coverage['branchMap'][] = $branch;

                        $sectionBranches = [];
                    }
                    break;
            }
        } while ($code !== 1);

        // This re-indexes the arrays to be 1 based instead of 0, which will make them be JSON objects rather
        // than arrays, which is how they're expected to be in the coverage JSON file
        $coverage['fnMap'] = array_filter(array_merge([0], $coverage['fnMap']));
        $coverage['statementMap'] = array_filter(array_merge([0], $coverage['statementMap']));
        $coverage['branchMap'] = array_filter(array_merge([0], $coverage['branchMap']));
        array_unshift($coverage['b'], '');
        unset($coverage['b'][0]);
        array_unshift($coverage['f'], '');
        unset($coverage['f'][0]);
        array_unshift($coverage['s'], '');
        unset($coverage['s'][0]);

        return $coverage;
    }

    /**
     * Groups pattern ids by their filename prefix
     *
     * @param array $ids
     * @return array
     */
    private function groupIdsByFile(array $ids)
    {
        $covered = [];

        foreach ($ids as $id) {
            $file = substr($id, 0, strpos($id, '::'));

            if (!isset($covered[$file])) {
                $covered[$file] = [];
            }

            $covered[$file][] = substr($id, strpos($id, '::') + 2);
        }

        return $covered;
    }

    /**
     * Counts number of times given pattern is covered by test patterns
     *
     * @param string $id
     * @param array $covered
     * @return int
     */
    private function getCoverageCount($id, array $covered)
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

        //print $regex . PHP_EOL;
        //print_r($covered);

        foreach ($covered as $patternId) {
            if (preg_match($regex, $patternId)) {
                $count++;
            }
        }

        return $count;
    }
}

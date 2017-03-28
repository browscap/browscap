<?php

declare(strict_types=1);

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
final class Processor implements ProcessorInterface
{
    /**@+
     * The codes representing different JSON elements
     *
     * These come from the Seld\JsonLint\JsonParser class. The values are returned by the lexer when
     * the lex() method is called.
     *
     * @var int
     */
    const JSON_OBJECT_START = 17;
    const JSON_OBJECT_END   = 18;
    const JSON_ARRAY_START  = 23;
    const JSON_ARRAY_END    = 24;
    const JSON_EOF          = 1;
    const JSON_STRING       = 4;
    const JSON_COLON        = 21;
    /**@-*/

    /**
     * @var string
     */
    private $resourceDir;

    /**
     * The pattern ids encountered during the test run. These are compared against the JSON file structure to determine
     * if the statement/function/branch is covered.
     *
     * @var string[]
     */
    private $coveredIds = [];

    /**
     * This is the full coverage array that gets output in the write method.  For each file an entry in the array
     * is added.  Each entry contains the elements required for Istanbul compatible coverage reporters.
     *
     * @var array
     */
    private $coverage = [];

    /**
     * An incrementing integer for every "function" (child match) encountered in all processed files. This is used
     * to name the anonymous functions in the coverage report.
     *
     * @var int
     */
    private $funcCount = 0;

    /**
     * A storage variable for the lines of a file while processing that file, used for determining column
     * position of a statement/function/branch
     *
     * @var string[]
     */
    private $fileLines = [];

    /**
     * A storage variable of the pattern ids covered by tests for a specific file (set when processing of that
     * file begins)
     *
     * @var string[]
     */
    private $fileCoveredIds = [];

    /**
     * A temporary storage for coverage information for a specific file that is later merged into the main $coverage
     * property after the file is done processing.
     *
     * @var array
     */
    private $fileCoverage = [];

    /**
     * Create a new Coverage Processor for the specified directory
     *
     * @param string $resourceDir
     */
    public function __construct(string $resourceDir)
    {
        $this->resourceDir = $resourceDir;
    }

    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param string[] $coveredIds
     *
     * @return void
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
     *
     * @return void
     */
    public function write(string $fileName)
    {
        file_put_contents(
            $fileName,
            // str_replace here is to convert empty arrays into empty JS objects, which is expected by
            // codecov.io. Owner of the service said he was going to patch it, haven't tested since
            // Note: Can't use JSON_FORCE_OBJECT here as we **do** want arrays for the 'b' structure
            // which FORCE_OBJECT turns into objects, breaking at least the Istanbul coverage reporter
            str_replace('[]', '{}', json_encode($this->coverage, JSON_UNESCAPED_SLASHES))
        );
    }

    /**
     * Stores passed in pattern ids, grouping them by file first
     *
     * @param string[] $coveredIds
     *
     * @return void
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
    public function getCoveredPatternIds() : array
    {
        return $this->coveredIds;
    }

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param string   $file
     * @param string   $contents
     * @param string[] $coveredIds
     *
     * @return array
     */
    public function processFile(string $file, string $contents, array $coveredIds) : array
    {
        // These keynames are expected by Istanbul compatible coverage reporters
        // the format is outlined here: https://github.com/gotwarlost/istanbul/blob/master/coverage.json.md
        $this->fileCoverage = [
            // path to file this coverage information is for (i.e. resources/user-agents/browsers/chrome/chrome.json)
            'path' => $file,
            // location information for the statements that should be covered
            'statementMap' => [],
            // location information for the functions that should be covered
            'fnMap' => [],
            // location information for the different branch structures that should be covered
            'branchMap' => [],
            // coverage counts for the statements from statementMap
            's' => [],
            // coverage counts for the branches from branchMap (in array form)
            'b' => [],
            // coverage counts for the functions from fnMap
            'f' => [],
        ];

        $this->fileLines      = explode("\n", $contents);
        $this->fileCoveredIds = $coveredIds;

        $lexer = new Lexer();
        $lexer->setInput($contents);

        $this->handleJsonRoot($lexer);

        // This re-indexes the arrays to be 1 based instead of 0, which will make them be JSON objects rather
        // than arrays, which is how they're expected to be in the coverage JSON file
        $this->fileCoverage['fnMap']        = array_filter(array_merge([0], $this->fileCoverage['fnMap']));
        $this->fileCoverage['statementMap'] = array_filter(array_merge([0], $this->fileCoverage['statementMap']));
        $this->fileCoverage['branchMap']    = array_filter(array_merge([0], $this->fileCoverage['branchMap']));

        // Can't use the same method for the b/s/f sections since they can (and should) contain a 0 value, which
        // array_filter would remove
        array_unshift($this->fileCoverage['b'], '');
        unset($this->fileCoverage['b'][0]);
        array_unshift($this->fileCoverage['f'], '');
        unset($this->fileCoverage['f'][0]);
        array_unshift($this->fileCoverage['s'], '');
        unset($this->fileCoverage['s'][0]);

        return $this->fileCoverage;
    }

    /**
     * Builds the location object for the current position in the JSON file
     *
     * @param Lexer  $lexer
     * @param bool   $end
     * @param string $content
     *
     * @return array
     */
    private function getLocationCoordinates(Lexer $lexer, bool $end = false, string $content = '') : array
    {
        $lineNumber  = $lexer->yylineno;
        $lineContent = $this->fileLines[$lineNumber];

        if ($content === '') {
            $content = $lexer->yytext;
        }

        $position = strpos($lineContent, $content);

        if ($end === true) {
            $position += strlen($content);
        }

        return ['line' => $lineNumber + 1, 'column' => $position];
    }

    /**
     * JSON file processing entry point
     *
     * Hands execution off to applicable method when certain tokens are encountered
     * (in this case, Division is the only one), returns to caller when EOF is reached
     *
     * @param Lexer $lexer
     *
     * @return void
     */
    private function handleJsonRoot(Lexer $lexer)
    {
        do {
            $code = $lexer->lex();

            if ($code === self::JSON_OBJECT_START) {
                $code = $this->handleJsonDivision($lexer);
            }
        } while ($code !== self::JSON_EOF);
    }

    /**
     * Processes the Division block (which is the root JSON object essentially)
     *
     * Lexes the main division object and hands execution off to relevant methods for further
     * processing. Returns the next token code returned from the lexer.
     *
     * @param Lexer $lexer
     *
     * @return int
     */
    private function handleJsonDivision(Lexer $lexer) : int
    {
        $enterUaGroup = false;

        do {
            $code = $lexer->lex();

            if ($code === self::JSON_STRING && $lexer->yytext === 'userAgents') {
                $enterUaGroup = true;
            } elseif ($code === self::JSON_ARRAY_START && $enterUaGroup === true) {
                $code         = $this->handleUseragentGroup($lexer);
                $enterUaGroup = false;
            } elseif ($code === self::JSON_OBJECT_START) {
                $code = $this->ignoreObjectBlock($lexer);
            }
        } while ($code !== self::JSON_OBJECT_END);

        return $lexer->lex();
    }

    /**
     * Processes the userAgents array
     *
     * @param Lexer $lexer
     *
     * @return int
     */
    private function handleUseragentGroup(Lexer $lexer) : int
    {
        $useragentPosition = 0;

        do {
            $code = $lexer->lex();

            if ($code === self::JSON_OBJECT_START) {
                $code = $this->handleUseragentBlock($lexer, $useragentPosition);
                ++$useragentPosition;
            }
        } while ($code !== self::JSON_ARRAY_END);

        return $lexer->lex();
    }

    /**
     * Processes each userAgent object in the userAgents array
     *
     * @param Lexer $lexer
     * @param int   $useragentPosition
     *
     * @return int
     */
    private function handleUseragentBlock(Lexer $lexer, int $useragentPosition) : int
    {
        $enterChildGroup = false;

        do {
            $code = $lexer->lex();

            if ($code === self::JSON_STRING && $lexer->yytext === 'children') {
                $enterChildGroup = true;
            } elseif ($code === self::JSON_ARRAY_START && $enterChildGroup === true) {
                $code            = $this->handleChildrenGroup($lexer, $useragentPosition);
                $enterChildGroup = false;
            } elseif ($code === self::JSON_OBJECT_START) {
                $code = $this->ignoreObjectBlock($lexer);
            }
        } while ($code !== self::JSON_OBJECT_END);

        return $lexer->lex();
    }

    /**
     * Processes the children array of a userAgent object
     *
     * @param Lexer $lexer
     * @param int   $useragentPosition
     *
     * @return int
     */
    private function handleChildrenGroup(Lexer $lexer, int $useragentPosition) : int
    {
        $childPosition = 0;

        do {
            $code = $lexer->lex();

            if ($code === self::JSON_OBJECT_START) {
                $code = $this->handleChildBlock($lexer, $useragentPosition, $childPosition);
                ++$childPosition;
            }
        } while ($code !== self::JSON_ARRAY_END);

        return $lexer->lex();
    }

    /**
     * Processes each child object in the children array
     *
     * @param Lexer $lexer
     * @param int   $useragentPosition
     * @param int   $childPosition
     *
     * @return int
     */
    private function handleChildBlock(Lexer $lexer, int $useragentPosition, int $childPosition) : int
    {
        $enterPlatforms = false;
        $enterDevices   = false;
        $collectMatch   = false;

        $functionStart       = $this->getLocationCoordinates($lexer);
        $functionDeclaration = [];

        do {
            $code = $lexer->lex();

            switch ($code) {
                case self::JSON_STRING:
                    if ($lexer->yytext === 'platforms') {
                        $enterPlatforms = true;
                    } elseif ($lexer->yytext === 'devices') {
                        $enterDevices = true;
                    } elseif ($lexer->yytext === 'match') {
                        $collectMatch = true;
                    } elseif ($collectMatch === true) {
                        $collectMatch        = false;
                        $match               = $lexer->yytext;
                        $functionDeclaration = [
                            'start' => $this->getLocationCoordinates($lexer, false, '"' . $match . '"'),
                            'end' => $this->getLocationCoordinates($lexer, true, '"' . $match . '"'),
                        ];
                    }
                    break;
                case self::JSON_OBJECT_START:
                    if ($enterDevices === true) {
                        $code         = $this->handleDeviceBlock($lexer, $useragentPosition, $childPosition);
                        $enterDevices = false;
                    } else {
                        $code = $this->ignoreObjectBlock($lexer);
                    }
                    break;
                case self::JSON_ARRAY_START:
                    if ($enterPlatforms === true) {
                        $code           = $this->handlePlatformBlock($lexer, $useragentPosition, $childPosition);
                        $enterPlatforms = false;
                    }
                    break;
            }
        } while ($code !== self::JSON_OBJECT_END);

        $functionEnd = $this->getLocationCoordinates($lexer, true);

        $functionCoverage = $this->getCoverageCount(
            sprintf('u%d::c%d::d::p', $useragentPosition, $childPosition),
            $this->fileCoveredIds
        );

        $this->collectFunction($functionStart, $functionEnd, $functionDeclaration, $functionCoverage);

        return $lexer->lex();
    }

    /**
     * Process the "devices" has in the child object
     *
     * @param Lexer $lexer
     * @param int   $useragentPosition
     * @param int   $childPosition
     *
     * @return int
     */
    private function handleDeviceBlock(Lexer $lexer, int $useragentPosition, int $childPosition) : int
    {
        $capturedKey = false;
        $sawColon    = false;

        $branchStart     = $this->getLocationCoordinates($lexer);
        $branchLocations = [];
        $branchCoverage  = [];

        do {
            $code = $lexer->lex();

            if ($code === self::JSON_STRING && $capturedKey === false) {
                $branchLocations[] = [
                    'start' => $this->getLocationCoordinates($lexer, false, '"' . $lexer->yytext . '"'),
                    'end' => $this->getLocationCoordinates($lexer, true, '"' . $lexer->yytext . '"'),
                ];
                $branchCoverage[] = $this->getCoverageCount(
                    sprintf('u%d::c%d::d%s::p', $useragentPosition, $childPosition, $lexer->yytext),
                    $this->fileCoveredIds
                );
                $capturedKey = true;
            } elseif ($code === self::JSON_COLON) {
                $sawColon = true;
            } elseif ($code === self::JSON_STRING && $sawColon === true) {
                $capturedKey = false;
            }
        } while ($code !== self::JSON_OBJECT_END);

        $branchEnd = $this->getLocationCoordinates($lexer, true);

        $this->collectBranch($branchStart, $branchEnd, $branchLocations, $branchCoverage);

        return $lexer->lex();
    }

    /**
     * Processes the "platforms" hash in the child object
     *
     * @param Lexer $lexer
     * @param int   $useragentPosition
     * @param int   $childPosition
     *
     * @return int
     */
    private function handlePlatformBlock(Lexer $lexer, int $useragentPosition, int $childPosition) : int
    {
        $branchStart     = $this->getLocationCoordinates($lexer);
        $branchLocations = [];
        $branchCoverage  = [];

        do {
            $code = $lexer->lex();

            if ($code === self::JSON_STRING) {
                $branchLocations[] = [
                    'start' => $this->getLocationCoordinates($lexer, false, '"' . $lexer->yytext . '"'),
                    'end' => $this->getLocationCoordinates($lexer, true, '"' . $lexer->yytext . '"'),
                ];
                $branchCoverage[] = $this->getCoverageCount(
                    sprintf('u%d::c%d::d::p%s', $useragentPosition, $childPosition, $lexer->yytext),
                    $this->fileCoveredIds
                );
            }
        } while ($code !== self::JSON_ARRAY_END);

        $branchEnd = $this->getLocationCoordinates($lexer, true);

        $this->collectBranch($branchStart, $branchEnd, $branchLocations, $branchCoverage);

        return $lexer->lex();
    }

    /**
     * Processes JSON object block that isn't needed for coverage data
     *
     * @param Lexer $lexer
     *
     * @return int
     */
    private function ignoreObjectBlock(Lexer $lexer) : int
    {
        do {
            $code = $lexer->lex();

            // recursively ignore nested objects
            if ($code === self::JSON_OBJECT_START) {
                $this->ignoreObjectBlock($lexer);
            }
        } while ($code !== self::JSON_OBJECT_END);

        return $lexer->lex();
    }

    /**
     * Collects and stores a function's location information as well as any passed in coverage counts
     *
     * @param array $start
     * @param array $end
     * @param array $declaration
     * @param int   $coverage
     *
     * @return void
     */
    private function collectFunction(array $start, array $end, array $declaration, int $coverage = 0)
    {
        $this->fileCoverage['fnMap'][] = [
            'name' => '(anonymous_' . $this->funcCount . ')',
            'decl' => $declaration,
            'loc' => ['start' => $start, 'end' => $end],
        ];

        $this->fileCoverage['f'][] = $coverage;

        // Collect statements as well, one for entire function, one just for function declaration
        $this->collectStatement($start, $end, $coverage);
        $this->collectStatement($declaration['start'], $declaration['end'], $coverage);

        ++$this->funcCount;
    }

    /**
     * Collects and stores a branch's location information as well as any coverage counts
     *
     * @param array $start
     * @param array $end
     * @param array $locations
     * @param int[] $coverage
     *
     * @return void
     */
    private function collectBranch(array $start, array $end, array $locations, array $coverage = [])
    {
        $this->fileCoverage['branchMap'][] = [
            'type' => 'switch',
            'locations' => $locations,
            'loc' => ['start' => $start, 'end' => $end],
        ];

        $this->fileCoverage['b'][] = $coverage;

        // Collect statements as well (entire branch is a statement, each location is a statement)
        $this->collectStatement($start, $end, array_sum($coverage));

        for ($i = 0, $count = count($locations); $i < $count; ++$i) {
            $this->collectStatement($locations[$i]['start'], $locations[$i]['end'], $coverage[$i]);
        }
    }

    /**
     * Collects and stores a statement's location information as well as any coverage counts
     *
     * @param array $start
     * @param array $end
     * @param int   $coverage
     *
     * @return void
     */
    private function collectStatement(array $start, array $end, int $coverage = 0)
    {
        $this->fileCoverage['statementMap'][] = [
            'start' => $start,
            'end' => $end,
        ];

        $this->fileCoverage['s'][] = $coverage;
    }

    /**
     * Groups pattern ids by their filename prefix
     *
     * @param string[] $ids
     *
     * @return array
     */
    private function groupIdsByFile(array $ids) : array
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
     * Counts number of times given generated pattern id is covered by patterns ids collected during tests
     *
     * @param string   $id
     * @param string[] $covered
     *
     * @return int
     */
    private function getCoverageCount(string $id, array $covered) : int
    {
        $id                  = str_replace('\/', '/', $id);
        list($u, $c, $d, $p) = explode('::', $id);

        $u = preg_quote(substr($u, 1), '/');
        $c = preg_quote(substr($c, 1), '/');
        $p = preg_quote(substr($p, 1), '/');
        $d = preg_quote(substr($d, 1), '/');

        $count = 0;

        if (strlen($p) === 0) {
            $p = '.*?';
        }
        if (strlen($d) === 0) {
            $d = '.*?';
        }

        $regex = sprintf('/^u%d::c%d::d%s::p%s$/', $u, $c, $d, $p);

        foreach ($covered as $patternId) {
            if (preg_match($regex, $patternId)) {
                ++$count;
            }
        }

        return $count;
    }
}

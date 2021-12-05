<?php

declare(strict_types=1);

namespace Browscap\Coverage;

use JsonException;
use RuntimeException;
use Seld\JsonLint\Lexer;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_filter;
use function array_merge;
use function array_sum;
use function array_unshift;
use function assert;
use function count;
use function explode;
use function file_put_contents;
use function is_array;
use function is_string;
use function json_encode;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function preg_match;
use function preg_quote;
use function sprintf;
use function str_replace;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

/**
 * This class creates coverage data for the json files in the resources directory
 */
final class Processor implements ProcessorInterface
{
    /**
     * The codes representing different JSON elements
     *
     * These come from the Seld\JsonLint\JsonParser class. The values are returned by the lexer when
     * the lex() method is called.
     */
    private const JSON_OBJECT_START = 17;
    private const JSON_OBJECT_END   = 18;
    private const JSON_ARRAY_START  = 23;
    private const JSON_ARRAY_END    = 24;
    private const JSON_EOF          = 1;
    private const JSON_STRING       = 4;
    private const JSON_COLON        = 21;

    private string $resourceDir;

    /**
     * The pattern ids encountered during the test run. These are compared against the JSON file structure to determine
     * if the statement/function/branch is covered.
     *
     * @var array<array<string>>
     */
    private array $coveredIds = [];

    /**
     * This is the full coverage array that gets output in the write method.  For each file an entry in the array
     * is added.  Each entry contains the elements required for Istanbul compatible coverage reporters.
     *
     * @var mixed[]
     */
    private array $coverage = [];

    /**
     * An incrementing integer for every "function" (child match) encountered in all processed files. This is used
     * to name the anonymous functions in the coverage report.
     */
    private int $funcCount = 0;

    /**
     * A storage variable for the lines of a file while processing that file, used for determining column
     * position of a statement/function/branch
     *
     * @var array<string>
     */
    private array $fileLines = [];

    /**
     * A storage variable of the pattern ids covered by tests for a specific file (set when processing of that
     * file begins)
     *
     * @var array<string>
     */
    private array $fileCoveredIds = [];

    /**
     * location information for the statements that should be covered
     *
     * @var array<string, array<int, string|int>>
     */
    private array $statementCoverage = [];

    /**
     * location information for the functions that should be covered
     *
     * @var array<string, array<int, string|int>>
     */
    private array $functionCoverage = [];

    /**
     * location information for the different branch structures that should be covered
     *
     * @var array<string, array<int, string|int>>
     */
    private array $branchCoverage = [];

    /**
     * Create a new Coverage Processor for the specified directory
     *
     * @throws void
     */
    public function __construct(string $resourceDir)
    {
        $this->resourceDir = $resourceDir;
    }

    /**
     * Process the directory of JSON files using the collected pattern ids
     *
     * @param array<string> $coveredIds
     *
     * @throws RuntimeException
     * @throws DirectoryNotFoundException
     */
    public function process(array $coveredIds): void
    {
        $this->setCoveredPatternIds($coveredIds);

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();
        $finder->in($this->resourceDir);

        foreach ($finder as $file) {
            assert($file instanceof SplFileInfo);

            $patternFileName = mb_substr($file->getPathname(), (int) mb_strpos($file->getPathname(), 'resources/'));
            assert(is_string($patternFileName));

            if (! isset($this->coveredIds[$patternFileName])) {
                $this->coveredIds[$patternFileName] = [];
            }

            $this->coverage[$patternFileName] = $this->processFile(
                $patternFileName,
                $file->getContents(),
                $this->coveredIds[$patternFileName]
            );
        }
    }

    /**
     * Write the coverage data in JSON format to specified filename
     *
     * @throws JsonException
     */
    public function write(string $fileName): void
    {
        $content = json_encode($this->coverage, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        file_put_contents(
            $fileName,
            // str_replace here is to convert empty arrays into empty JS objects, which is expected by
            // codecov.io. Owner of the service said he was going to patch it, haven't tested since
            // Note: Can't use JSON_FORCE_OBJECT here as we **do** want arrays for the 'b' structure
            // which FORCE_OBJECT turns into objects, breaking at least the Istanbul coverage reporter
            str_replace('[]', '{}', $content)
        );
    }

    /**
     * Stores passed in pattern ids, grouping them by file first
     *
     * @param array<string> $coveredIds
     *
     * @throws void
     */
    public function setCoveredPatternIds(array $coveredIds): void
    {
        $this->coveredIds = $this->groupIdsByFile($coveredIds);
    }

    /**
     * Returns the grouped pattern ids previously set
     *
     * @return array<array<string>>
     *
     * @throws void
     */
    public function getCoveredPatternIds(): array
    {
        return $this->coveredIds;
    }

    /**
     * Process an individual file for coverage data using covered ids
     *
     * @param array<int|string, string> $coveredIds
     *
     * @return array<string, array<int, string|int>|string>
     *
     * @throws void
     */
    public function processFile(string $file, string $contents, array $coveredIds): array
    {
        $this->functionCoverage = [
            // location information for the functions that should be covered
            'fnMap' => [],
            // coverage counts for the functions from fnMap
            'f' => [],
        ];

        $this->statementCoverage = [
            // location information for the statements that should be covered
            'statementMap' => [],
            // coverage counts for the statements from statementMap
            's' => [],
        ];

        $this->branchCoverage = [
            // location information for the different branch structures that should be covered
            'branchMap' => [],
            // coverage counts for the branches from branchMap (in array form)
            'b' => [],
        ];

        $this->fileLines      = explode("\n", $contents);
        $this->fileCoveredIds = $coveredIds;

        $lexer = new Lexer();
        $lexer->setInput($contents);

        $this->handleJsonRoot($lexer);

        // This re-indexes the arrays to be 1 based instead of 0, which will make them be JSON objects rather
        // than arrays, which is how they're expected to be in the coverage JSON file
        $this->functionCoverage['fnMap']         = array_filter(array_merge([0], $this->functionCoverage['fnMap']));
        $this->statementCoverage['statementMap'] = array_filter(array_merge([0], $this->statementCoverage['statementMap']));
        $this->branchCoverage['branchMap']       = array_filter(array_merge([0], $this->branchCoverage['branchMap']));

        // Can't use the same method for the b/s/f sections since they can (and should) contain a 0 value, which
        // array_filter would remove
        array_unshift($this->branchCoverage['b'], '');
        unset($this->branchCoverage['b'][0]);
        array_unshift($this->functionCoverage['f'], '');
        unset($this->functionCoverage['f'][0]);
        array_unshift($this->statementCoverage['s'], '');
        unset($this->statementCoverage['s'][0]);

        // These keynames are expected by Istanbul compatible coverage reporters
        // the format is outlined here: https://github.com/gotwarlost/istanbul/blob/master/coverage.json.md
        return [
            // path to file this coverage information is for (i.e. resources/user-agents/browsers/chrome/chrome.json)
            'path' => $file,
            // location information for the statements that should be covered
            'statementMap' => $this->statementCoverage['statementMap'],
            // location information for the functions that should be covered
            'fnMap' => $this->functionCoverage['fnMap'],
            // location information for the different branch structures that should be covered
            'branchMap' => $this->branchCoverage['branchMap'],
            // coverage counts for the statements from statementMap
            's' => $this->statementCoverage['s'],
            // coverage counts for the branches from branchMap (in array form)
            'b' => $this->branchCoverage['b'],
            // coverage counts for the functions from fnMap
            'f' => $this->functionCoverage['f'],
        ];
    }

    /**
     * Builds the location object for the current position in the JSON file
     *
     * @return array<string, float|int|false>
     *
     * @throws void
     */
    private function getLocationCoordinates(Lexer $lexer, bool $end = false, string $content = ''): array
    {
        $lineNumber  = $lexer->yylineno;
        $lineContent = $this->fileLines[$lineNumber];

        if ($content === '') {
            $content = $lexer->yytext;
        }

        $position = mb_strpos($lineContent, $content);

        if ($end === true) {
            $position += mb_strlen($content);
        }

        return ['line' => $lineNumber + 1, 'column' => $position];
    }

    /**
     * JSON file processing entry point
     *
     * Hands execution off to applicable method when certain tokens are encountered
     * (in this case, Division is the only one), returns to caller when EOF is reached
     *
     * @throws void
     */
    private function handleJsonRoot(Lexer $lexer): void
    {
        do {
            $code = $lexer->lex();

            if ($code !== self::JSON_OBJECT_START) {
                continue;
            }

            $code = $this->handleJsonDivision($lexer);
        } while ($code !== self::JSON_EOF);
    }

    /**
     * Processes the Division block (which is the root JSON object essentially)
     *
     * Lexes the main division object and hands execution off to relevant methods for further
     * processing. Returns the next token code returned from the lexer.
     *
     * @throws void
     */
    private function handleJsonDivision(Lexer $lexer): int
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
     * @throws void
     */
    private function handleUseragentGroup(Lexer $lexer): int
    {
        $useragentPosition = 0;

        do {
            $code = $lexer->lex();

            if ($code !== self::JSON_OBJECT_START) {
                continue;
            }

            $code = $this->handleUseragentBlock($lexer, $useragentPosition);
            ++$useragentPosition;
        } while ($code !== self::JSON_ARRAY_END);

        return $lexer->lex();
    }

    /**
     * Processes each userAgent object in the userAgents array
     *
     * @throws void
     */
    private function handleUseragentBlock(Lexer $lexer, int $useragentPosition): int
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
     * @throws void
     */
    private function handleChildrenGroup(Lexer $lexer, int $useragentPosition): int
    {
        $childPosition = 0;

        do {
            $code = $lexer->lex();

            if ($code !== self::JSON_OBJECT_START) {
                continue;
            }

            $code = $this->handleChildBlock($lexer, $useragentPosition, $childPosition);
            ++$childPosition;
        } while ($code !== self::JSON_ARRAY_END);

        return $lexer->lex();
    }

    /**
     * Processes each child object in the children array
     *
     * @throws void
     */
    private function handleChildBlock(Lexer $lexer, int $useragentPosition, int $childPosition): int
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
     * @throws void
     */
    private function handleDeviceBlock(Lexer $lexer, int $useragentPosition, int $childPosition): int
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
                $branchCoverage[]  = $this->getCoverageCount(
                    sprintf('u%d::c%d::d%s::p', $useragentPosition, $childPosition, $lexer->yytext),
                    $this->fileCoveredIds
                );
                $capturedKey       = true;
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
     * @throws void
     */
    private function handlePlatformBlock(Lexer $lexer, int $useragentPosition, int $childPosition): int
    {
        $branchStart     = $this->getLocationCoordinates($lexer);
        $branchLocations = [];
        $branchCoverage  = [];

        do {
            $code = $lexer->lex();

            if ($code !== self::JSON_STRING) {
                continue;
            }

            $branchLocations[] = [
                'start' => $this->getLocationCoordinates($lexer, false, '"' . $lexer->yytext . '"'),
                'end' => $this->getLocationCoordinates($lexer, true, '"' . $lexer->yytext . '"'),
            ];
            $branchCoverage[]  = $this->getCoverageCount(
                sprintf('u%d::c%d::d::p%s', $useragentPosition, $childPosition, $lexer->yytext),
                $this->fileCoveredIds
            );
        } while ($code !== self::JSON_ARRAY_END);

        $branchEnd = $this->getLocationCoordinates($lexer, true);

        $this->collectBranch($branchStart, $branchEnd, $branchLocations, $branchCoverage);

        return $lexer->lex();
    }

    /**
     * Processes JSON object block that isn't needed for coverage data
     *
     * @throws void
     */
    private function ignoreObjectBlock(Lexer $lexer): int
    {
        do {
            $code = $lexer->lex();

            // recursively ignore nested objects
            if ($code !== self::JSON_OBJECT_START) {
                continue;
            }

            $this->ignoreObjectBlock($lexer);
        } while ($code !== self::JSON_OBJECT_END);

        return $lexer->lex();
    }

    /**
     * Collects and stores a function's location information as well as any passed in coverage counts
     *
     * @param mixed[]   $start
     * @param mixed[]   $end
     * @param mixed[][] $declaration
     *
     * @throws void
     */
    private function collectFunction(array $start, array $end, array $declaration, int $coverage = 0): void
    {
        $this->functionCoverage['fnMap'][] = [
            'name' => '(anonymous_' . $this->funcCount . ')',
            'decl' => $declaration,
            'loc' => ['start' => $start, 'end' => $end],
        ];

        $this->functionCoverage['f'][] = $coverage;

        // Collect statements as well, one for entire function, one just for function declaration
        $this->collectStatement($start, $end, $coverage);
        $this->collectStatement($declaration['start'], $declaration['end'], $coverage);

        ++$this->funcCount;
    }

    /**
     * Collects and stores a branch's location information as well as any coverage counts
     *
     * @param mixed[]    $start
     * @param mixed[]    $end
     * @param mixed[][]  $locations
     * @param array<int> $coverage
     *
     * @throws void
     */
    private function collectBranch(array $start, array $end, array $locations, array $coverage = []): void
    {
        $this->branchCoverage['branchMap'][] = [
            'type' => 'switch',
            'locations' => $locations,
            'loc' => ['start' => $start, 'end' => $end],
        ];

        $this->branchCoverage['b'][] = $coverage;

        // Collect statements as well (entire branch is a statement, each location is a statement)
        $this->collectStatement($start, $end, (int) array_sum($coverage));

        for ($i = 0, $count = count($locations); $i < $count; ++$i) {
            assert(is_array($locations[$i]['start']));
            assert(is_array($locations[$i]['end']));
            $this->collectStatement($locations[$i]['start'], $locations[$i]['end'], $coverage[$i]);
        }
    }

    /**
     * Collects and stores a statement's location information as well as any coverage counts
     *
     * @param mixed[] $start
     * @param mixed[] $end
     *
     * @throws void
     */
    private function collectStatement(array $start, array $end, int $coverage = 0): void
    {
        $this->statementCoverage['statementMap'][] = [
            'start' => $start,
            'end' => $end,
        ];

        $this->statementCoverage['s'][] = $coverage;
    }

    /**
     * Groups pattern ids by their filename prefix
     *
     * @param array<string> $ids
     *
     * @return array<array<string>>
     *
     * @throws void
     */
    private function groupIdsByFile(array $ids): array
    {
        $covered = [];

        foreach ($ids as $id) {
            $pos = mb_strpos($id, '::');

            if ($pos === false) {
                continue;
            }

            $file = mb_substr($id, 0, $pos);

            if (! isset($covered[$file])) {
                $covered[$file] = [];
            }

            $covered[$file][] = mb_substr($id, mb_strpos($id, '::') + 2);
        }

        return $covered;
    }

    /**
     * Counts number of times given generated pattern id is covered by patterns ids collected during tests
     *
     * @param array<string> $covered
     *
     * @throws void
     */
    private function getCoverageCount(string $id, array $covered): int
    {
        $id              = str_replace('\/', '/', $id);
        [$u, $c, $d, $p] = explode('::', $id);

        $u = preg_quote(mb_substr($u, 1), '/');
        $c = preg_quote(mb_substr($c, 1), '/');
        $p = preg_quote(mb_substr($p, 1), '/');
        $d = preg_quote(mb_substr($d, 1), '/');

        $count = 0;

        if (mb_strlen($p) === 0) {
            $p = '.*?';
        }

        if (mb_strlen($d) === 0) {
            $d = '.*?';
        }

        $regex = sprintf('/^u%d::c%d::d%s::p%s$/', $u, $c, $d, $p);

        foreach ($covered as $patternId) {
            if (! preg_match($regex, $patternId)) {
                continue;
            }

            ++$count;
        }

        return $count;
    }
}

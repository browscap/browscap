<?php

declare(strict_types=1);

namespace BrowscapTest\Coverage;

use Browscap\Coverage\Processor;
use PHPUnit\Framework\TestCase;

use function array_sum;
use function assert;
use function file_get_contents;
use function is_string;

final class ProcessorTest extends TestCase
{
    /** @var Processor */
    private $object;

    /** @var string */
    private $resourceDir = __DIR__ . '/../../fixtures/coverage/';

    /**
     * Run before each test, creates a new Processor object
     */
    protected function setUp(): void
    {
        $this->object = new Processor($this->resourceDir);
    }

    /**
     * Data provider for the testJsonStructure test
     *
     * @return array<int, array<int, array<string, int>|string>>
     */
    public function jsonStructureProvider(): array
    {
        return [
            ['test1.json', ['statementCount' => 5, 'branchCount' => 1, 'functionCount' => 1]],
            ['test2.json', ['statementCount' => 15, 'branchCount' => 3, 'functionCount' => 3]],
        ];
    }

    /**
     * This test verifies that the different structures were extracted from the test JSON files
     *
     * @param array<string, int> $expected
     *
     * @dataProvider jsonStructureProvider
     */
    public function testJsonStructure(string $fileName, array $expected): void
    {
        $content = file_get_contents($this->resourceDir . $fileName);
        assert(is_string($content));
        $coverage = $this->object->processFile(
            $fileName,
            $content,
            []
        );

        self::assertCount($expected['statementCount'], $coverage['statementMap']);
        self::assertCount($expected['statementCount'], $coverage['s']);

        self::assertCount($expected['branchCount'], $coverage['branchMap']);
        self::assertCount($expected['branchCount'], $coverage['b']);

        self::assertCount($expected['functionCount'], $coverage['fnMap']);
        self::assertCount($expected['functionCount'], $coverage['f']);
    }

    /**
     * Data provider for the testCoverage test
     *
     * @return array<string, array<int, array<int|string, int|string>|string>>
     */
    public function coverageProvider(): array
    {
        return [
            'test1-no-coverage' => [
                'test1.json',
                [],
                [
                    's' => 0,
                    'b' => 0,
                    'f' => 0,
                ],
            ],
            'test1-partial-coverage' => [
                'test1.json',
                ['u0::c0::d::pPlatform_1'],
                [
                    's' => 4,
                    'b' => 1,
                    'f' => 1,
                ],
            ],
            'test1-full-coverage' => [
                'test1.json',
                ['u0::c0::d::pPlatform_1', 'u0::c0::d::pPlatform_2'],
                [
                    's' => 8,
                    'b' => 2,
                    'f' => 2,
                ],
            ],
            'test1-full-coverage-double' => [
                'test1.json',
                ['u0::c0::d::pPlatform_1', 'u0::c0::d::pPlatform_2', 'u0::c0::d::pPlatform_2'],
                [
                    's' => 12,
                    'b' => 3,
                    'f' => 3,
                ],
            ],
            'test2-no-coverage' => [
                'test2.json',
                [],
                [
                    's' => 0,
                    'b' => 0,
                    'f' => 0,
                ],
            ],
            'test2-partial-coverage' => [
                'test2.json',
                ['u0::c0::d::pPlatform_1'],
                [
                    's' => 4,
                    'b' => 1,
                    'f' => 1,
                ],
            ],
            'test2-full-coverage' => [
                'test2.json',
                [
                    'u0::c0::d::pPlatform_1',
                    'u0::c0::d::pPlatform_2',
                    'u0::c1::ddevice1::pPlatform_1',
                    'u0::c1::ddevice2::pPlatform_2',
                    'u1::c0::d::p',
                ],
                [
                    's' => 22,
                    'b' => 6,
                    'f' => 5,
                ],
            ],
        ];
    }

    /**
     * Tests that the amount of covered statements/branches/functions matches expected
     *
     * @param array<int|string, string> $coveredIds
     * @param array<string, int>        $expected
     *
     * @dataProvider coverageProvider
     */
    public function testCoverage(string $fileName, array $coveredIds, array $expected): void
    {
        $content = file_get_contents($this->resourceDir . $fileName);
        assert(is_string($content));
        $coverage = $this->object->processFile(
            $fileName,
            $content,
            $coveredIds
        );

        self::assertSame($expected['s'], array_sum($coverage['s']));
        self::assertSame($expected['f'], array_sum($coverage['f']));

        $branchSum = 0;

        foreach ($coverage['b'] as $branch) {
            $branchSum += array_sum($branch);
        }

        self::assertSame($expected['b'], $branchSum);
    }

    /**
     * Tests that the collected patterns ids are grouped by filename prefix
     */
    public function testPatternIdGrouping(): void
    {
        $patternIds = [
            'abc.json::u0::c0::d::p',
            'abc.json::u0::c1::d::p',
            'def.json::u0::c1::d::p',
            'ghi.json::u0::c1::d::p',
        ];

        $this->object->setCoveredPatternIds($patternIds);

        self::assertSame(
            [
                'abc.json' => ['u0::c0::d::p', 'u0::c1::d::p'],
                'def.json' => ['u0::c1::d::p'],
                'ghi.json' => ['u0::c1::d::p'],
            ],
            $this->object->getCoveredPatternIds()
        );
    }
}

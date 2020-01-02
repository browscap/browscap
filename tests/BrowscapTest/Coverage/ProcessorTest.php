<?php
declare(strict_types = 1);
namespace BrowscapTest\Coverage;

use Browscap\Coverage\Processor;
use PHPUnit\Framework\TestCase;

final class ProcessorTest extends TestCase
{
    /**
     * @var \Browscap\Coverage\Processor
     */
    private $object;

    /**
     * @var string
     */
    private $resourceDir = __DIR__ . '/../../fixtures/coverage/';

    /**
     * Run before each test, creates a new Processor object
     */
    protected function setUp() : void
    {
        $this->object = new Processor($this->resourceDir);
    }

    /**
     * Data provider for the testJsonStructure test
     */
    public function jsonStructureProvider() : array
    {
        return [
            ['test1.json', ['statementCount' => 5, 'branchCount' => 1, 'functionCount' => 1]],
            ['test2.json', ['statementCount' => 15, 'branchCount' => 3, 'functionCount' => 3]],
        ];
    }

    /**
     * This test verifies that the different structures were extracted from the test JSON files
     *
     * @dataProvider jsonStructureProvider
     *
     * @param string $fileName
     * @param array  $expected
     */
    public function testJsonStructure(string $fileName, array $expected) : void
    {
        /** @var string $content */
        $content  = file_get_contents($this->resourceDir . $fileName);
        $coverage = $this->object->processFile(
            $fileName,
            $content,
            []
        );

        self::assertSame($expected['statementCount'], count($coverage['statementMap']));
        self::assertSame($expected['statementCount'], count($coverage['s']));

        self::assertSame($expected['branchCount'], count($coverage['branchMap']));
        self::assertSame($expected['branchCount'], count($coverage['b']));

        self::assertSame($expected['functionCount'], count($coverage['fnMap']));
        self::assertSame($expected['functionCount'], count($coverage['f']));
    }

    /**
     * Data provider for the testCoverage test
     */
    public function coverageProvider() : array
    {
        return [
            'test1-no-coverage' => ['test1.json', [], [
                's' => 0,
                'b' => 0,
                'f' => 0,
            ]],
            'test1-partial-coverage' => ['test1.json', ['u0::c0::d::pPlatform_1'], [
                's' => 4,
                'b' => 1,
                'f' => 1,
            ]],
            'test1-full-coverage' => ['test1.json', ['u0::c0::d::pPlatform_1', 'u0::c0::d::pPlatform_2'], [
                's' => 8,
                'b' => 2,
                'f' => 2,
            ]],
            'test1-full-coverage-double' => [
                'test1.json',
                ['u0::c0::d::pPlatform_1', 'u0::c0::d::pPlatform_2', 'u0::c0::d::pPlatform_2'],
                [
                    's' => 12,
                    'b' => 3,
                    'f' => 3,
                ],
            ],
            'test2-no-coverage' => ['test2.json', [], [
                's' => 0,
                'b' => 0,
                'f' => 0,
            ]],
            'test2-partial-coverage' => ['test2.json', ['u0::c0::d::pPlatform_1'], [
                's' => 4,
                'b' => 1,
                'f' => 1,
            ]],
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
     * @dataProvider coverageProvider
     *
     * @param string   $fileName
     * @param string[] $coveredIds
     * @param array    $expected
     */
    public function testCoverage(string $fileName, array $coveredIds, array $expected) : void
    {
        /** @var string $content */
        $content  = file_get_contents($this->resourceDir . $fileName);
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
    public function testPatternIdGrouping() : void
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

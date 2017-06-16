<?php

declare(strict_types=1);

/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   BrowscapTest
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Coverage;

use Browscap\Coverage\Processor;

/**
 * Class ExpanderTest
 *
 * @category   BrowscapTest
 * @author     Jay Klehr <jay.klehr@gmail.com>
 */
final class ProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Coverage\Processor
     */
    private $object = null;

    /**
     * @var string
     */
    private $resourceDir = __DIR__ . '/../../fixtures/coverage/';

    /**
     * Run before each test, creates a new Processor object
     *
     * @return void
     */
    public function setUp()
    {
        $this->object = new Processor($this->resourceDir);
    }

    /**
     * Data provider for the testJsonStructure test
     *
     * @return array
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
     * @return void
     */
    public function testJsonStructure(string $fileName, array $expected)
    {
        $coverage = $this->object->processFile($fileName, file_get_contents($this->resourceDir . $fileName), []);

        self::assertSame($expected['statementCount'], count($coverage['statementMap']));
        self::assertSame($expected['statementCount'], count($coverage['s']));

        self::assertSame($expected['branchCount'], count($coverage['branchMap']));
        self::assertSame($expected['branchCount'], count($coverage['b']));

        self::assertSame($expected['functionCount'], count($coverage['fnMap']));
        self::assertSame($expected['functionCount'], count($coverage['f']));
    }

    /**
     * Data provider for the testCoverage test
     *
     * @return array
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
     * @param string   $fileName
     * @param string[] $coveredIds
     * @param array    $expected
     *
     * @return void
     */
    public function testCoverage(string $fileName, array $coveredIds, array $expected)
    {
        $coverage = $this->object->processFile(
            $fileName,
            file_get_contents($this->resourceDir . $fileName),
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
     *
     * @return void
     */
    public function testPatternIdGrouping()
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

<?php
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

namespace BrowscapTest\Generator\Helper;

use Browscap\Generator\Helper\BuildHelper;

/**
 * Class BuildGeneratorTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class BuildHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests running a build
     *
     * @group generator
     * @group sourcetest
     */
    public function testRun()
    {
        $logger = $this->createMock(\Monolog\Logger::class);

        $writerCollection = $this->getMockBuilder(\Browscap\Writer\WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                    'fileStart',
                    'renderHeader',
                    'renderAllDivisionsHeader',
                    'renderDivisionFooter',
                    'renderSectionHeader',
                    'renderSectionBody',
                    'fileEnd',
                ])
            ->getMock();

        $writerCollection->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderDivisionFooter')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionBody')
            ->with(self::callback(function (array $props) {
                // Be sure that PatternId key is removed
                return !array_key_exists('PatternId', $props);
            }))
            ->will(self::returnSelf());

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();
        $division
            ->expects(self::exactly(4))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => [
                            'properties' => [
                                'Parent' => 'DefaultProperties',
                                'Browser' => 'xyz',
                                'Version' => '1.0',
                                'MajorVer' => '1',
                                'PatternId' => 'tests/test.json::u0::c0::d::p',
                            ],
                            'userAgent' => 'abc',
                        ],
                    ]
                )
            );
        $division
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue([2]));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions', 'checkProperty'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));
        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));
        $collection
            ->expects(self::once())
            ->method('checkProperty')
            ->will(self::returnValue(true));

        $collectionCreator = $this->getMockBuilder(\Browscap\Helper\CollectionCreator::class)
            ->disableOriginalConstructor()
            ->setMethods(['setLogger', 'getLogger', 'createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('setLogger')
            ->will(self::returnSelf());
        $collectionCreator->expects(self::never())
            ->method('getLogger')
            ->will(self::returnValue($logger));
        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', '.', $logger, $writerCollection, $collectionCreator);
    }

    /**
     * tests running a build with pattern id collection enabled
     *
     * @group generator
     * @group sourcetest
     */
    public function testRunWithPatternIdCollectionEnabled()
    {
        $logger = $this->createMock(\Monolog\Logger::class);

        $writerCollection = $this->getMockBuilder(\Browscap\Writer\WriterCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([
                    'fileStart',
                    'renderHeader',
                    'renderAllDivisionsHeader',
                    'renderDivisionFooter',
                    'renderSectionHeader',
                    'renderSectionBody',
                    'fileEnd',
                ])
            ->getMock();

        $writerCollection->expects(self::once())
            ->method('fileStart')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('fileEnd')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::once())
            ->method('renderAllDivisionsHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderDivisionFooter')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionHeader')
            ->will(self::returnSelf());
        $writerCollection->expects(self::any())
            ->method('renderSectionBody')
            ->with(self::callback(function (array $props) {
                // Be sure that PatternId key is present
                return array_key_exists('PatternId', $props);
            }))
            ->will(self::returnSelf());

        $division = $this->getMockBuilder(\Browscap\Data\Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents', 'getVersions'])
            ->getMock();
        $division
            ->expects(self::exactly(4))
            ->method('getUserAgents')
            ->will(
                self::returnValue(
                    [
                        0 => [
                            'properties' => [
                                'Parent' => 'DefaultProperties',
                                'Browser' => 'xyz',
                                'Version' => '1.0',
                                'MajorVer' => '1',
                                'PatternId' => 'tests/test.json::u0::c0::d::p',
                            ],
                            'userAgent' => 'abc',
                        ],
                    ]
                )
            );
        $division
            ->expects(self::once())
            ->method('getVersions')
            ->will(self::returnValue([2]));

        $collection = $this->getMockBuilder(\Browscap\Data\DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerationDate', 'getDefaultProperties', 'getDefaultBrowser', 'getDivisions', 'checkProperty'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getGenerationDate')
            ->will(self::returnValue(new \DateTime()));
        $collection
            ->expects(self::exactly(2))
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDefaultBrowser')
            ->will(self::returnValue($division));
        $collection
            ->expects(self::once())
            ->method('getDivisions')
            ->will(self::returnValue([$division]));
        $collection
            ->expects(self::once())
            ->method('checkProperty')
            ->will(self::returnValue(true));

        $collectionCreator = $this->getMockBuilder(\Browscap\Helper\CollectionCreator::class)
            ->disableOriginalConstructor()
            ->setMethods(['setLogger', 'getLogger', 'createDataCollection'])
            ->getMock();

        $collectionCreator->expects(self::once())
            ->method('setLogger')
            ->will(self::returnSelf());
        $collectionCreator->expects(self::never())
            ->method('getLogger')
            ->will(self::returnValue($logger));
        $collectionCreator->expects(self::once())
            ->method('createDataCollection')
            ->will(self::returnValue($collection));

        BuildHelper::run('test', '.', $logger, $writerCollection, $collectionCreator, true);
    }
}

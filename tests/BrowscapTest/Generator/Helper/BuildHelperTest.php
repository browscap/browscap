<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @copyright  1998-2014 Browser Capabilities Project
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
class BuildHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests running a build
     *
     * @group generator
     * @group sourcetest
     */
    public function testRun()
    {
        $logger = $this->getMockBuilder(\Monolog\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

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
                                'Parent'   => 'DefaultProperties',
                                'Browser'  => 'xyz',
                                'Version'  => '1.0',
                                'MajorBer' => '1',
                            ],
                            'userAgent'  => 'abc',
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
}

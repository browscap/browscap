<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Factory\DivisionFactory;
use Browscap\Data\Factory\UseragentFactory;
use Browscap\Data\Validator\DivisionData;
use Monolog\Logger;

/**
 * Class DivisionFactoryTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DivisionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\DivisionFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $logger = $this->createMock(Logger::class);

        $useragentFactory = $this->getMockBuilder(UseragentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $useragentFactory
            ->expects(self::any())
            ->method('build')
            ->will(self::returnValue([]));

        $divisionData = $this->createMock(DivisionData::class);

        $this->object = new DivisionFactory($logger);

        $property = new \ReflectionProperty($this->object, 'useragentFactory');
        $property->setAccessible(true);
        $property->setValue($this->object, $useragentFactory);

        $property = new \ReflectionProperty($this->object, 'divisionData');
        $property->setAccessible(true);
        $property->setValue($this->object, $divisionData);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildOk() : void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[], []],
        ];
        $filename     = 'test.xyz';
        $allDivisions = [];

        self::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, $allDivisions, false));
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildOkWithVersions() : void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[]],
            'versions' => ['1.0'],
        ];
        $filename     = 'test.xyz';
        $allDivisions = [];

        self::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, $allDivisions, false));
    }
}

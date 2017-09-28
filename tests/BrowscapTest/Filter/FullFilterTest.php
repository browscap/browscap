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
namespace BrowscapTest\Filter;

use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Filter\FilterInterface;
use Browscap\Filter\FullFilter;

/**
 * Class FullFilterTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class FullFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Filter\FullFilter
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(self::any())
            ->method('isOutputProperty')
            ->will(self::returnValue(true));

        $this->object = new FullFilter($propertyHolder);
    }

    /**
     * tests getter for the filter type
     *
     * @group filter
     * @group sourcetest
     */
    public function testGetType() : void
    {
        self::assertSame(FilterInterface::TYPE_FULL, $this->object->getType());
    }

    /**
     * tests detecting if a divion should be in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutput() : void
    {
        $division = $this->createMock(Division::class);

        self::assertTrue($this->object->isOutput($division));
    }

    /**
     * @group filter
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $isExtra
     */
    public function testIsOutputProperty() : void
    {
        self::assertTrue($this->object->isOutputProperty('Comment'));
    }

    /**
     * @group filter
     * @group sourcetest
     *
     * @param mixed $propertyName
     * @param mixed $isExtra
     */
    public function testIsOutputPropertyModified() : void
    {
        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $propertyHolder
            ->expects(self::any())
            ->method('isOutputProperty')
            ->will(self::returnValue(false));

        $object = new FullFilter($propertyHolder);
        self::assertFalse($object->isOutputProperty('Comment'));
    }

    /**
     * tests if a section is always in the output
     *
     * @group filter
     * @group sourcetest
     */
    public function testIsOutputSectionAlways() : void
    {
        $this->assertTrue($this->object->isOutputSection([]));
        $this->assertTrue($this->object->isOutputSection(['full' => false]));
        $this->assertTrue($this->object->isOutputSection(['full' => true]));
    }
}

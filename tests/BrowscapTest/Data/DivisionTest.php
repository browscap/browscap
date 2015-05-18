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
 * @package    Data
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Data;

use Browscap\Data\Division;

/**
 * Class DivisionTest
 *
 * @category   BrowscapTest
 * @package    Data
 * @author     James Titcumb <james@asgrim.com>
 */
class DivisionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Browscap\Data\Division
     */
    private $object = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->object = new Division();
    }

    /**
     * tests setter and getter
     *
     * @group data
     * @group sourcetest
     */
    public function testSetGetLite()
    {
        self::assertSame($this->object, $this->object->setLite(true));
        self::assertTrue($this->object->isLite());
    }

    /**
     * tests setter and getter for the division name
     *
     * @group data
     * @group sourcetest
     */
    public function testSetGetName()
    {
        $name = 'TestName';

        self::assertSame($this->object, $this->object->setName($name));
        self::assertSame($name, $this->object->getName());
    }

    /**
     * tests setter and getter for the sortindex
     *
     * @group data
     * @group sourcetest
     */
    public function testSetGetSortIndex()
    {
        $sortIndex = 42;

        self::assertSame($this->object, $this->object->setSortIndex($sortIndex));
        self::assertSame($sortIndex, $this->object->getSortIndex());
    }

    /**
     * tests setter and getter for useragents
     *
     * @group data
     * @group sourcetest
     */
    public function testSetGetUserAgents()
    {
        $userAgents = array('abc' => 'def');

        self::assertSame($this->object, $this->object->setUserAgents($userAgents));
        self::assertSame($userAgents, $this->object->getUserAgents());
    }

    /**
     * tests setter and getter for versions
     *
     * @group data
     * @group sourcetest
     */
    public function testSetGetVersions()
    {
        $versions = array(1, 2, 3);

        self::assertSame($this->object, $this->object->setVersions($versions));
        self::assertSame($versions, $this->object->getVersions());
    }
}

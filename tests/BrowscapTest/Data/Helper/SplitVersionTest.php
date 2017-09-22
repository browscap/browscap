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
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\SplitVersion;

/**
 * Class SplitVersionTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class SplitVersionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\SplitVersion
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new SplitVersion();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testGetVersionParts() : void
    {
        $result = $this->object->getVersionParts('1');

        self::assertInternalType('array', $result);
        self::assertSame(['1', '0'], $result);
    }
}

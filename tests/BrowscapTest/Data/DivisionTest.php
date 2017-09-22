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
namespace BrowscapTest\Data;

use Browscap\Data\Division;
use Browscap\Data\Useragent;

/**
 * Class DivisionTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class DivisionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter
     *
     * @group data
     * @group sourcetest
     */
    public function testGetter() : void
    {
        $useragent = $this->getMockBuilder(Useragent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(self::never())
            ->method('getUserAgent')
            ->will(self::returnValue('abc'));

        $useragent
            ->expects(self::never())
            ->method('getProperties')
            ->will(self::returnValue([
                'Parent' => 'DefaultProperties',
                'Browser' => 'xyz',
                'Version' => '1.0',
                'MajorBer' => '1',
                'Device_Type' => 'Desktop',
                'isTablet' => 'false',
                'isMobileDevice' => 'false',
            ]));

        $name       = 'TestName';
        $sortIndex  = 42;
        $userAgents = [0 => $useragent];
        $versions   = [1, 2, 3];
        $fileName   = 'abc.json';

        $object = new Division($name, $sortIndex, $userAgents, true, false, $versions, $fileName);

        self::assertSame($name, $object->getName());
        self::assertSame($sortIndex, $object->getSortIndex());
        self::assertSame($userAgents, $object->getUserAgents());
        self::assertTrue($object->isLite());
        self::assertFalse($object->isStandard());
        self::assertSame($versions, $object->getVersions());
        self::assertSame($fileName, $object->getFileName());
    }
}

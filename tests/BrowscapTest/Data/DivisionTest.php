<?php
declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Division;
use Browscap\Data\UserAgent;
use PHPUnit\Framework\TestCase;

class DivisionTest extends TestCase
{
    /**
     * tests setter and getter
     */
    public function testGetter() : void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgent', 'getProperties'])
            ->getMock();

        $useragent
            ->expects(static::never())
            ->method('getUserAgent')
            ->willReturn('abc');

        $useragent
            ->expects(static::never())
            ->method('getProperties')
            ->willReturn([
                'Parent' => 'DefaultProperties',
                'Browser' => 'xyz',
                'Version' => '1.0',
                'MajorBer' => '1',
                'Device_Type' => 'Desktop',
                'isTablet' => 'false',
                'isMobileDevice' => 'false',
            ]);

        $name       = 'TestName';
        $sortIndex  = 42;
        $userAgents = [0 => $useragent];
        $versions   = [1, 2, 3];
        $fileName   = 'abc.json';

        $object = new Division($name, $sortIndex, $userAgents, true, false, $versions, $fileName);

        static::assertSame($name, $object->getName());
        static::assertSame($sortIndex, $object->getSortIndex());
        static::assertSame($userAgents, $object->getUserAgents());
        static::assertTrue($object->isLite());
        static::assertFalse($object->isStandard());
        static::assertSame($versions, $object->getVersions());
        static::assertSame($fileName, $object->getFileName());
    }
}

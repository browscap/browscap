<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\SplitVersion;

/**
 * Class SplitVersionTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class SplitVersionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\SplitVersion
     */
    private $object;

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

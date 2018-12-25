<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\SplitVersion;
use PHPUnit\Framework\TestCase;

class SplitVersionTest extends TestCase
{
    /**
     * @var SplitVersion
     */
    private $object;

    protected function setUp() : void
    {
        $this->object = new SplitVersion();
    }

    public function testGetVersionParts() : void
    {
        $result = $this->object->getVersionParts('1');

        static::assertIsArray($result);
        static::assertSame(['1', '0'], $result);
    }
}

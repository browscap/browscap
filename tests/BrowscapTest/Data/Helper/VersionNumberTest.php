<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\VersionNumber;
use PHPUnit\Framework\TestCase;

class VersionNumberTest extends TestCase
{
    /**
     * @var VersionNumber
     */
    private $object;

    protected function setUp() : void
    {
        $this->object = new VersionNumber();
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices() : void
    {
        static::assertSame('Android Browser 3.0', $this->object->replace('Android Browser #MAJORVER#.#MINORVER#', '3', '0'));
    }
}

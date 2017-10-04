<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Helper;

use Browscap\Data\Helper\VersionNumber;

/**
 * Class ExpanderTestTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class VersionNumberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Helper\VersionNumber
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new VersionNumber();
    }

    /**
     * tests pattern id generation on a not empty data collection with children, platforms and devices
     *
     * @group data
     * @group sourcetest
     */
    public function testPatternIdCollectionOnNotEmptyDatacollectionWithChildrenPlatformsAndDevices() : void
    {
        self::assertSame('Android Browser 3.0', $this->object->replace('Android Browser #MAJORVER#.#MINORVER#', '3', '0'));
    }
}

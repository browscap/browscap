<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\UseragentFactory;
use Browscap\Data\Useragent;
use Browscap\Data\Validator\ChildrenData;
use Browscap\Data\Validator\UseragentData;

/**
 * Class UseragentFactoryTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class UseragentFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\UseragentFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new UseragentFactory();

        $useragentData = $this->createMock(UseragentData::class);

        $property = new \ReflectionProperty($this->object, 'useragentData');
        $property->setAccessible(true);
        $property->setValue($this->object, $useragentData);

        $childrenData = $this->createMock(ChildrenData::class);

        $property = new \ReflectionProperty($this->object, 'childrenData');
        $property->setAccessible(true);
        $property->setValue($this->object, $childrenData);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildOkCore() : void
    {
        $userAgentsData = [
            [
                'userAgent' => 'abc',
                'properties' => [
                    'Parent' => 'DefaultProperties',
                    'Comment' => 'abc',
                    'Version' => '0.0',
                ],
                'children' => [
                    ['match' => 'xyz'],
                ],
            ],
        ];

        $allDivisions = [];

        $uas = $this->object->build($userAgentsData, [], true, $allDivisions, '');

        self::assertInternalType('array', $uas);

        foreach ($uas as $useragent) {
            self::assertInstanceOf(Useragent::class, $useragent);
        }
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testChildrenNotArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('each entry of the children property has to be an array for key "abc"');

        $userAgentsData = [
            [
                'userAgent' => 'abc',
                'properties' => [
                    'Parent' => 'DefaultProperties',
                    'Comment' => 'abc',
                    'Version' => '0.0',
                ],
                'children' => ['test'],
            ],
        ];

        $allDivisions = [];

        $uas = $this->object->build($userAgentsData, [], false, $allDivisions, '');

        self::assertInternalType('array', $uas);

        foreach ($uas as $useragent) {
            self::assertInstanceOf(Useragent::class, $useragent);
        }
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildOkNotCore() : void
    {
        $userAgentsData = [
            [
                'userAgent' => 'abc',
                'properties' => [
                    'Parent' => 'DefaultProperties',
                    'Comment' => 'abc',
                    'Version' => '0.0',
                ],
                'children' => [
                    ['match' => 'xyz'],
                ],
            ],
        ];

        $allDivisions = [];

        $uas = $this->object->build($userAgentsData, [], false, $allDivisions, '');

        self::assertInternalType('array', $uas);

        foreach ($uas as $useragent) {
            self::assertInstanceOf(Useragent::class, $useragent);
        }
    }
}

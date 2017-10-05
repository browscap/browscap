<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\UserAgentFactory;
use Browscap\Data\UserAgent;
use Browscap\Data\Validator\ChildrenDataValidator;
use Browscap\Data\Validator\UseragentDataValidator;

/**
 * Class UseragentFactoryTestTest
 */
class UseragentFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\UserAgentFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new UserAgentFactory();

        $useragentData = $this->createMock(UseragentDataValidator::class);

        $property = new \ReflectionProperty($this->object, 'useragentData');
        $property->setAccessible(true);
        $property->setValue($this->object, $useragentData);

        $childrenData = $this->createMock(ChildrenDataValidator::class);

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
            self::assertInstanceOf(UserAgent::class, $useragent);
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
            self::assertInstanceOf(UserAgent::class, $useragent);
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
            self::assertInstanceOf(UserAgent::class, $useragent);
        }
    }
}

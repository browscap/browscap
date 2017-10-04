<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Engine;
use Browscap\Data\Factory\EngineFactory;

/**
 * Class EngineFactoryTestTest
 */
class EngineFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Factory\EngineFactory
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new EngineFactory();
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildWithMissingParent() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('parent Engine "abc" is missing for engine "Test"');

        $engineData = ['abc' => 'def', 'inherits' => 'abc'];
        $json       = [];
        $engineName = 'Test';

        $this->object->build($engineData, $json, $engineName);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuildWithRepeatingProperties() : void
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('the value for property "abc" has the same value in the keys "Test" and its parent "abc"');

        $engineData = ['properties' => ['abc' => 'def'], 'inherits' => 'abc'];
        $json       = [
            'engines' => [
                'abc' => [
                    'properties' => ['abc' => 'def'],
                ],
            ],
        ];
        $engineName = 'Test';

        $this->object->build($engineData, $json, $engineName);
    }

    /**
     * tests the creating of an engine factory
     *
     * @group data
     * @group sourcetest
     */
    public function testBuild() : void
    {
        $engineData = ['properties' => ['abc' => 'xyz'], 'inherits' => 'abc'];
        $json       = [
            'engines' => [
                'abc' => [
                    'properties' => ['abc' => 'def'],
                ],
            ],
        ];
        $engineName = 'Test';

        self::assertInstanceOf(Engine::class, $this->object->build($engineData, $json, $engineName));
    }
}

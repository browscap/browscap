<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Assert\InvalidArgumentException;
use Browscap\Data\Engine;
use Browscap\Data\Factory\EngineFactory;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class EngineFactoryTest extends TestCase
{
    /**
     * @var EngineFactory
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new EngineFactory();
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithMissingInheritAndProperties() : void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('required attibute "properties" is missing');

        $engineData = ['abc' => 'def'];
        $json       = [];
        $engineName = 'Test';

        $this->object->build($engineData, $json, $engineName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithMissingParent() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('parent Engine "abc" is missing for engine "Test"');

        $engineData = ['abc' => 'def', 'inherits' => 'abc'];
        $json       = [];
        $engineName = 'Test';

        $this->object->build($engineData, $json, $engineName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithRepeatingProperties() : void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('the value for property "abc" has the same value in the keys "Test" and its parent "abc"');

        $engineData = ['properties' => ['abc' => 'def'], 'inherits' => 'abc'];
        $json       = [
            'abc' => [
                'properties' => ['abc' => 'def'],
            ],
        ];
        $engineName = 'Test';

        $this->object->build($engineData, $json, $engineName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuild() : void
    {
        $engineData = ['properties' => ['abc' => 'xyz'], 'inherits' => 'abc'];
        $json       = [
            'abc' => [
                'properties' => ['abc' => 'def'],
            ],
        ];
        $engineName = 'Test';

        self::assertInstanceOf(Engine::class, $this->object->build($engineData, $json, $engineName));
    }
}

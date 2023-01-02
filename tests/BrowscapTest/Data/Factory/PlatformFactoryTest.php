<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use Browscap\Data\Factory\PlatformFactory;
use Browscap\Data\Platform;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use UnexpectedValueException;

class PlatformFactoryTest extends TestCase
{
    private PlatformFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new PlatformFactory();
    }

    /**
     * tests that the missing "lite" property is leading to an error
     *
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testBuildMissingLiteProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "lite" key is missing for the platform with the key "Test"');

        $platformData = ['abc' => 'def'];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests that the missing "standard" property is leading to an error
     *
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testBuildMissingStandardProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for the platform with the key "Test"');

        $platformData = ['abc' => 'def', 'lite' => false];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests that the missing "match" property is leading to an error
     *
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testBuildWithoutMatchProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for the "match" key is missing for the platform with the key "Test"');

        $platformData = ['properties' => ['abc' => 'def'], 'lite' => false, 'standard' => false, 'inherits' => 'abc'];
        $json         = [
            'abc' => [
                'properties' => ['abc' => 'def'],
                'standard' => false,
                'lite' => false,
            ],
        ];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests that the missing "inherits" property and missing "properties" property is leading to an error
     *
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function testBuildWithMissingInheritAndProperties(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('required attibute "properties" is missing');

        $platformData = ['abc' => 'def', 'match' => 'test*', 'lite' => false, 'standard' => false];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * tests that a missing parent platform is leading to an error
     *
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testBuildMissingParentPlatform(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('parent Platform "abc" is missing for platform "Test"');

        $platformData = ['abc' => 'def', 'match' => 'test*', 'lite' => false, 'standard' => false, 'inherits' => 'abc'];
        $json         = [];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function testBuildWithRepeatingProperties(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('the value for property "abc" has the same value in the keys "Test" and its parent "abc"');

        $platformData = ['properties' => ['abc' => 'def'], 'match' => 'test*', 'lite' => false, 'standard' => false, 'inherits' => 'abc'];
        $json         = [
            'abc' => [
                'match' => 'test*',
                'properties' => ['abc' => 'def'],
                'standard' => false,
                'lite' => false,
            ],
        ];
        $platformName = 'Test';

        $this->object->build($platformData, $json, $platformName);
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     */
    public function testCreationOfPlatform(): void
    {
        $platformData = ['properties' => ['abc' => 'zyx'], 'match' => 'test*', 'lite' => true, 'standard' => true, 'inherits' => 'abc'];
        $json         = [
            'abc' => [
                'match' => 'test*',
                'properties' => ['abc' => 'def'],
                'standard' => false,
                'lite' => false,
            ],
        ];
        $platformName = 'Test';

        $platform = $this->object->build($platformData, $json, $platformName);

        static::assertInstanceOf(Platform::class, $platform);
        static::assertFalse($platform->isLite());
        static::assertFalse($platform->isStandard());
    }
}

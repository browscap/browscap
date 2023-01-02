<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use Browscap\Data\Browser;
use Browscap\Data\Factory\BrowserFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use UnexpectedValueException;

class BrowserFactoryTest extends TestCase
{
    private BrowserFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new BrowserFactory();
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testBuildWithoutStandardProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for browser "Test"');

        $browserData = ['Browser' => 'def'];
        $browserName = 'Test';

        $this->object->build($browserData, $browserName);
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function testBuildWithWrongBrowserType(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('unsupported browser type given for browser "Test"');

        $browserData = ['properties' => ['Browser' => 'xyz'], 'standard' => true, 'lite' => false, 'type' => 'does not exist'];
        $browserName = 'Test';

        $this->object->build($browserData, $browserName);
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testBuildWithUnsupportedBrowserType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "validator" is not an element of the valid values: application, bot, bot-syndication-reader, bot-trancoder, browser, email-client, feed-reader, library, multimedia-player, offline-browser, tool, transcoder, useragent-anonymizer, unknown');

        $browserData = ['properties' => ['Browser' => 'xyz'], 'standard' => true, 'lite' => false, 'type' => 'validator'];
        $browserName = 'Test';

        $this->object->build($browserData, $browserName);
    }

    /**
     * @throws AssertionFailedException
     * @throws RuntimeException
     */
    public function testCreationOfBrowser(): void
    {
        $browserData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'lite' => false, 'type' => 'bot'];
        $browserName = 'Test';

        $browser = $this->object->build($browserData, $browserName);
        static::assertInstanceOf(Browser::class, $browser);
        static::assertTrue($browser->isStandard());
    }
}

<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Assert\InvalidArgumentException;
use Browscap\Data\Browser;
use Browscap\Data\Factory\BrowserFactory;
use PHPUnit\Framework\TestCase;

class BrowserFactoryTest extends TestCase
{
    /**
     * @var BrowserFactory
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new BrowserFactory();
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithoutStandardProperty() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the value for "standard" key is missing for browser "Test"');

        $browserData = ['abc' => 'def'];
        $browserName = 'Test';

        $this->object->build($browserData, $browserName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithWrongBrowserType() : void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('unsupported browser type given for browser "Test"');

        $browserData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'lite' => false, 'type' => 'does not exist'];
        $browserName = 'Test';

        $this->object->build($browserData, $browserName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testBuildWithUnsupportedBrowserType() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "validator" is not an element of the valid values: application, bot, browser, email-client, feed-reader, library, multimedia-player, offline-browser, tool, transcoder, unknown, useragent-anonymizer');

        $browserData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'lite' => false, 'type' => 'validator'];
        $browserName = 'Test';

        $this->object->build($browserData, $browserName);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testCreationOfBrowser() : void
    {
        $browserData = ['properties' => ['abc' => 'xyz'], 'standard' => true, 'lite' => false, 'type' => 'bot'];
        $browserName = 'Test';

        $browser = $this->object->build($browserData, $browserName);
        self::assertInstanceOf(Browser::class, $browser);
        self::assertTrue($browser->isStandard());
    }
}

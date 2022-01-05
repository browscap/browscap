<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Validator;

use Assert\AssertionFailedException;
use Browscap\Data\Validator\PropertiesValidator;
use LogicException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class PropertiesValidatorTest extends TestCase
{
    private PropertiesValidator $object;

    /**
     * @throws void
     */
    protected function setUp(): void
    {
        $this->object = new PropertiesValidator();
    }

    /**
     * @throws LogicException
     * @throws AssertionFailedException
     */
    public function testCheckPropertyWithoutParent(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('property "Parent" is missing for key "test"');

        $properties = [];

        $this->object->validate($properties, 'test');
    }

    /**
     * tests if no error is raised if all went well
     *
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws LogicException
     * @throws AssertionFailedException
     */
    public function testCheckPropertyOk(): void
    {
        $properties = [
            'Parent' => 'default',
            'Comment' => 'Default Browser',
            'Browser' => 'Default Browser',
            'Browser_Type' => 'unknown',
            'Browser_Bits' => 0,
            'Browser_Maker' => 'unknown',
            'Browser_Modus' => 'unknown',
            'Version' => '0.0',
            'MajorVer' => '0',
            'MinorVer' => '0',
            'Platform' => 'unknown',
            'Platform_Version' => 'unknown',
            'Platform_Description' => 'unknown',
            'Platform_Bits' => 0,
            'Platform_Maker' => 'unknown',
            'Alpha' => false,
            'Beta' => false,
            'Win16' => false,
            'Win32' => false,
            'Win64' => false,
            'Frames' => false,
            'IFrames' => false,
            'Tables' => false,
            'Cookies' => false,
            'BackgroundSounds' => false,
            'JavaScript' => false,
            'VBScript' => false,
            'JavaApplets' => false,
            'ActiveXControls' => false,
            'isMobileDevice' => false,
            'isTablet' => false,
            'isSyndicationReader' => false,
            'Crawler' => false,
            'isFake' => false,
            'isAnonymized' => false,
            'isModified' => false,
            'CssVersion' => 0,
            'AolVersion' => 0,
            'Device_Name' => 'unknown',
            'Device_Maker' => 'unknown',
            'Device_Type' => 'unknown',
            'Device_Pointing_Method' => 'unknown',
            'Device_Code_Name' => 'unknown',
            'Device_Brand_Name' => 'unknown',
            'RenderingEngine_Name' => 'unknown',
            'RenderingEngine_Version' => 'unknown',
            'RenderingEngine_Description' => 'unknown',
            'RenderingEngine_Maker' => 'unknown',
            'PatternId' => 'resources/core/default-browser.json::u0',
        ];

        $this->object->validate($properties, 'test');

        static::assertTrue(true);
    }
}

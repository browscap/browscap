<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Validator;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use Browscap\Data\Validator\DivisionDataValidator;
use LogicException;
use PHPUnit\Framework\TestCase;

class DivisionDataValidatorTest extends TestCase
{
    /** @var DivisionDataValidator */
    private $object;

    protected function setUp(): void
    {
        $this->object = new DivisionDataValidator();
    }

    /**
     * @throws AssertionFailedException
     */
    public function testDivisionPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "division" is missing in File abc.json');

        $divisionData = [];
        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testDivisionPropertyIsNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "division" has to be a string in File abc.json');

        $divisionData = [
            'division' => [],
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testSortIndexPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "sortIndex" is missing in File abc.json');

        $divisionData = ['division' => 'abc'];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testSortIndexPropertyIsNotInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "sortIndex" has to be a integer in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => false,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testSortIndexPropertyIsZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "sortIndex" has to be a positive integer in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => -1,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testLitePropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "lite" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testLitePropertyIsNotBoolean(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "lite" has to be an boolean in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => 'false',
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testStandardPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "standard" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testStandardPropertyIsNotBoolean(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "standard" has to be an boolean in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => 'true',
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentsPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgents" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentsPropertyIsNotAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgents" should be an non-empty array in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [],
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];
        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgent" is missing in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [[]],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentPropertyIsNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgent" has to be a string in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [['userAgent' => []]],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentPropertyHasInvalidCharacters(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('required attibute "userAgent" includes invalid characters in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [['userAgent' => 'abc[']],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentPropertisDefinedTwice(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Division "abc" is defined twice in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [['userAgent' => 'abc']],
        ];

        $allDivisions = ['abc'];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentPropertyHasVersionPlaceholdersButNoVersions(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Division "abc#MAJORVER#" is defined with version placeholders, but no versions are set in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [['userAgent' => 'abc#MAJORVER#']],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testUserAgentPropertyHasNoVersionPlaceholdersButMultipleVersions(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Division "abc" is defined without version placeholders, but there are versions set in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1', '2'],
            'userAgents' => [['userAgent' => 'abc']],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "properties" is missing in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [['userAgent' => 'abc']],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyIsNotArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "properties" should be an array in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => 'bcd',
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testParentPropertyIsMissingInProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Parent" property is missing for key "abc" in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['abc'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testWrongParentProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Parent" property is not linked to the "DefaultProperties" for key "abc" in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'abc'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testCommentPropertyIsMissingInProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Comment" property is missing for key "abc" in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testCommentPropertyIsNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Comment" property has to be a string for key "abc" in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => []],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testVersionPropertyIsMissingInProperties(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property is missing for key "abc" in file "abc.json", but there are defined versions');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testOkForCore(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, true);
        static::assertTrue(true);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testVersionPropertyIsNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Version" property has to be a string for key "abc" in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => []],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testVersionPropertyHasPlaceholdersButNoVersions(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property has version placeholders for key "abc" in file "abc.json", but no versions are defined');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testVersionPropertyHasNoPlaceholdersButMultipleVersions(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property has no version placeholders for key "abc#MAJORVER#" in file "abc.json", but versions are defined');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1', '2'],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testChildrenPropertyIsMissingInProperties(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "children" is missing in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testChildrenPropertyIsNotAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "children" should be an array in userAgents section 0 in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => 'xyz',
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testChildrenPropertyHasDirectMatch(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the children property shall not have the "match" entry for key "abc" in file "abc.json"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => ['match' => '123'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testDeviceAndDevicesPropertiesAreAvailable(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('a child entry may not define both the "device" and the "devices" entries for key "abc"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => [],
                            'devices' => 'def',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testDevicesPropertyIsNotAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "devices" entry for key "abc" has to be an array');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        ['devices' => 'def'],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testDevicePropertyIsNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "device" entry has to be a string for key "abc"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => [],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testMatchPropertyIsNotAvailable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('each entry of the children property requires an "match" entry for key "abc"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        ['device' => 'abc'],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testMatchPropertyIsNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "match" entry for key "abc" has to be a string');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => [],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testMatchPropertyIncludesInvalidCharacters(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('key "[abc" includes invalid characters');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => '[abc',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testMatchPropertyIncludesPlatformPlaceholder(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#PLATFORM#" is defined with platform placeholder, but no platforms are assigned');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc#PLATFORM#',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPlatformsPropertyIsNotAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "platforms" entry for key "abc2" has to be an array');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc2',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc',
                            'platforms' => 'abc',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testMultiplePlatformsWithoutPlatformPlaceholder(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key "abc2"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1'],
            'userAgents' => [
                [
                    'userAgent' => 'abc2',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc',
                            'platforms' => ['abc', 'def'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testVersionPlaceholderIsAvailableButNoVersions(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#MAJORVER#" is defined with version placeholders, but no versions are set');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc2',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc#MAJORVER#',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersions(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc" is defined without version placeholders, but there are versions set');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1', '2'],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersionsAndNoDynamicPlatform(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#PLATFORM#" is defined without version placeholders, but there are versions set');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1', '2'],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc#PLATFORM#',
                            'platforms' => ['abc', 'def'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersionsButWithDynamicPlatform(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['1', '2'],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc#PLATFORM#',
                            'platforms' => ['abc', 'def_dynamic'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
        static::assertTrue(true);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testDevicePlaceholderIsAvailableButNoDevices(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#DEVICE#" is defined with device placeholder, but no devices are assigned');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc#DEVICE#',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testNoDevicePlaceholderIsAvailableButMultipleDevices(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "devices" entry contains multiple devices but there is no #DEVICE# token for key "abc#MAJORVER#"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'match' => 'abc',
                            'devices' => ['cdf', 'xyz'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testOkWithoutProperties(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'match' => 'abc#DEVICE#',
                            'devices' => ['cdf', 'xyz'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
        static::assertTrue(true);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyIsNotAnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "properties" entry for key "abc" has to be an array');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc',
                            'properties' => 'test',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasParent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the Parent property must not set inside the children array for key "abc"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc',
                            'properties' => ['Parent' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasVersionSameAsParent(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property is set for key "abc", but was already set for its parent "abc#MAJORVER#" with the same value');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => [],
            'userAgents' => [
                [
                    'userAgent' => 'abc#MAJORVER#',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc',
                            'properties' => ['Version' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasDeviceProperties(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains device data for key "abc2", please use the "device" or the "devices" keyword');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc2',
                            'properties' => ['Version' => 'test', 'Device_Name' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasEngineProperties(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains engine data for key "abc2", please use the "engine" keyword');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc2',
                            'properties' => ['Version' => 'test', 'RenderingEngine_Name' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasPlatformProperties(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains platform data for key "abc2", please use the "platforms" keyword');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc2',
                            'properties' => ['Version' => 'test', 'Platform' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasBrowserProperties(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains browser data for key "abc2", please use the "browser" keyword');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc2',
                            'properties' => ['Version' => 'test', 'Browser' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testPropertiesPropertyHasDeprecatedProperties(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains deprecated properties for key "abc2"');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc2',
                            'properties' => ['Version' => 'test', 'AolVersion' => '1'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     */
    public function testOk(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'versions' => ['0.0'],
            'userAgents' => [
                [
                    'userAgent' => 'abc',
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test'],
                    'children' => [
                        [
                            'device' => 'abc',
                            'match' => 'abc2',
                            'properties' => ['Version' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
        static::assertTrue(true);
    }
}

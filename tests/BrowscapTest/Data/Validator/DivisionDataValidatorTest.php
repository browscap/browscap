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
    private DivisionDataValidator $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new DivisionDataValidator();
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testDivisionPropertyIsNotAvailable(): void
    {
        $divisionData = [];
        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "division" is missing in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testDivisionPropertyIsNotString(): void
    {
        $divisionData = [
            'division' => [],
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "division" has to be a string in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testSortIndexPropertyIsNotAvailable(): void
    {
        $divisionData = ['division' => 'abc'];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "sortIndex" is missing in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testSortIndexPropertyIsNotInteger(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => false,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "sortIndex" has to be a integer in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testSortIndexPropertyIsZero(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => -1,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "sortIndex" has to be a positive integer in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testLitePropertyIsNotAvailable(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "lite" is missing in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testLitePropertyIsNotBoolean(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => 'false',
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "lite" has to be an boolean in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testStandardPropertyIsNotAvailable(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "standard" is missing in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testStandardPropertyIsNotBoolean(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => 'true',
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "standard" has to be an boolean in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testUserAgentsPropertyIsNotAvailable(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgents" is missing in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testUserAgentsPropertyIsNotAnArray(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [],
        ];

        $fileName     = 'abc.json';
        $allDivisions = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgents" should be an non-empty array in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testUserAgentPropertyIsNotAvailable(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [[]],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgent" is missing in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testUserAgentPropertyIsNotString(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [['userAgent' => []]],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "userAgent" has to be a string in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testUserAgentPropertyHasInvalidCharacters(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [['userAgent' => 'abc[']],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('required attibute "userAgent" includes invalid characters in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testUserAgentPropertisDefinedTwice(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [['userAgent' => 'abc']],
        ];

        $allDivisions = ['abc'];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Division "abc" is defined twice in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testUserAgentPropertyHasVersionPlaceholdersButNoVersions(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Division "abc#MAJORVER#" is defined with version placeholders, but no versions are set in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testUserAgentPropertyHasNoVersionPlaceholdersButMultipleVersions(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Division "abc" is defined without version placeholders, but there are versions set in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testPropertiesPropertyIsNotAvailable(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "properties" is missing in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testPropertiesPropertyIsNotArray(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "properties" should be an array in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testParentPropertyIsMissingInProperties(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Parent" property is missing for key "abc" in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testWrongParentProperty(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Parent" property is not linked to the "DefaultProperties" for key "abc" in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testCommentPropertyIsMissingInProperties(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Comment" property is missing for key "abc" in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testCommentPropertyIsNotString(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Comment" property has to be a string for key "abc" in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testVersionPropertyIsMissingInProperties(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property is missing for key "abc" in file "abc.json", but there are defined versions');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
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
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testVersionPropertyIsNotString(): void
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
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => []],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "Version" property has to be a string for key "abc" in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testVersionPropertyHasPlaceholdersButNoVersions(): void
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
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '#MAJORVER#'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property has version placeholders for key "abc" in file "abc.json", but no versions are defined');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testVersionPropertyHasNoPlaceholdersButMultipleVersions(): void
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
                    'properties' => ['Parent' => 'DefaultProperties', 'Comment' => 'test', 'Version' => '1.0'],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property has no version placeholders for key "abc#MAJORVER#" in file "abc.json", but versions are defined');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testChildrenPropertyIsMissingInProperties(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "children" is missing in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testChildrenPropertyIsNotAnArray(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required attibute "children" should be an array in userAgents section 0 in File abc.json');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testChildrenPropertyHasDirectMatch(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the children property shall not have the "match" entry for key "abc" in file "abc.json"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testDeviceAndDevicesPropertiesAreAvailable(): void
    {
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
                            'match' => '123',
                            'device' => 'def',
                            'devices' => [],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('a child entry may not define both the "device" and the "devices" entries for key "abc"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testDevicesPropertyIsNotAnArray(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "devices" entry for key "abc" has to be an array');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testDevicePropertyIsNotString(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "device" entry has to be a string for key "abc"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testMatchPropertyIsNotAvailable(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('each entry of the children property requires an "match" entry for key "abc"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testMatchPropertyIsNotString(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "match" entry for key "abc" has to be a string');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testMatchPropertyIncludesInvalidCharacters(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('key "[abc" includes invalid characters');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testMatchPropertyIncludesPlatformPlaceholder(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#PLATFORM#" is defined with platform placeholder, but no platforms are assigned');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testPlatformsPropertyIsNotAnArray(): void
    {
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "platforms" entry for key "abc2" has to be an array');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testMultiplePlatformsWithoutPlatformPlaceholder(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key "abc2"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testVersionPlaceholderIsAvailableButNoVersions(): void
    {
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#MAJORVER#" is defined with version placeholders, but no versions are set');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersions(): void
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
                            'match' => 'abc',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc" is defined without version placeholders, but there are versions set');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testNoVersionPlaceholderIsAvailableButMultipleVersionsAndNoDynamicPlatform(): void
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
                            'platforms' => ['abc', 'def'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#PLATFORM#" is defined without version placeholders, but there are versions set');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
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
     * @throws LogicException
     */
    public function testDevicePlaceholderIsAvailableButNoDevices(): void
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
                            'device' => 'abc',
                            'match' => 'abc#DEVICE#',
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the key "abc#DEVICE#" is defined with device placeholder, but no devices are assigned');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testNoDevicePlaceholderIsAvailableButMultipleDevices(): void
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
                            'match' => 'abc',
                            'devices' => ['cdf' => 'cdf', 'xyz' => 'xyz'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "devices" entry contains multiple devices but there is no #DEVICE# token for key "abc#MAJORVER#"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
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
                            'devices' => ['cdf' => 'xyz'],
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
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testPropertiesPropertyIsNotAnArray(): void
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "properties" entry for key "abc" has to be an array');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasParent(): void
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the Parent property must not set inside the children array for key "abc"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasVersionSameAsParent(): void
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

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the "Version" property is set for key "abc", but was already set for its parent "abc#MAJORVER#" with the same value');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasDeviceProperties(): void
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
                            'properties' => ['Version' => 'test', 'Device_Name' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains device data for key "abc2", please use the "device" or the "devices" keyword');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasEngineProperties(): void
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
                            'properties' => ['Version' => 'test', 'RenderingEngine_Name' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains engine data for key "abc2", please use the "engine" keyword');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasPlatformProperties(): void
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
                            'properties' => ['Version' => 'test', 'Platform' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains platform data for key "abc2", please use the "platforms" keyword');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasBrowserProperties(): void
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
                            'properties' => ['Version' => 'test', 'Browser' => 'test'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains browser data for key "abc2", please use the "browser" keyword');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
     */
    public function testPropertiesPropertyHasDeprecatedProperties(): void
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
                            'properties' => ['Version' => 'test', 'AolVersion' => '1'],
                        ],
                    ],
                ],
            ],
        ];

        $allDivisions = [];
        $fileName     = 'abc.json';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('the properties array contains deprecated properties for key "abc2"');

        $this->object->validate($divisionData, $fileName, $allDivisions, false);
    }

    /**
     * @throws AssertionFailedException
     * @throws LogicException
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

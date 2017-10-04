<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Validator;

use Browscap\Data\Division;
use Browscap\Data\Validator\DivisionData;

/**
 * Class DivisionDataTest
 *
 * @category   BrowscapTest
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DivisionDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Browscap\Data\Validator\DivisionData
     */
    private $object;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        $this->object = new DivisionData();
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDivisionPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "division" is missing in File abc.json');

        $divisionData = [];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testDivisionPropertyIsNotString() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "division" has to be a string in File abc.json');

        $divisionData = [
            'division' => [],
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testSortIndexPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "sortIndex" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testSortIndexPropertyIsNotInteger() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "sortIndex" has to be a positive integer in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => false,
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testSortIndexPropertyIsZero() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "sortIndex" has to be a positive integer in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => -1,
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testLitePropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "lite" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testLitePropertyIsNotBoolean() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "lite" has to be an boolean in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => 'false',
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testStandardPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "standard" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testStandardPropertyIsNotBoolean() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "standard" has to be an boolean in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => 'true',
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentsPropertyIsNotAvailable() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "userAgents" is missing in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testUserAgentsPropertyIsNotAnArray() : void
    {
        $this->expectException('\LogicException');
        $this->expectExceptionMessage('required attibute "userAgents" should be an non-empty array in File abc.json');

        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => [],
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
    }

    /**
     * @group data
     * @group sourcetest
     */
    public function testOk() : void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => false,
            'standard' => true,
            'userAgents' => ['abc'],
        ];

        $fileName = 'abc.json';

        $this->object->validate($divisionData, $fileName);
        self::assertTrue(true);
    }
}

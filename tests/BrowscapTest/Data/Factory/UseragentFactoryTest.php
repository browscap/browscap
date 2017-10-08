<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\UserAgentFactory;
use Browscap\Data\UserAgent;

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

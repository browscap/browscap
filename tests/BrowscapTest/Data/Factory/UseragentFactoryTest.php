<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\UserAgentFactory;
use Browscap\Data\UserAgent;
use PHPUnit\Framework\TestCase;

class UseragentFactoryTest extends TestCase
{
    /**
     * @var UserAgentFactory
     */
    private $object;

    public function setUp() : void
    {
        $this->object = new UserAgentFactory();
    }

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

        $uas = $this->object->build($userAgentsData, [], true);

        self::assertInternalType('array', $uas);

        foreach ($uas as $useragent) {
            self::assertInstanceOf(UserAgent::class, $useragent);
        }
    }

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

        $uas = $this->object->build($userAgentsData, [], false);

        self::assertInternalType('array', $uas);

        foreach ($uas as $useragent) {
            self::assertInstanceOf(UserAgent::class, $useragent);
        }
    }
}

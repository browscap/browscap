<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Browscap\Data\Factory\UserAgentFactory;
use Browscap\Data\UserAgent;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UseragentFactoryTest extends TestCase
{
    private UserAgentFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new UserAgentFactory();
    }

    /**
     * @throws Exception
     * @throws RuntimeException
     */
    public function testBuildOkCore(): void
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

        $uas = $this->object->build($userAgentsData, true);

        static::assertIsArray($uas);

        foreach ($uas as $useragent) {
            static::assertInstanceOf(UserAgent::class, $useragent);
        }
    }

    /**
     * @throws Exception
     * @throws RuntimeException
     */
    public function testBuildOkNotCore(): void
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

        $uas = $this->object->build($userAgentsData, false);

        static::assertIsArray($uas);

        foreach ($uas as $useragent) {
            static::assertInstanceOf(UserAgent::class, $useragent);
        }
    }
}

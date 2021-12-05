<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Factory\DivisionFactory;
use Browscap\Data\Factory\UserAgentFactory;
use PHPUnit\Framework\TestCase;

use function assert;

class DivisionFactoryTest extends TestCase
{
    private DivisionFactory $object;

    protected function setUp(): void
    {
        $useragentFactory = $this->getMockBuilder(UserAgentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $useragentFactory
            ->expects(static::once())
            ->method('build')
            ->willReturn([]);

        assert($useragentFactory instanceof UserAgentFactory);
        $this->object = new DivisionFactory($useragentFactory);
    }

    public function testCreationOfDivision(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[], []],
        ];
        $filename     = 'test.xyz';

        static::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }

    public function testBuildOkWithVersions(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[]],
            'versions' => ['1.0'],
        ];
        $filename     = 'test.xyz';

        static::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }
}

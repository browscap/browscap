<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Factory\DivisionFactory;
use Browscap\Data\Factory\UserAgentFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DivisionFactoryTest extends TestCase
{
    /**
     * @var DivisionFactory
     */
    private $object;

    protected function setUp() : void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $useragentFactory = $this->getMockBuilder(UserAgentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $useragentFactory
            ->expects(static::any())
            ->method('build')
            ->willReturn([]);

        /* @var LoggerInterface $logger */
        /* @var UserAgentFactory $useragentFactory */
        $this->object = new DivisionFactory($logger, $useragentFactory);
    }

    public function testCreationOfDivision() : void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[], []],
        ];
        $filename = 'test.xyz';

        static::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }

    public function testBuildOkWithVersions() : void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[]],
            'versions' => ['1.0'],
        ];
        $filename = 'test.xyz';

        static::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }
}

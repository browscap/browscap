<?php
declare(strict_types = 1);
namespace BrowscapTest\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Factory\DivisionFactory;
use Browscap\Data\Factory\UserAgentFactory;
use Browscap\Data\Validator\DivisionDataValidator;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class DivisionFactoryTest extends TestCase
{
    /**
     * @var DivisionFactory
     */
    private $object;

    public function setUp() : void
    {
        $logger = $this->createMock(Logger::class);

        $useragentFactory = $this->getMockBuilder(UserAgentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $useragentFactory
            ->expects(self::any())
            ->method('build')
            ->will(self::returnValue([]));

        $divisionData = $this->createMock(DivisionDataValidator::class);

        $this->object = new DivisionFactory($logger, $useragentFactory);
    }

    public function testBuildOk() : void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [[], []],
        ];
        $filename     = 'test.xyz';

        self::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
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
        $filename     = 'test.xyz';

        self::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }
}

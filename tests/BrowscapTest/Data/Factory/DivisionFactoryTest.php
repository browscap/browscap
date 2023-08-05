<?php

declare(strict_types=1);

namespace BrowscapTest\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Factory\DivisionFactory;
use Browscap\Data\Factory\UserAgentFactory;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\TestCase;

use function assert;

class DivisionFactoryTest extends TestCase
{
    private DivisionFactory $object;

    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        $useragentFactory = $this->createMock(UserAgentFactory::class);

        $useragentFactory
            ->expects(static::once())
            ->method('build')
            ->willReturn([]);

        assert($useragentFactory instanceof UserAgentFactory);
        $this->object = new DivisionFactory($useragentFactory);
    }

    /** @throws ExpectationFailedException */
    public function testCreationOfDivision(): void
    {
        $divisionData = [
            'division' => 'abc',
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [],
        ];
        $filename     = 'test.xyz';

        static::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }

    /** @throws ExpectationFailedException */
    public function testBuildOkWithVersions(): void
    {
        $divisionData = [
            'division' => 'abc',
            'versions' => ['1.0'],
            'sortIndex' => 1,
            'lite' => true,
            'standard' => true,
            'userAgents' => [],
        ];
        $filename     = 'test.xyz';

        static::assertInstanceOf(Division::class, $this->object->build($divisionData, $filename, false));
    }
}

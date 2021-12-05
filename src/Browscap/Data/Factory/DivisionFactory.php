<?php

declare(strict_types=1);

namespace Browscap\Data\Factory;

use Browscap\Data\Division;

use function is_array;
use function mb_strpos;
use function mb_substr;

/**
 * @phpstan-import-type DivisionData from Division
 */
final class DivisionFactory
{
    private UserAgentFactory $useragentFactory;

    public function __construct(UserAgentFactory $useragentFactory)
    {
        $this->useragentFactory = $useragentFactory;
    }

    /**
     * validates the $divisionData array and creates Division objects from it
     *
     * @param mixed[] $divisionData
     * @phpstan-param DivisionData $divisionData
     */
    public function build(
        array $divisionData,
        string $filename,
        bool $isCore
    ): Division {
        if (isset($divisionData['versions']) && is_array($divisionData['versions'])) {
            $versions = $divisionData['versions'];
        } else {
            $versions = ['0.0'];
        }

        return new Division(
            $divisionData['division'],
            (int) $divisionData['sortIndex'],
            $this->useragentFactory->build($divisionData['userAgents'], $isCore),
            (bool) $divisionData['lite'],
            (bool) $divisionData['standard'],
            $versions,
            mb_substr($filename, (int) mb_strpos($filename, 'resources/'))
        );
    }
}

<?php

declare(strict_types=1);

namespace Browscap\Data\Factory;

use Browscap\Data\UserAgent;

/** @phpstan-import-type UserAgentData from UserAgent */
class UserAgentFactory
{
    /**
     * validates the $userAgentsData array and creates at least one Useragent object from it
     *
     * @param mixed[][] $userAgentsData
     * @phpstan-param array<UserAgentData> $userAgentsData
     *
     * @return UserAgent[]
     */
    public function build(array $userAgentsData, bool $isCore): array
    {
        $useragents = [];

        foreach ($userAgentsData as $useragent) {
            $children = [];

            if (! $isCore) {
                $children = $useragent['children'] ?? [];
            }

            $useragents[] = new UserAgent(
                $useragent['userAgent'],
                $useragent['properties'],
                $children,
                $useragent['platform'] ?? null,
                $useragent['engine'] ?? null,
                $useragent['device'] ?? null,
                $useragent['browser'] ?? null,
            );
        }

        return $useragents;
    }
}

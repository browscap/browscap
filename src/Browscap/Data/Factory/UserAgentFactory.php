<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\UserAgent;

class UserAgentFactory
{
    /**
     * validates the $userAgentsData array and creates at least one Useragent object from it
     *
     * @param array[] $userAgentsData
     * @param bool    $isCore
     *
     * @return UserAgent[]
     */
    public function build(array $userAgentsData, bool $isCore) : array
    {
        $useragents = [];

        foreach ($userAgentsData as $useragent) {
            $children = [];

            if (!$isCore) {
                $children = $useragent['children'];
            }

            $useragents[] = new UserAgent(
                $useragent['userAgent'],
                $useragent['properties'],
                $children,
                isset($useragent['platform']) ? $useragent['platform'] : null,
                isset($useragent['engine']) ? $useragent['engine'] : null,
                isset($useragent['device']) ? $useragent['device'] : null,
                isset($useragent['browser']) ? $useragent['browser'] : null
            );
        }

        return $useragents;
    }
}

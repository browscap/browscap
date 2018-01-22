<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\Division;
use Psr\Log\LoggerInterface;

final class DivisionFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserAgentFactory
     */
    private $useragentFactory;

    /**
     * @param LoggerInterface  $logger
     * @param UserAgentFactory $useragentFactory
     */
    public function __construct(LoggerInterface $logger, UserAgentFactory $useragentFactory)
    {
        $this->logger           = $logger;
        $this->useragentFactory = $useragentFactory;
    }

    /**
     * validates the $divisionData array and creates Division objects from it
     *
     * @param array  $divisionData
     * @param string $filename
     * @param bool   $isCore
     *
     * @return Division
     */
    public function build(
        array $divisionData,
        string $filename,
        bool $isCore
    ) : Division {
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

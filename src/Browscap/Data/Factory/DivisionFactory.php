<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Validator\DivisionDataValidator;
use Psr\Log\LoggerInterface;

class DivisionFactory
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
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger           = $logger;
        $this->useragentFactory = new UserAgentFactory();
    }

    /**
     * validates the $deviceData array and creates Device objects from it
     *
     * @param array  $divisionData
     * @param string $filename
     * @param array  &$allDivisions
     * @param bool   $isCore
     *
     * @throws \UnexpectedValueException If required attibutes are missing in the division
     * @throws \LogicException
     *
     * @return Division
     */
    public function build(
        array $divisionData,
        string $filename,
        array &$allDivisions,
        bool $isCore
    ) : Division {
        if (isset($divisionData['versions']) && is_array($divisionData['versions'])) {
            $versions = $divisionData['versions'];
        } else {
            $versions = ['0.0'];
        }

        if (1 < count($divisionData['userAgents'])) {
            $this->logger->info('division "' . $divisionData['division'] . '" has more than one "userAgents" section, try to separate them');
        }

        return new Division(
            $divisionData['division'],
            (int) $divisionData['sortIndex'],
            $this->useragentFactory->build($divisionData['userAgents'], $versions, $isCore, $allDivisions, $filename),
            (bool) $divisionData['lite'],
            (bool) $divisionData['standard'],
            $versions,
            mb_substr($filename, (int) mb_strpos($filename, 'resources/'))
        );
    }
}

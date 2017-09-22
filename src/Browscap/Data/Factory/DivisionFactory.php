<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\Division;
use Browscap\Data\Validator\DivisionData;
use Psr\Log\LoggerInterface;

/**
 * Class DivisionFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DivisionFactory
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var UseragentFactory
     */
    private $useragentFactory;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger           = $logger;
        $this->useragentFactory = new UseragentFactory();
    }

    /**
     * @param array  $divisionData
     * @param string $filename
     * @param array  &$allDivisions
     * @param bool   $isCore
     *
     * @throws \UnexpectedValueException If required attibutes are missing in the division
     * @throws \LogicException
     *
     * @return \Browscap\Data\Division
     */
    public function build(
        array $divisionData,
        string $filename,
        array &$allDivisions,
        bool $isCore
    ) : Division {
        DivisionData::validate($divisionData, $filename);

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

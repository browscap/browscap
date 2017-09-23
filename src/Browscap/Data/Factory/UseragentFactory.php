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

use Browscap\Data\Useragent;
use Browscap\Data\Validator\ChildrenData;
use Browscap\Data\Validator\UseragentData;

/**
 * Class UaFactory
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class UseragentFactory
{
    /**
     * @var \Browscap\Data\Validator\UseragentData
     */
    private $useragentData;

    /**
     * @var \Browscap\Data\Validator\ChildrenData
     */
    private $childrenData;

    public function __construct()
    {
        $this->useragentData = new UseragentData();
        $this->childrenData  = new ChildrenData();
    }

    /**
     * @param array[] $userAgentsData
     * @param array   $versions
     * @param bool    $isCore
     * @param array   &$allDivisions
     * @param string  $filename
     *
     * @throws \RuntimeException If the file does not exist or has invalid JSON
     * @throws \LogicException   If required attibutes are missing in the division
     * @throws \LogicException
     *
     * @return \Browscap\Data\Useragent[]
     */
    public function build(array $userAgentsData, array $versions, bool $isCore, array &$allDivisions, string $filename) : array
    {
        $useragents = [];

        foreach ($userAgentsData as $useragent) {
            $this->useragentData->validate($useragent, $versions, $allDivisions, $isCore, $filename);

            $children = [];

            if (!$isCore) {
                foreach ($useragent['children'] as $child) {
                    if (!is_array($child)) {
                        throw new \LogicException(
                            'each entry of the children property has to be an array for key "'
                            . $useragent['userAgent'] . '"'
                        );
                    }

                    $this->childrenData->validate($child, $useragent, $versions);
                }

                $children = $useragent['children'];
            }

            $useragents[] = new Useragent(
                $useragent['userAgent'],
                $useragent['properties'],
                $children,
                isset($useragent['platform']) ? $useragent['platform'] : null,
                isset($useragent['engine']) ? $useragent['engine'] : null,
                isset($useragent['device']) ? $useragent['device'] : null
            );

            $allDivisions[] = $useragent['userAgent'];
        }

        return $useragents;
    }
}

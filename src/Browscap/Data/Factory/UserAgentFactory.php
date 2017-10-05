<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\UserAgent;
use Browscap\Data\Validator\ChildrenDataValidator;
use Browscap\Data\Validator\UseragentDataValidator;

class UserAgentFactory
{
    /**
     * @var UseragentDataValidator
     */
    private $useragentData;

    /**
     * @var ChildrenDataValidator
     */
    private $childrenData;

    public function __construct()
    {
        $this->useragentData = new UseragentDataValidator();
        $this->childrenData  = new ChildrenDataValidator();
    }

    /**
     * validates the $userAgentsData array and creates at least one Useragent object from it
     *
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
     * @return UserAgent[]
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

            $useragents[] = new UserAgent(
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

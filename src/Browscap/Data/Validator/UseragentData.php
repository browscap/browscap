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
namespace Browscap\Data\Validator;

use Browscap\Data\Helper\CheckDeviceData;
use Browscap\Data\Helper\CheckEngineData;
use Browscap\Data\Helper\CheckPlatformData;

/**
 * Class UseragentData
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class UseragentData
{
    /**
     * @var \Browscap\Data\Helper\CheckDeviceData
     */
    private $checkDeviceData;

    /**
     * @var \Browscap\Data\Helper\CheckEngineData
     */
    private $checkEngineData;

    /**
     * @var \Browscap\Data\Helper\CheckPlatformData
     */
    private $checkPlatformData;

    public function __construct()
    {
        $this->checkDeviceData   = new CheckDeviceData();
        $this->checkEngineData   = new CheckEngineData();
        $this->checkPlatformData = new CheckPlatformData();
    }

    /**
     * @param array $useragentData
     * @param array $versions
     * @param array %$allDivisions
     * @param bool   $isCore
     * @param string $filename
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function validate(
        array $useragentData,
        array $versions,
        array &$allDivisions,
        bool $isCore,
        string $filename
    ) : void {
        if (!array_key_exists('userAgent', $useragentData)) {
            throw new \LogicException('Name for Division is missing in file "' . $filename . '"');
        }

        if (!is_string($useragentData['userAgent'])) {
            throw new \LogicException('Name of Division has to be a string in file "' . $filename . '"');
        }

        if (preg_match('/[\[\]]/', $useragentData['userAgent'])) {
            throw new \LogicException(
                'Name of Division "' . $useragentData['userAgent'] . '" includes invalid characters in file "' . $filename . '"'
            );
        }

        if (false === mb_strpos($useragentData['userAgent'], '#')
            && in_array($useragentData['userAgent'], $allDivisions)
        ) {
            throw new \LogicException('Division "' . $useragentData['userAgent'] . '" is defined twice in file "' . $filename . '"');
        }

        if ((false !== mb_strpos($useragentData['userAgent'], '#MAJORVER#')
                || false !== mb_strpos($useragentData['userAgent'], '#MINORVER#'))
            && ['0.0'] === $versions
        ) {
            throw new \LogicException(
                'Division "' . $useragentData['userAgent']
                . '" is defined with version placeholders, but no versions are set in file "' . $filename . '"'
            );
        }

        if (false === mb_strpos($useragentData['userAgent'], '#MAJORVER#')
            && false === mb_strpos($useragentData['userAgent'], '#MINORVER#')
            && ['0.0'] !== $versions
            && 1 < count($versions)
        ) {
            throw new \LogicException(
                'Division "' . $useragentData['userAgent']
                . '" is defined without version placeholders, but there are versions set in file "' . $filename . '"'
            );
        }

        if (!array_key_exists('properties', $useragentData)) {
            throw new \LogicException(
                'the properties entry is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!is_array($useragentData['properties']) || empty($useragentData['properties'])) {
            throw new \LogicException(
                'the properties entry has to be an non-empty array for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!$isCore && !isset($useragentData['properties']['Parent'])) {
            throw new \LogicException(
                'the "Parent" property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!$isCore && 'DefaultProperties' !== $useragentData['properties']['Parent']) {
            throw new \LogicException(
                'the "Parent" property is not linked to the "DefaultProperties" for key "'
                . $useragentData['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!array_key_exists('Comment', $useragentData['properties'])) {
            throw new \LogicException(
                'the "Comment" property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!is_string($useragentData['properties']['Comment'])) {
            throw new \LogicException(
                'the "Comment" property has to be a string for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!array_key_exists('Version', $useragentData['properties']) && ['0.0'] !== $versions) {
            throw new \LogicException(
                'the "Version" property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                . '", but there are defined versions'
            );
        }

        if (!$isCore) {
            if (array_key_exists('Version', $useragentData['properties'])) {
                if (!is_string($useragentData['properties']['Version'])) {
                    throw new \LogicException(
                        'the "Version" property has to be a string for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                        . '"'
                    );
                }

                if ((false !== mb_strpos($useragentData['properties']['Version'], '#MAJORVER#')
                        || false !== mb_strpos($useragentData['properties']['Version'], '#MINORVER#'))
                    && ['0.0'] === $versions) {
                    throw new \LogicException(
                        'the "Version" property has version placeholders for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                        . '", but no versions are defined'
                    );
                }

                if (false === mb_strpos($useragentData['properties']['Version'], '#MAJORVER#')
                    && false === mb_strpos($useragentData['properties']['Version'], '#MINORVER#')
                    && ['0.0'] !== $versions
                    && 1 < count($versions)
                ) {
                    throw new \LogicException(
                        'the "Version" property has no version placeholders for key "' . $useragentData['userAgent'] . '" in file "' . $filename
                        . '", but versions are defined'
                    );
                }
            }

            if (!array_key_exists('children', $useragentData)) {
                throw new \LogicException(
                    'the children property is missing for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
                );
            }

            if (!is_array($useragentData['children'])) {
                throw new \LogicException(
                    'the children property has to be an array for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
                );
            }

            if (array_key_exists('match', $useragentData['children'])) {
                throw new \LogicException(
                    'the children property shall not have the "match" entry for key "' . $useragentData['userAgent'] . '" in file "' . $filename . '"'
                );
            }

            $this->checkPlatformData->check(
                $useragentData['properties'],
                'the properties array contains platform data for key "' . $useragentData['userAgent']
                . '", please use the "platform" keyword'
            );

            $this->checkEngineData->check(
                $useragentData['properties'],
                'the properties array contains engine data for key "' . $useragentData['userAgent']
                . '", please use the "engine" keyword'
            );

            $this->checkDeviceData->check(
                $useragentData['properties'],
                'the properties array contains device data for key "' . $useragentData['userAgent']
                . '", please use the "device" keyword'
            );
        }
    }
}

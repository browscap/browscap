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
     * @param array $useragent
     * @param array $versions
     * @param array %$allDivisions
     * @param bool   $isCore
     * @param string $filename
     *
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public function validate(
        array $useragent,
        array $versions,
        array &$allDivisions,
        bool $isCore,
        string $filename
    ) : void {
        if (!array_key_exists('userAgent', $useragent)) {
            throw new \UnexpectedValueException('Name for Division is missing');
        }

        if (!is_string($useragent['userAgent'])) {
            throw new \UnexpectedValueException(
                'Name of Division has to be a string'
            );
        }

        if (preg_match('/[\[\]]/', $useragent['userAgent'])) {
            throw new \UnexpectedValueException(
                'Name of Division "' . $useragent['userAgent'] . '" includes invalid characters'
            );
        }

        if (false === mb_strpos($useragent['userAgent'], '#')
            && in_array($useragent['userAgent'], $allDivisions)
        ) {
            throw new \UnexpectedValueException('Division "' . $useragent['userAgent'] . '" is defined twice');
        }

        if ((false !== mb_strpos($useragent['userAgent'], '#MAJORVER#')
                || false !== mb_strpos($useragent['userAgent'], '#MINORVER#'))
            && ['0.0'] === $versions
        ) {
            throw new \UnexpectedValueException(
                'Division "' . $useragent['userAgent']
                . '" is defined with version placeholders, but no versions are set'
            );
        }

        if (false === mb_strpos($useragent['userAgent'], '#MAJORVER#')
            && false === mb_strpos($useragent['userAgent'], '#MINORVER#')
            && ['0.0'] !== $versions
            && 1 < count($versions)
        ) {
            throw new \UnexpectedValueException(
                'Division "' . $useragent['userAgent']
                . '" is defined without version placeholders, but there are versions set'
            );
        }

        if (!array_key_exists('properties', $useragent)) {
            throw new \UnexpectedValueException(
                'the properties entry is missing for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!is_array($useragent['properties']) || empty($useragent['properties'])) {
            throw new \UnexpectedValueException(
                'the properties entry has to be an non-empty array for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!$isCore && !isset($useragent['properties']['Parent'])) {
            throw new \UnexpectedValueException(
                'the "Parent" property is missing for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!$isCore && 'DefaultProperties' !== $useragent['properties']['Parent']) {
            throw new \UnexpectedValueException(
                'the "Parent" property is not linked to the "DefaultProperties" for key "'
                . $useragent['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!array_key_exists('Comment', $useragent['properties'])) {
            throw new \UnexpectedValueException(
                'the "Comment" property is missing for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!is_string($useragent['properties']['Comment'])) {
            throw new \UnexpectedValueException(
                'the "Comment" property has to be a string for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
            );
        }

        if (!array_key_exists('Version', $useragent['properties']) && ['0.0'] !== $versions) {
            throw new \UnexpectedValueException(
                'the "Version" property is missing for key "' . $useragent['userAgent'] . '" in file "' . $filename
                . '", but there are defined versions'
            );
        }

        if (!$isCore) {
            if (array_key_exists('Version', $useragent['properties'])
                && (false !== mb_strpos($useragent['properties']['Version'], '#MAJORVER#')
                    || false !== mb_strpos($useragent['properties']['Version'], '#MINORVER#'))
                && ['0.0'] === $versions) {
                throw new \UnexpectedValueException(
                    'the "Version" property is set for key "' . $useragent['userAgent'] . '" in file "' . $filename
                    . '", but no versions are defined'
                );
            }

            if (!array_key_exists('children', $useragent)) {
                if ('C:\Users\Thomas Müller\Documents\GitHub\browscap\tests\BrowscapTest\Data/../../fixtures/ua/ua-with-version-property-but-no-versions.json' === $filename) {
                    var_dump($useragent);
                }

                throw new \UnexpectedValueException(
                    'the children property is missing for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
                );
            }

            if (!is_array($useragent['children'])) {
                throw new \UnexpectedValueException(
                    'the children property has to be an array for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
                );
            }

            if (array_key_exists('match', $useragent['children'])) {
                throw new \UnexpectedValueException(
                    'the children property shall not have the "match" entry for key "' . $useragent['userAgent'] . '" in file "' . $filename . '"'
                );
            }

            $this->checkPlatformData->check(
                $useragent['properties'],
                'the properties array contains platform data for key "' . $useragent['userAgent']
                . '", please use the "platform" keyword'
            );

            $this->checkEngineData->check(
                $useragent['properties'],
                'the properties array contains engine data for key "' . $useragent['userAgent']
                . '", please use the "engine" keyword'
            );

            $this->checkDeviceData->check(
                $useragent['properties'],
                'the properties array contains device data for key "' . $useragent['userAgent']
                . '", please use the "device" keyword'
            );
        }
    }
}

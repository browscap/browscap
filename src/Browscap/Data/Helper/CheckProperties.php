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
namespace Browscap\Data\Helper;

/**
 * Class DataCollection
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class CheckProperties
{
    /**
     * @param string $key
     * @param array  $properties
     *
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public function check($key, array $properties) : void
    {
        if (!isset($properties['Version'])) {
            throw new \UnexpectedValueException('Version property not found for key "' . $key . '"');
        }

        if (!isset($properties['Parent']) && !in_array($key, ['DefaultProperties', '*'])) {
            throw new \UnexpectedValueException('Parent property is missing for key "' . $key . '"');
        }

        if (!isset($properties['Device_Type'])) {
            throw new \UnexpectedValueException('property "Device_Type" is missing for key "' . $key . '"');
        }

        if (!isset($properties['isTablet'])) {
            throw new \UnexpectedValueException('property "isTablet" is missing for key "' . $key . '"');
        }

        if (!isset($properties['isMobileDevice'])) {
            throw new \UnexpectedValueException('property "isMobileDevice" is missing for key "' . $key . '"');
        }

        switch ($properties['Device_Type']) {
            case 'Tablet':
                if (true !== $properties['isTablet']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is NOT marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }

                break;
            case 'Mobile Phone':
            case 'Mobile Device':
            case 'Ebook Reader':
            case 'Console':
            case 'Digital Camera':
                if (true === $properties['isTablet']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }

                break;
            case 'TV Device':
            case 'Desktop':
            default:
                if (true === $properties['isTablet']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true === $properties['isMobileDevice']) {
                    throw new \UnexpectedValueException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Mobile Device for key "'
                        . $key . '"'
                    );
                }

                break;
        }
    }
}

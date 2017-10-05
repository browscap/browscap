<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

class PropertiesValidator
{
    /**
     * @param string $key
     * @param array  $properties
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function check(string $key, array $properties) : void
    {
        if (!array_key_exists('Version', $properties)) {
            throw new \LogicException('Version property not found for key "' . $key . '"');
        }

        if (!array_key_exists('Parent', $properties) && !in_array($key, ['DefaultProperties', '*'])) {
            throw new \LogicException('Parent property is missing for key "' . $key . '"');
        }

        if (!array_key_exists('Device_Type', $properties)) {
            throw new \LogicException('property "Device_Type" is missing for key "' . $key . '"');
        }

        if (!array_key_exists('isTablet', $properties)) {
            throw new \LogicException('property "isTablet" is missing for key "' . $key . '"');
        }

        if (!array_key_exists('isMobileDevice', $properties)) {
            throw new \LogicException('property "isMobileDevice" is missing for key "' . $key . '"');
        }

        switch ($properties['Device_Type']) {
            case 'Tablet':
                if (true !== $properties['isTablet']) {
                    throw new \LogicException(
                        'the device of type "' . $properties['Device_Type'] . '" is NOT marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \LogicException(
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
                    throw new \LogicException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \LogicException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }

                break;
            case 'TV Device':
            case 'Desktop':
            default:
                if (true === $properties['isTablet']) {
                    throw new \LogicException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true === $properties['isMobileDevice']) {
                    throw new \LogicException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Mobile Device for key "'
                        . $key . '"'
                    );
                }

                break;
        }
    }
}

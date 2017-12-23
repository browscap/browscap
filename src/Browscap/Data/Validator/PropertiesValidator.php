<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

use Assert\Assertion;

final class PropertiesValidator implements ValidatorInterface
{
    /**
     * validates the fully expanded properties
     *
     * @param array  $properties Data to validate
     * @param string $key
     *
     * @throws \LogicException
     * @throws \Assert\AssertionFailedException
     */
    public function validate(array $properties, string $key) : void
    {
        Assertion::keyExists($properties, 'Version', 'Version property not found for key "' . $key . '"');

        if (!in_array($key, ['DefaultProperties', '*'])) {
            Assertion::keyExists($properties, 'Parent', 'Parent property is missing for key "' . $key . '"');
        }

        Assertion::keyExists($properties, 'Device_Type', 'property "Device_Type" is missing for key "' . $key . '"');
        Assertion::keyExists($properties, 'isTablet', 'property "isTablet" is missing for key "' . $key . '"');
        Assertion::keyExists($properties, 'isMobileDevice', 'property "isMobileDevice" is missing for key "' . $key . '"');

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

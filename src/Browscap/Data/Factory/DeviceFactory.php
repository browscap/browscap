<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Device;

class DeviceFactory
{
    /**
     * validates the $deviceData array and creates Device objects from it
     *
     * @param array  $deviceData The Device data for the current object
     * @param array  $json       The Device data for all devices
     * @param string $deviceName The name for the current device
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return Device
     */
    public function build(array $deviceData, array $json, string $deviceName) : Device
    {
        if (!isset($deviceData['properties'])) {
            $deviceData['properties'] = [];
        }

        Assertion::keyExists($deviceData, 'standard', 'the value for "standard" key is missing for device "' . $deviceName . '"');

        if (array_key_exists('inherits', $deviceData)) {
            $parentName = $deviceData['inherits'];

            Assertion::keyExists($json['devices'], $parentName, 'parent Device "' . $parentName . '" is missing for device "' . $deviceName . '"');

            $parentDevice     = $this->build($json['devices'][$parentName], $json, $parentName);
            $parentDeviceData = $parentDevice->getProperties();

            $deviceProperties = $deviceData['properties'];

            foreach ($deviceProperties as $name => $value) {
                if (isset($parentDeviceData[$name])
                    && $parentDeviceData[$name] === $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $deviceName
                        . '" and its parent "' . $parentName . '"'
                    );
                }
            }

            $deviceData['properties'] = array_merge(
                $parentDeviceData,
                $deviceProperties
            );

            if (!$parentDevice->isStandard()) {
                $deviceData['standard'] = false;
            }
        }

        return new Device($deviceData['properties'], $deviceData['standard']);
    }
}

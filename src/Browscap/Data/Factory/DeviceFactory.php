<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Browscap\Data\Device;

/**
 * Class DeviceFactory
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DeviceFactory
{
    /**
     * Load a engines.json file and parse it into the platforms data array
     *
     * @param array  $deviceData
     * @param array  $json
     * @param string $deviceName
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return \Browscap\Data\Device
     */
    public function build(array $deviceData, array $json, string $deviceName) : Device
    {
        if (!isset($deviceData['properties'])) {
            $deviceData['properties'] = [];
        }

        if (!array_key_exists('standard', $deviceData)) {
            throw new \UnexpectedValueException(
                'the value for "standard" key is missing for device "' . $deviceName . '"'
            );
        }

        if (array_key_exists('inherits', $deviceData)) {
            $parentName = $deviceData['inherits'];

            if (!isset($json['devices'][$parentName])) {
                throw new \UnexpectedValueException(
                    'parent Device "' . $parentName . '" is missing for device "' . $deviceName . '"'
                );
            }

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

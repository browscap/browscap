<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Device;

final class DeviceFactory
{
    /**
     * validates the $deviceData array and creates Device objects from it
     *
     * @param array  $deviceData The Device data for the current object
     * @param string $deviceName The name for the current device
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return Device
     */
    public function build(array $deviceData, string $deviceName) : Device
    {
        Assertion::keyExists($deviceData, 'standard', 'the value for "standard" key is missing for device "' . $deviceName . '"');
        Assertion::boolean($deviceData['standard'], 'the value for "standard" key has to be a boolean value for device "' . $deviceName . '"');

        Assertion::keyExists($deviceData, 'properties', 'required attibute "properties" is missing for device "' . $deviceName . '"');
        Assertion::isArray($deviceData['properties'], 'the value for "properties" key has to be an array for device "' . $deviceName . '"');

        return new Device($deviceData['properties'], $deviceData['standard']);
    }
}

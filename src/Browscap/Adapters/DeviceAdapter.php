<?php

namespace Browscap\Adapters;

use Browscap\Parser\ParserInterface;
use Browscap\Entity\Device;

class DeviceAdapter
{
    protected $parser;

    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return Browscap\Entity\Device[]
     */
    public function generateEntities()
    {
        $deviceData = $this->parser->getParsed();

        $devices = array();

        foreach ($deviceData->devices as $sourceDevice)
        {
            $device = new Device();
            $device->deviceId = $sourceDevice->deviceId;
            $device->name = $sourceDevice->name;
            $device->maker = $sourceDevice->maker;

            if (isset($sourceDevice->match)) {
                $device->match = $sourceDevice->match;
            }

            $devices[$device->deviceId] = $device;
        }

        return $devices;
    }
}
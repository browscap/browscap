<?php
declare(strict_types = 1);
namespace Browscap\Data;

use Browscap\Data\Helper\CheckDeviceData;
use Browscap\Data\Helper\CheckEngineData;
use Browscap\Data\Helper\CheckPlatformData;
use Browscap\Data\Helper\TrimProperty;
use Psr\Log\LoggerInterface;

/**
 * Class Expander
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class Expander
{
    /**
     * @var \Browscap\Data\DataCollection
     */
    private $collection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * This store the components of the pattern id that are later merged into a string. Format for this
     * can be seen in the {@see resetPatternId} method.
     *
     * @var array
     */
    private $patternId = [];

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

    /**
     * @var \Browscap\Data\Helper\TrimProperty
     */
    private $trimProperty;

    /**
     * Create a new data expander
     *
     * @param \Psr\Log\LoggerInterface      $logger
     * @param \Browscap\Data\DataCollection $collection
     */
    public function __construct(LoggerInterface $logger, DataCollection $collection)
    {
        $this->logger            = $logger;
        $this->collection        = $collection;
        $this->checkDeviceData   = new CheckDeviceData();
        $this->checkEngineData   = new CheckEngineData();
        $this->checkPlatformData = new CheckPlatformData();
        $this->trimProperty      = new TrimProperty();
    }

    /**
     * @param \Browscap\Data\Division $division
     * @param string                  $divisionName
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    public function expand(Division $division, string $divisionName) : array
    {
        $allInputDivisions = $this->parseDivision(
            $division,
            $divisionName
        );

        return $this->expandProperties($allInputDivisions);
    }

    /**
     * Resets the pattern id
     *
     * @return void
     */
    private function resetPatternId() : void
    {
        $this->patternId = [
            'division' => '',
            'useragent' => '',
            'platform' => '',
            'device' => '',
            'child' => '',
        ];
    }

    /**
     * Render a single division
     *
     * @param \Browscap\Data\Division $division
     * @param string                  $divisionName
     *
     * @return array
     */
    private function parseDivision(Division $division, string $divisionName) : array
    {
        $output = [];

        $i = 0;
        foreach ($division->getUserAgents() as $uaData) {
            $this->resetPatternId();
            $this->patternId['division']  = $division->getFileName();
            $this->patternId['useragent'] = $i;

            $output = array_merge(
                $output,
                $this->parseUserAgent(
                    $uaData,
                    $division->isLite(),
                    $division->isStandard(),
                    $division->getSortIndex(),
                    $divisionName
                )
            );
            ++$i;
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param \Browscap\Data\Useragent $uaData
     * @param bool                     $lite
     * @param bool                     $standard
     * @param int                      $sortIndex
     * @param string                   $divisionName
     *
     * @return array
     */
    private function parseUserAgent(Useragent $uaData, bool $lite, bool $standard, int $sortIndex, string $divisionName) : array
    {
        $uaProperties = $uaData->getProperties();

        if (null !== $uaData->getPlatform()) {
            $this->patternId['platform'] = $uaData->getPlatform();
            $platform                    = $this->collection->getPlatform($uaData->getPlatform());

            if (!$platform->isLite()) {
                $lite = false;
            }

            if (!$platform->isStandard()) {
                $standard = false;
            }

            $platformProperties = $platform->getProperties();
        } else {
            $this->patternId['platform'] = '';
            $platformProperties          = [];
        }

        if (null !== $uaData->getEngine()) {
            $engine           = $this->collection->getEngine($uaData->getEngine());
            $engineProperties = $engine->getProperties();
        } else {
            $engineProperties = [];
        }

        if (null !== $uaData->getDevice()) {
            $device           = $this->collection->getDevice($uaData->getDevice());
            $deviceProperties = $device->getProperties();

            if (!$device->isStandard()) {
                $standard = false;
            }
        } else {
            $deviceProperties = [];
        }

        $ua = $uaData->getUserAgent();

        $output = [
            $ua => array_merge(
                [
                    'lite' => $lite,
                    'standard' => $standard,
                    'sortIndex' => $sortIndex,
                    'division' => $divisionName,
                ],
                $platformProperties,
                $engineProperties,
                $deviceProperties,
                $uaProperties
            ),
        ];

        $i = 0;
        foreach ($uaData->getChildren() as $child) {
            $this->patternId['child'] = $i;
            if (isset($child['devices']) && is_array($child['devices'])) {
                // Replace our device array with a single device property with our #DEVICE# token replaced
                foreach ($child['devices'] as $deviceMatch => $deviceName) {
                    $this->patternId['device'] = $deviceMatch;
                    $subChild                  = $child;
                    $subChild['match']         = str_replace('#DEVICE#', $deviceMatch, $subChild['match']);
                    $subChild['device']        = $deviceName;
                    unset($subChild['devices']);
                    $output = array_merge(
                        $output,
                        $this->parseChildren($ua, $subChild, $lite, $standard)
                    );
                }
            } else {
                $this->patternId['device'] = '';
                $output                    = array_merge(
                    $output,
                    $this->parseChildren($ua, $child, $lite, $standard)
                );
            }
            ++$i;
        }

        return $output;
    }

    /**
     * Render the children section in a single User Agent block
     *
     * @param string $ua
     * @param array  $uaDataChild
     * @param bool   $lite
     * @param bool   $standard
     *
     * @return array[]
     */
    private function parseChildren(string $ua, array $uaDataChild, bool $lite = true, bool $standard = true) : array
    {
        $output = [];

        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $this->patternId['platform'] = $platform;
                $properties                  = ['Parent' => $ua, 'lite' => $lite, 'standard' => $standard];
                $platformProperties          = $this->collection->getPlatform($platform);

                if (!$platformProperties->isLite()) {
                    $properties['lite'] = false;
                }

                if (!$platformProperties->isStandard()) {
                    $properties['standard'] = false;
                }

                $uaBase = str_replace('#PLATFORM#', $platformProperties->getMatch(), $uaDataChild['match']);

                if (array_key_exists('engine', $uaDataChild)) {
                    $engine           = $this->collection->getEngine($uaDataChild['engine']);
                    $engineProperties = $engine->getProperties();
                } else {
                    $engineProperties = [];
                }

                if (array_key_exists('device', $uaDataChild)) {
                    $device           = $this->collection->getDevice($uaDataChild['device']);
                    $deviceProperties = $device->getProperties();

                    if (!$device->isStandard()) {
                        $properties['standard'] = false;
                    }
                } else {
                    $deviceProperties = [];
                }

                $properties = array_merge(
                    $properties,
                    $engineProperties,
                    $deviceProperties,
                    $platformProperties->getProperties()
                );

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $childProperties = $uaDataChild['properties'];

                    $properties = array_merge($properties, $childProperties);
                }

                $properties['PatternId'] = $this->getPatternId();

                $output[$uaBase] = $properties;
            }
        } else {
            $properties = ['Parent' => $ua, 'lite' => $lite, 'standard' => $standard];

            if (array_key_exists('engine', $uaDataChild)) {
                $engine           = $this->collection->getEngine($uaDataChild['engine']);
                $engineProperties = $engine->getProperties();
            } else {
                $engineProperties = [];
            }

            if (array_key_exists('device', $uaDataChild)) {
                $device           = $this->collection->getDevice($uaDataChild['device']);
                $deviceProperties = $device->getProperties();

                if (!$device->isStandard()) {
                    $properties['standard'] = false;
                }
            } else {
                $deviceProperties = [];
            }

            $properties = array_merge($properties, $engineProperties, $deviceProperties);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $childProperties = $uaDataChild['properties'];

                $this->checkPlatformData->check(
                    $childProperties,
                    'the properties array contains platform data for key "' . $ua
                    . '", please use the "platforms" keyword'
                );

                $this->checkEngineData->check(
                    $childProperties,
                    'the properties array contains engine data for key "' . $ua
                    . '", please use the "engine" keyword'
                );

                $this->checkDeviceData->check(
                    $childProperties,
                    'the properties array contains device data for key "' . $ua
                    . '", please use the "device" or the "devices" keyword'
                );

                $properties = array_merge($properties, $childProperties);
            }

            $uaBase                      = str_replace('#PLATFORM#', '', $uaDataChild['match']);
            $this->patternId['platform'] = '';

            $properties['PatternId'] = $this->getPatternId();

            $output[$uaBase] = $properties;
        }

        return $output;
    }

    /**
     * Builds and returns the string pattern id from the array components
     *
     * @return string
     */
    private function getPatternId() : string
    {
        return sprintf(
            '%s::u%d::c%d::d%s::p%s',
            $this->patternId['division'],
            $this->patternId['useragent'],
            $this->patternId['child'],
            $this->patternId['device'],
            $this->patternId['platform']
        );
    }

    /**
     * expands all properties for all useragents to make sure all properties are set and make it possible to skip
     * incomplete properties and remove duplicate definitions
     *
     * @param array $allInputDivisions
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    private function expandProperties(array $allInputDivisions) : array
    {
        $this->logger->debug('expand all properties');
        $allDivisions = [];

        $ua                = $this->collection->getDefaultProperties()->getUserAgents()[0];
        $defaultproperties = $ua->getProperties();

        foreach (array_keys($allInputDivisions) as $key) {
            $this->logger->debug('expand all properties for key "' . $key . '"');

            $userAgent = $key;
            $parents   = [$userAgent];

            while (isset($allInputDivisions[$userAgent]['Parent'])) {
                if ($allInputDivisions[$userAgent]['Parent'] === $userAgent) {
                    break;
                }

                $parents[] = $allInputDivisions[$userAgent]['Parent'];
                $userAgent = $allInputDivisions[$userAgent]['Parent'];
            }
            unset($userAgent);

            $parents     = array_reverse($parents);
            $browserData = $defaultproperties;
            $properties  = $allInputDivisions[$key];

            foreach ($parents as $parent) {
                if (!isset($allInputDivisions[$parent])) {
                    continue;
                }

                if (!is_array($allInputDivisions[$parent])) {
                    throw new \UnexpectedValueException(
                        'Parent "' . $parent . '" is not an array for key "' . $key . '"'
                    );
                }

                if ($key !== $parent
                    && isset($allInputDivisions[$parent]['sortIndex'], $properties['sortIndex'])

                    && ($allInputDivisions[$parent]['division'] !== $properties['division'])
                ) {
                    if ($allInputDivisions[$parent]['sortIndex'] >= $properties['sortIndex']) {
                        throw new \UnexpectedValueException(
                            'sorting not ready for key "'
                            . $key . '"'
                        );
                    }
                }

                $browserData = array_merge($browserData, $allInputDivisions[$parent]);
            }

            array_pop($parents);
            $browserData['Parents'] = implode(',', $parents);
            unset($parents);

            foreach (array_keys($browserData) as $propertyName) {
                if (is_bool($browserData[$propertyName])) {
                    $properties[$propertyName] = $browserData[$propertyName];
                } else {
                    $properties[$propertyName] = $this->trimProperty->trimProperty((string) $browserData[$propertyName]);
                }
            }

            unset($browserData);

            $allDivisions[$key] = $properties;

            if (!isset($properties['Version'])) {
                throw new \UnexpectedValueException('Version property not found for key "' . $key . '"');
            }

            $completeVersions = explode('.', $properties['Version'], 2);

            $properties['MajorVer'] = (string) $completeVersions[0];

            if (isset($completeVersions[1])) {
                $minorVersion = (string) $completeVersions[1];
            } else {
                $minorVersion = '0';
            }

            $properties['MinorVer'] = $minorVersion;

            $allDivisions[$key] = $properties;
        }

        return $allDivisions;
    }
}

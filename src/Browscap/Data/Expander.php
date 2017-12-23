<?php
declare(strict_types = 1);
namespace Browscap\Data;

use Browscap\Data\Helper\TrimProperty;
use Psr\Log\LoggerInterface;

class Expander
{
    /**
     * @var DataCollection
     */
    private $collection;

    /**
     * @var LoggerInterface
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
     * @var TrimProperty
     */
    private $trimProperty;

    /**
     * Create a new data expander
     *
     * @param LoggerInterface $logger
     * @param DataCollection  $collection
     */
    public function __construct(LoggerInterface $logger, DataCollection $collection)
    {
        $this->logger       = $logger;
        $this->collection   = $collection;
        $this->trimProperty = new TrimProperty();
    }

    /**
     * @param Division $division
     * @param string   $divisionName
     *
     * @throws \UnexpectedValueException
     * @throws \OutOfBoundsException
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
     */
    private function resetPatternId() : void
    {
        $this->patternId = [
            'division' => '',
            'useragent' => '',
            'platform' => '',
            'device' => '',
            'browser' => '',
            'child' => '',
        ];
    }

    /**
     * parses and expands a single division
     *
     * @param Division $division
     * @param string   $divisionName
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
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
     * parses and expands a single User Agent block
     *
     * @param UserAgent $uaData
     * @param bool      $lite
     * @param bool      $standard
     * @param int       $sortIndex
     * @param string    $divisionName
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    private function parseUserAgent(UserAgent $uaData, bool $lite, bool $standard, int $sortIndex, string $divisionName) : array
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

        if (null !== $uaData->getBrowser()) {
            $this->patternId['browser'] = $uaData->getBrowser();
            $browser                    = $this->collection->getBrowser($uaData->getBrowser());
            $browserProperties          = $browser->getProperties();

            if (!$browser->isStandard()) {
                $standard = false;
            }

            if (!$browser->isLite()) {
                $lite = false;
            }
        } else {
            $browserProperties          = [];
            $this->patternId['browser'] = '';
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
                $browserProperties,
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
     * parses and expands the children section in a single User Agent block
     *
     * @param string $ua
     * @param array  $uaDataChild
     * @param bool   $lite
     * @param bool   $standard
     *
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     *
     * @return array
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

                if (array_key_exists('browser', $uaDataChild)) {
                    $browser           = $this->collection->getBrowser($uaDataChild['browser']);
                    $browserProperties = $browser->getProperties();

                    if (!$browser->isStandard()) {
                        $properties['standard'] = false;
                    }

                    if (!$browser->isLite()) {
                        $properties['lite'] = false;
                    }
                } else {
                    $browserProperties = [];
                }

                $properties = array_merge(
                    $properties,
                    $engineProperties,
                    $deviceProperties,
                    $browserProperties,
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

            if (array_key_exists('browser', $uaDataChild)) {
                $browser           = $this->collection->getBrowser($uaDataChild['browser']);
                $browserProperties = $browser->getProperties();

                if (!$browser->isStandard()) {
                    $properties['standard'] = false;
                }

                if (!$browser->isLite()) {
                    $properties['lite'] = false;
                }
            } else {
                $browserProperties = [];
            }

            $properties = array_merge($properties, $engineProperties, $deviceProperties, $browserProperties);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $properties = array_merge($properties, $uaDataChild['properties']);
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
            '%s::u%d::c%d::d%s::p%s::b%s',
            $this->patternId['division'],
            $this->patternId['useragent'],
            $this->patternId['child'],
            $this->patternId['device'],
            $this->patternId['platform'],
            $this->patternId['browser']
        );
    }

    /**
     * expands all properties for all useragents to make sure all properties are set and make it possible to skip
     * incomplete properties and remove duplicate definitions
     *
     * @param array[] $allInputDivisions
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

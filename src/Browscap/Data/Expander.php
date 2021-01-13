<?php

declare(strict_types=1);

namespace Browscap\Data;

use Browscap\Data\Helper\TrimProperty;
use BrowserDetector\Loader\NotFoundException;
use Generator;
use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use UaBrowserType\TypeLoader as BrowserTypeLoader;
use UaDeviceType\TypeLoader as DeviceTypeLoader;
use UnexpectedValueException;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_pop;
use function array_reverse;
use function explode;
use function implode;
use function is_array;
use function is_string;
use function realpath;
use function sprintf;
use function str_replace;

class Expander
{
    /** @var DataCollection */
    private $collection;

    /** @var LoggerInterface */
    private $logger;

    /**
     * This store the components of the pattern id that are later merged into a string. Format for this
     * can be seen in the {@see resetPatternId} method.
     *
     * @var array<int|string, string|int|null>
     */
    private $patternId = [];

    /** @var TrimProperty */
    private $trimProperty;

    /**
     * Create a new data expander
     */
    public function __construct(LoggerInterface $logger, DataCollection $collection)
    {
        $this->logger       = $logger;
        $this->collection   = $collection;
        $this->trimProperty = new TrimProperty();
    }

    /**
     * @return mixed[][]
     *
     * @throws UnexpectedValueException
     * @throws OutOfBoundsException
     * @throws DuplicateDataException
     */
    public function expand(Division $division, string $divisionName): array
    {
        $defaultproperties    = $this->collection->getDefaultProperties()->getUserAgents()[0];
        $allInputDivisions    = [$defaultproperties->getUserAgent() => $defaultproperties->getProperties()];
        $allExpandedDivisions = [];

        foreach ($this->parseDivision($division, $divisionName) as $ua => $properties) {
            if (array_key_exists($ua, $allInputDivisions)) {
                throw new DuplicateDataException(
                    sprintf(
                        'tried to add section "%s" for division "%s" in file "%s", but this was already added before',
                        $ua,
                        $division->getName(),
                        (string) realpath((string) $division->getFileName())
                    )
                );
            }

            $allInputDivisions[$ua]    = $properties;
            $allExpandedDivisions[$ua] = $this->expandProperties(
                $ua,
                $properties,
                $defaultproperties->getProperties(),
                $allInputDivisions
            );
        }

        return $allExpandedDivisions;
    }

    /**
     * Resets the pattern id
     */
    private function resetPatternId(): void
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
     * parses and expands a single division
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    private function parseDivision(Division $division, string $divisionName): Generator
    {
        $i = 0;
        foreach ($division->getUserAgents() as $uaData) {
            $this->resetPatternId();
            $this->patternId['division']  = $division->getFileName();
            $this->patternId['useragent'] = $i;

            yield from $this->parseUserAgent(
                $uaData,
                $division->isLite(),
                $division->isStandard(),
                $division->getSortIndex(),
                $divisionName
            );

            ++$i;
        }
    }

    /**
     * parses and expands a single User Agent block
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    private function parseUserAgent(UserAgent $uaData, bool $liteUa, bool $standardUa, int $sortIndex, string $divisionName): Generator
    {
        $uaProperties = $uaData->getProperties();
        $lite         = $liteUa;
        $standard     = $standardUa;

        if ($uaData->getPlatform() !== null) {
            $this->patternId['platform'] = $uaData->getPlatform();
            $platform                    = $this->collection->getPlatform($uaData->getPlatform());

            if (! $platform->isLite()) {
                $lite = false;
            }

            if (! $platform->isStandard()) {
                $standard = false;
            }

            $platformProperties = $platform->getProperties();
        } else {
            $this->patternId['platform'] = '';
            $platformProperties          = [];
        }

        if ($uaData->getEngine() !== null) {
            $engine           = $this->collection->getEngine($uaData->getEngine());
            $engineProperties = $engine->getProperties();
        } else {
            $engineProperties = [];
        }

        if ($uaData->getDevice() !== null) {
            [$deviceProperties, $deviceStandard] = $this->getDeviceProperties($uaData->getDevice(), $standard);

            $standard = $standard && $deviceStandard;
        } else {
            $deviceProperties = [];
        }

        if ($uaData->getBrowser() !== null) {
            [$browserProperties, $browserLite, $browserStandard] = $this->getBrowserProperties($uaData->getBrowser(), $lite, $standard);
            $lite                                                = $lite && $browserLite;
            $standard                                            = $standard && $browserStandard;
        } else {
            $browserProperties = [];
        }

        $ua = $uaData->getUserAgent();

        yield $ua => array_merge(
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
        );

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

                    yield from $this->parseChildren($ua, $subChild, $lite, $standard);
                }
            } else {
                $this->patternId['device'] = '';

                yield from $this->parseChildren($ua, $child, $lite, $standard);
            }

            ++$i;
        }
    }

    /**
     * parses and expands the children section in a single User Agent block
     *
     * @param mixed[] $uaDataChild
     *
     * @throws OutOfBoundsException
     * @throws UnexpectedValueException
     */
    private function parseChildren(string $ua, array $uaDataChild, bool $liteChild = true, bool $standardChild = true): Generator
    {
        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platformName) {
                $lite     = $liteChild;
                $standard = $standardChild;

                $this->patternId['platform'] = $platformName;
                $platform                    = $this->collection->getPlatform($platformName);

                if (! $platform->isLite()) {
                    $lite = false;
                }

                if (! $platform->isStandard()) {
                    $standard = false;
                }

                $uaBase = str_replace('#PLATFORM#', $platform->getMatch(), $uaDataChild['match']);

                if (array_key_exists('engine', $uaDataChild)) {
                    $engine           = $this->collection->getEngine($uaDataChild['engine']);
                    $engineProperties = $engine->getProperties();
                } else {
                    $engineProperties = [];
                }

                if (array_key_exists('device', $uaDataChild)) {
                    [$deviceProperties, $deviceStandard] = $this->getDeviceProperties($uaDataChild['device'], $standard);
                    $standard                            = $standard && $deviceStandard;
                } else {
                    $deviceProperties = [];
                }

                if (array_key_exists('browser', $uaDataChild)) {
                    [$browserProperties, $browserLite, $browserStandard] = $this->getBrowserProperties($uaDataChild['browser'], $lite, $standard);
                    $lite                                                = $lite && $browserLite;
                    $standard                                            = $standard && $browserStandard;
                } else {
                    $browserProperties = [];
                }

                $properties = array_merge(
                    [
                        'Parent' => $ua,
                        'lite' => $lite,
                        'standard' => $standard,
                    ],
                    $engineProperties,
                    $deviceProperties,
                    $browserProperties,
                    $platform->getProperties()
                );

                if (
                    isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $childProperties = $uaDataChild['properties'];

                    $properties = array_merge($properties, $childProperties);
                }

                $properties['PatternId'] = $this->getPatternId();

                yield $uaBase => $properties;
            }
        } else {
            $lite     = $liteChild;
            $standard = $standardChild;

            if (array_key_exists('engine', $uaDataChild)) {
                $engine           = $this->collection->getEngine($uaDataChild['engine']);
                $engineProperties = $engine->getProperties();
            } else {
                $engineProperties = [];
            }

            if (array_key_exists('device', $uaDataChild)) {
                [$deviceProperties, $deviceStandard] = $this->getDeviceProperties($uaDataChild['device'], $standard);
                $standard                            = $standard && $deviceStandard;
            } else {
                $deviceProperties = [];
            }

            if (array_key_exists('browser', $uaDataChild)) {
                [$browserProperties, $browserLite, $browserStandard] = $this->getBrowserProperties($uaDataChild['browser'], $lite, $standard);
                $lite                                                = $lite && $browserLite;
                $standard                                            = $standard && $browserStandard;
            } else {
                $browserProperties = [];
            }

            $properties = array_merge(
                [
                    'Parent' => $ua,
                    'lite' => $lite,
                    'standard' => $standard,
                ],
                $engineProperties,
                $deviceProperties,
                $browserProperties
            );

            if (
                isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $properties = array_merge($properties, $uaDataChild['properties']);
            }

            $uaBase                      = str_replace('#PLATFORM#', '', $uaDataChild['match']);
            $this->patternId['platform'] = '';

            $properties['PatternId'] = $this->getPatternId();

            yield $uaBase => $properties;
        }
    }

    /**
     * @return mixed[]
     */
    private function getDeviceProperties(string $devicekey, bool $standard): array
    {
        $device           = $this->collection->getDevice($devicekey);
        $deviceProperties = $device->getProperties();

        if (! $device->isStandard()) {
            $standard = false;
        }

        $deviceTypeLoader = new DeviceTypeLoader();

        try {
            $deviceType = $deviceTypeLoader->load($device->getType());

            $deviceProperties['isMobileDevice'] = $deviceType->isMobile();
            $deviceProperties['isTablet']       = $deviceType->isTablet();

            if ($deviceType->getName() === null) {
                $deviceProperties['Device_Type'] = 'unknown';
            } elseif ($deviceType->getName() === 'TV') {
                $deviceProperties['Device_Type'] = 'TV Device';
            } elseif ($deviceType->getName() === 'Mobile Console') {
                $deviceProperties['Device_Type'] = 'Console';
            } else {
                $deviceProperties['Device_Type'] = $deviceType->getName();
            }
        } catch (NotFoundException $e) {
            $this->logger->critical($e);

            $deviceProperties['isMobileDevice'] = false;
            $deviceProperties['isTablet']       = false;
            $deviceProperties['Device_Type']    = 'unknown';
        }

        return [$deviceProperties, $standard];
    }

    /**
     * @return mixed[]
     */
    private function getBrowserProperties(string $browserkey, bool $lite, bool $standard): array
    {
        $browser           = $this->collection->getBrowser($browserkey);
        $browserProperties = $browser->getProperties();

        if (! $browser->isStandard()) {
            $standard = false;
        }

        if (! $browser->isLite()) {
            $lite = false;
        }

        $browserTypeLoader = new BrowserTypeLoader();

        try {
            $browserType = $browserTypeLoader->load($browser->getType());

            $browserProperties['isSyndicationReader'] = $browserType->isSyndicationReader();
            $browserProperties['Crawler']             = $browserType->isBot();
            $browserProperties['Browser_Type']        = ($browserType->getName() ?? 'unknown');
        } catch (NotFoundException $e) {
            $this->logger->critical($e);

            $browserProperties['isSyndicationReader'] = false;
            $browserProperties['Crawler']             = false;
            $browserProperties['Browser_Type']        = 'unknown';
        }

        return [$browserProperties, $lite, $standard];
    }

    /**
     * Builds and returns the string pattern id from the array components
     */
    private function getPatternId(): string
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
     * expands all properties for one useragent to make sure all properties are set and make it possible to skip
     * incomplete properties and remove duplicate definitions
     *
     * @param array<string> $properties
     * @param array<string> $defaultproperties
     * @param mixed[][]     $allInputDivisions
     *
     * @return mixed[]
     */
    private function expandProperties(string $ua, array $properties, array $defaultproperties, array $allInputDivisions): array
    {
        $this->logger->debug('expand all properties for useragent "' . $ua . '"');

        $userAgent = $ua;
        $parents   = [$userAgent];

        while (isset($allInputDivisions[$userAgent]['Parent'])) {
            if ($allInputDivisions[$userAgent]['Parent'] === $userAgent) {
                throw new InvalidParentException(sprintf('useragent "%s" defines itself as parent', $ua));
            }

            $parents[] = $allInputDivisions[$userAgent]['Parent'];
            $userAgent = $allInputDivisions[$userAgent]['Parent'];
        }

        unset($userAgent);

        $parents     = array_reverse($parents);
        $browserData = $defaultproperties;

        foreach ($parents as $parent) {
            if (! isset($allInputDivisions[$parent])) {
                throw new ParentNotDefinedException(
                    sprintf('the parent "%s" for useragent "%s" is not defined', $parent, $ua)
                );
            }

            if (! is_array($allInputDivisions[$parent])) {
                throw new UnexpectedValueException(
                    'Parent "' . $parent . '" is not an array for useragent "' . $ua . '"'
                );
            }

            if (
                $ua !== $parent
                && isset($allInputDivisions[$parent]['sortIndex'], $properties['sortIndex'])

                && ($allInputDivisions[$parent]['division'] !== $properties['division'])
            ) {
                if ($allInputDivisions[$parent]['sortIndex'] >= $properties['sortIndex']) {
                    throw new UnexpectedValueException(
                        'sorting not ready for useragent "'
                        . $ua . '"'
                    );
                }
            }

            $browserData = array_merge($browserData, $allInputDivisions[$parent]);
        }

        array_pop($parents);
        $browserData['Parents'] = implode(',', $parents);
        unset($parents);

        foreach (array_keys($browserData) as $propertyName) {
            $properties[$propertyName] = $browserData[$propertyName];

            if (! is_string($browserData[$propertyName])) {
                continue;
            }

            $properties[$propertyName] = $this->trimProperty->trim($browserData[$propertyName]);
        }

        unset($browserData);

        if (! isset($properties['Version'])) {
            throw new UnexpectedValueException('Version property not found for useragent "' . $ua . '"');
        }

        $completeVersions = explode('.', $properties['Version'], 2);

        $properties['MajorVer'] = $completeVersions[0];
        $properties['MinorVer'] = $completeVersions[1] ?? '0';

        return $properties;
    }
}

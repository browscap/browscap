<?php

namespace Browscap\Generator;

class CollectionParser
{
    /**
     * @var \Browscap\Generator\DataCollection
     */
    private $collection;

    /**
     * Set the data collection
     *
     * @param \Browscap\Generator\DataCollection $collection
     * @return \Browscap\Generator\BrowscapIniGenerator
     */
    public function setDataCollection(DataCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return \Browscap\Generator\DataCollection
     */
    public function getDataCollection()
    {
        if (!isset($this->collection)) {
            throw new \LogicException("Data collection has not been set yet - call setDataCollection");
        }

        return $this->collection;
    }

    /**
     * Generate and return the formatted browscap data
     *
     * @return array
     */
    public function parse()
    {
        $allDivisions = array();

        foreach ($this->getDataCollection()->getDivisions() as $division) {
            if ($division['division'] == 'Browscap Version') {
                continue;
            }

            if (isset($division['lite'])) {
                $lite = $division['lite'];
            } else {
                $lite = false;
            }

            $sortIndex = $division['sortIndex'];

            if (isset($division['versions']) && is_array($division['versions'])) {
                foreach ($division['versions'] as $version) {
                    $dots = explode('.', $version, 2);

                    $majorVer = $dots[0];
                    $minorVer = (isset($dots[1]) ? $dots[1] : 0);

                    $userAgents = json_encode($division['userAgents']);
                    $userAgents = str_replace(
                        array('#MAJORVER#', '#MINORVER#'),
                        array($majorVer, $minorVer),
                        $userAgents
                    );

                    $divisionName = str_replace(
                        array('#MAJORVER#', '#MINORVER#'),
                        array($majorVer, $minorVer),
                        $division['division']
                    );

                    $userAgents = json_decode($userAgents, true);

                    $allDivisions += $this->parseDivision(
                        $userAgents,
                        $majorVer,
                        $minorVer,
                        $lite,
                        $sortIndex,
                        $divisionName
                    );
                }
            } else {
                $allDivisions += $this->parseDivision(
                    $division['userAgents'],
                    0,
                    0,
                    $lite,
                    $sortIndex,
                    $division['division']
                );
            }
        }

        // full expand of all data
        $allDivisions = $this->expandProperties($allDivisions);

        return $allDivisions;
    }

    /**
     * Render a single division
     *
     * @param array   $userAgents
     * @param string  $majorVer
     * @param string  $minorVer
     * @param boolean $lite
     * @param integer $sortIndex
     * @param string  $divisionName
     *
     * @return array
     */
    private function parseDivision(array $userAgents, $majorVer, $minorVer, $lite, $sortIndex, $divisionName)
    {
        $output = array();

        foreach ($userAgents as $uaData) {
            $output += $this->parseUserAgent($uaData, $majorVer, $minorVer, $lite, $sortIndex, $divisionName);
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param array   $uaData
     * @param string  $majorVer
     * @param string  $minorVer
     * @param boolean $lite
     * @param integer $sortIndex
     * @param string  $divisionName
     *
     * @return array
     */
    private function parseUserAgent(array $uaData, $majorVer, $minorVer, $lite, $sortIndex, $divisionName)
    {
        $output = array(
            $uaData['userAgent'] => array(
                    'lite' => $lite,
                    'sortIndex' => $sortIndex,
                    'division' => $divisionName
                ) + $this->parseProperties($uaData['properties'], $majorVer, $minorVer)
        );

        if (isset($uaData['children']) && is_array($uaData['children'])) {
            if (isset($uaData['children']['match'])) {
                $output += $this->parseChildren($uaData['userAgent'], $uaData['children'], $majorVer, $minorVer);
            } else {
                foreach ($uaData['children'] as $child) {
                    if (!is_array($child) || !isset($child['match'])) {
                        continue;
                    }

                    $output += $this->parseChildren($uaData['userAgent'], $child, $majorVer, $minorVer);
                }
            }
        }

        return $output;
    }

    /**
     * Render the children section in a single User Agent block
     *
     * @param string $ua
     * @param array  $uaDataChild
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return array
     */
    private function parseChildren($ua, array $uaDataChild, $majorVer, $minorVer)
    {
        $output = array();

        // @todo This needs work here. What if we specify platforms AND versions?
        // We need to make it so it does as many permutations as necessary.
        if (isset($uaDataChild['platforms']) && is_array($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $properties = $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer);

                $platformData = $this->getDataCollection()->getPlatform($platform);
                $uaBase       = str_replace('#PLATFORM#', $platformData['match'], $uaDataChild['match']);

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $properties += $this->parseProperties(
                        ($uaDataChild['properties'] + $platformData['properties']),
                        $majorVer,
                        $minorVer
                    );
                } else {
                    $properties += $this->parseProperties($platformData['properties'], $majorVer, $minorVer);
                }

                $output[$uaBase] = $properties;
            }
        } else {
            $properties = $this->parseProperties(['Parent' => $ua], $majorVer, $minorVer);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $properties += $this->parseProperties($uaDataChild['properties'], $majorVer, $minorVer);
            }

            $output[$uaDataChild['match']] = $properties;
        }

        return $output;
    }

    /**
     * Render the properties of a single User Agent
     *
     * @param array  $properties
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return array
     */
    private function parseProperties(array $properties, $majorVer, $minorVer)
    {
        $output = array();
        foreach ($properties as $property => $value) {
            $value = str_replace(
                array('#MAJORVER#', '#MINORVER#'),
                array($majorVer, $minorVer),
                $value
            );

            $output[$property] = $value;
        }

        // add prefixed duplicates if the original exists
        if (isset($output['Browser'])) {
            $output['Browser_Name'] = $output['Browser'];
        }

        if (isset($output['Version'])) {
            $output['Browser_Version'] = $output['Version'];
        }

        if (isset($output['isSyndicationReader'])) {
            $output['Browser_isSyndicationReader'] = $output['isSyndicationReader'];
        }

        if (isset($output['Crawler'])) {
            $output['Browser_isBot'] = $output['Crawler'];
        }

        if (isset($output['Alpha'])) {
            $output['Browser_isAlpha'] = $output['Alpha'];
        }

        if (isset($output['Beta'])) {
            $output['Browser_isBeta'] = $output['Beta'];
        }

        if (isset($output['isMobileDevice'])) {
            $output['Device_isMobileDevice'] = $output['isMobileDevice'];
        }

        if (isset($output['isTablet'])) {
            $output['Device_isTablet'] = $output['isTablet'];
        }

        return $output;
    }

    /**
     * expands all properties for all useragents to make sure all properties are set and make it possible to skip
     * incomplete properties and remove duplicate definitions
     *
     * @param array $allInputDivisions
     *
     * @return array
     */
    private function expandProperties(array $allInputDivisions)
    {
        $allDivisions = array();

        foreach ($allInputDivisions as $key => $properties) {

            if (!isset($properties['Parent'])
                && 'DefaultProperties' !== $key
                && '*' !== $key
            ) {
                continue;
            }

            $userAgent = $key;
            $parents   = array($userAgent);

            while (isset($allInputDivisions[$userAgent]['Parent'])) {
                if ($userAgent === $allInputDivisions[$userAgent]['Parent']) {
                    break;
                }

                $parents[] = $allInputDivisions[$userAgent]['Parent'];
                $userAgent = $allInputDivisions[$userAgent]['Parent'];
            }
            unset($userAgent);

            $parents     = array_reverse($parents);
            $browserData = array();

            foreach ($parents as $parent) {
                if (!isset($allInputDivisions[$parent])) {
                    continue;
                }

                if (!is_array($allInputDivisions[$parent])) {
                    continue;
                }

                $browserData = array_merge($browserData, $allInputDivisions[$parent]);
            }

            array_pop($parents);
            $browserData['Parents'] = implode(',', $parents);

            foreach ($browserData as $propertyName => $propertyValue) {
                switch ((string) $propertyValue) {
                    case 'true':
                        $properties[$propertyName] = true;
                        break;
                    case 'false':
                        $properties[$propertyName] = false;
                        break;
                    default:
                        $properties[$propertyName] = trim($propertyValue);
                        break;
                }
            }

            $allDivisions[$key] = $properties;

            if (!isset($properties['Version'])) {
                continue;
            }

            $completeVersions = explode('.', $properties['Version'], 2);

            $properties['MajorVer'] = (string) $completeVersions[0];

            if (isset($completeVersions[1])) {
                $properties['MinorVer'] = (string) $completeVersions[1];
            } else {
                $properties['MinorVer'] = 0;
            }

            $allDivisions[$key] = $properties;
        }

        return $allDivisions;
    }

    /**
     * Get the type of a property
     *
     * @param string $propertyName
     * @throws \Exception
     * @return string
     */
    public static function getPropertyType($propertyName)
    {
        switch ($propertyName) {
            case 'Comment':
            case 'Category':
            case 'SubCategory':
            case 'Browser_Type':
            case 'Browser_SubType':
            case 'Browser':
            case 'Browser_Name':
            case 'Browser_Full':
            case 'Browser_Description':
            case 'Browser_Maker':
            case 'Browser_Modus':
            case 'Browser_Icon':
            case 'Platform':
            case 'Platform_Name':
            case 'Platform_Full':
            case 'Platform_Description':
            case 'Platform_Maker':
            case 'Platform_Icon':
            case 'Device_Type':
            case 'Device_Code_Name':
            case 'Device_Maker':
            case 'Device_Brand_Name':
            case 'Device_Name':
            case 'Device_Maker':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Full':
            case 'RenderingEngine_Description':
            case 'RenderingEngine_Maker':
            case 'RenderingEngine_Icon':
            case 'Parent':
                return 'string';
            case 'Platform_Version':
            case 'RenderingEngine_Version':
                return 'generic';
            case 'Version':
            case 'Browser_Version':
            case 'Browser_Bits':
            case 'Platform_Bits':
            case 'CssVersion':
            case 'AolVersion':
            case 'MajorVer':
            case 'MinorVer':
                return 'number';
            case 'Alpha':
            case 'Browser_isAlpha':
            case 'Beta':
            case 'Browser_isBeta':
            case 'Win16':
            case 'Win32':
            case 'Win64':
            case 'Frames':
            case 'IFrames':
            case 'Tables':
            case 'Cookies':
            case 'BackgroundSounds':
            case 'JavaScript':
            case 'VBScript':
            case 'JavaApplets':
            case 'ActiveXControls':
            case 'isMobileDevice':
            case 'Device_isMobileDevice':
            case 'Device_isTablet':
            case 'Device_isDesktop':
            case 'Device_isTv':
            case 'isSyndicationReader':
            case 'Browser_isSyndicationReader':
            case 'Crawler':
            case 'Browser_isBot':
            case 'Browser_isTanscoder':
                return 'boolean';
            default:
                throw new \InvalidArgumentException("Property {$propertyName} did not have a defined property type");
        }
    }

    /**
     * Determine if the specified property is an "extra" property (that should
     * be included in the "full" versions of the files)
     *
     * @param string $propertyName
     * @return boolean
     */
    public static function isExtraProperty($propertyName)
    {
        switch ($propertyName) {
            case 'Category':
            case 'SubCategory':
            case 'Browser_Type':
            case 'Browser_SubType':
            case 'Browser_Name':
            case 'Browser_Full':
            case 'Browser_Version':
            case 'Browser_Description':
            case 'Browser_Bits':
            case 'Browser_isSyndicationReader':
            case 'Browser_isBot':
            case 'Browser_isTanscoder':
            case 'Browser_isAlpha':
            case 'Browser_isBeta':
            case 'Browser_Maker':
            case 'Browser_Modus':
            case 'Browser_Icon':
            case 'Platform_Name':
            case 'Platform_Full':
            case 'Platform_Description':
            case 'Platform_Bits':
            case 'Platform_Maker':
            case 'Platform_Icon':
            case 'Device_Type':
            case 'Device_Code_Name':
            case 'Device_Maker':
            case 'Device_Brand_Name':
            case 'Device_Name':
            case 'Device_isMobileDevice':
            case 'Device_isTablet':
            case 'Device_isDesktop':
            case 'Device_isTv':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Full':
            case 'RenderingEngine_Version':
            case 'RenderingEngine_Description':
            case 'RenderingEngine_Maker':
            case 'RenderingEngine_Icon':
                return true;
            default:
                return false;
        }
    }
}

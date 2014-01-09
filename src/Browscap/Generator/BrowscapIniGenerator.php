<?php

namespace Browscap\Generator;

class BrowscapIniGenerator implements GeneratorInterface
{
    /**
     * @var bool
     */
    protected $quoteStringProperties;

    /**
     * @var bool
     */
    protected $includeExtraProperties;

    /**
     * @var bool
     */
    protected $liteOnly;

    /**
     * @var \Browscap\Generator\DataCollection
     */
    protected $collection;

    /**
     * Set defaults
     */
    public function __construct()
    {
        $this->quoteStringProperties = false;
        $this->includeExtraProperties = true;
        $this->liteOnly = false;
    }

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
     * Set the options for generation
     *
     * @param boolean $quoteStringProperties
     * @param boolean $includeExtraProperties
     * @param boolean $liteOnly
     * @return \Browscap\Generator\BrowscapIniGenerator
     */
    public function setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $this->quoteStringProperties = (bool)$quoteStringProperties;
        $this->includeExtraProperties = (bool)$includeExtraProperties;
        $this->liteOnly = (bool)$liteOnly;

        return $this;
    }

    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        $output = $this->renderHeader();

        $allDivisions = array();

        foreach ($this->getDataCollection()->getDivisions() as $division) {
            if ($division['division'] == 'Browscap Version') {
                continue;
            }

            if ($this->liteOnly && (!isset($division['lite']) || $division['lite'] == false)) {
                continue;
            }

            if (isset($division['versions']) && is_array($division['versions'])) {
                foreach ($division['versions'] as $version) {
                    $dots = explode('.', $version, 2);

                    $majorVer = $dots[0];
                    $minorVer = (isset($dots[1]) ? $dots[1] : 0);

                    $tmp = json_encode($division['userAgents']);
                    $tmp = str_replace(
                        array('#MAJORVER#', '#MINORVER#'),
                        array($majorVer, $minorVer),
                        $tmp
                    );

                    $userAgents = json_decode($tmp, true);

                    $allDivisions += $this->parseDivision($userAgents, $majorVer, $minorVer);
                }
            } else {
                $allDivisions += $this->parseDivision($division['userAgents'], 0, 0);
            }
        }

        $allProperties = array_keys(array('Parent' => '') + $allDivisions['DefaultProperties']);

        /*
         * full expand of all data
         *
         *
         */
        $allDivisions = $this->expandProperties($allDivisions);

        foreach ($allDivisions as $key => $properties) {
            if (!empty($properties['Parents'])) {
                $groups[$properties['Parents']][] = $key;
            }
        }

        /*
        //sort
        $sort1  = array();
        $sort2  = array();
        $sort3  = array();
        $sort4  = array();
        $sort5  = array();
        $sort7  = array();
        $sort8  = array();
        $sort10 = array();

        foreach ($allDivisions as $key => $properties) {
            $x = 0;

            if ('DefaultProperties' === $key) {
                $x = -1;
            }

            if ('*' === $key) {
                $x = 11;
            }

            $sort1[$key] = $x;
            $sort5[$key] = (isset($properties['sortIndex']) ? $properties['sortIndex'] : 0 );

            if (!empty($properties['Browser'])) {
                $sort2[$key] = strtolower($properties['Browser']);
            } else {
                $sort2[$key] = '';
            }

            if (!empty($properties['Version'])) {
                $sort3[$key]  = (float)$properties['Version'];
            } else {
                $sort3[$key]  = 0.0;
            }

            if (!empty($properties['Platform'])) {
                $sort4[$key] = strtolower($properties['Platform']);
            } else {
                $sort4[$key] = '';
            }

            $parents = (empty($properties['Parents']) ? '' : $properties['Parents'] . ',') . $key;

            if (!empty($groups[$parents])) {
                $group    = $parents;
                $subgroup = 0;
            } elseif (!empty($properties['Parents'])) {
                $group    = $properties['Parents'];
                $subgroup = 1;
            } else {
                $group    = '';
                $subgroup = 2;
            }

            $sort7[$key]  = strtolower($group);
            $sort8[$key]  = strtolower($subgroup);
            $sort10[$key] = $key;
        }

        array_multisort(
            $sort1, SORT_ASC,
            $sort5, SORT_ASC, // sortIndex
            $sort7, SORT_ASC, // Parents
            $sort8, SORT_ASC, // Parent first
            $sort2, SORT_ASC, // Browser Name
            $sort3, SORT_NUMERIC, // Browser Version::Major
            $sort4, SORT_ASC, // Platform Name
            $sort10, SORT_ASC,
            $allDivisions
        );
        /**/

        return $this->render($allDivisions, $output, $allProperties);
    }

    /**
     * Generate the header
     *
     * @return string
     */
    protected function renderHeader()
    {
        $version = $this->getDataCollection()->getVersion();
        $dateUtc = $this->getDataCollection()->getGenerationDate()->format('l, F j, Y \a\t h:i A T');
        $date = $this->getDataCollection()->getGenerationDate()->format('r');

        $header = "";

        $header .= ";;; Provided courtesy of http://tempdownloads.browserscap.com/\n";
        $header .= ";;; Created on {$dateUtc}\n\n";

        $header .= ";;; Keep up with the latest goings-on with the project:\n";
        $header .= ";;; Follow us on Twitter <https://twitter.com/browscap>, or...\n";
        $header .= ";;; Like us on Facebook <https://facebook.com/browscap>, or...\n";
        $header .= ";;; Collaborate on GitHub <https://github.com/GaryKeith/browscap>, or...\n";
        $header .= ";;; Discuss on Google Groups <https://groups.google.com/d/forum/browscap>.\n\n";

        $header .= ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version\n\n";

        $header .= "[GJK_Browscap_Version]\n";
        $header .= "Version={$version}\n";
        $header .= "Released={$date}\n\n";

        return $header;
    }

    /**
     * Render a single division
     *
     * @param array  $userAgents
     * @param string $majorVer
     * @param string $minorVer
     *
     * @return array
     */
    protected function parseDivision(array $userAgents, $majorVer, $minorVer)
    {
        $output = array();

        foreach ($userAgents as $uaData) {
            $output += $this->parseUserAgent($uaData, $majorVer, $minorVer);
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param array  $uaData
     * @param string $majorVer
     * @param string $minorVer
     *
     * @internal param array $uaData
     * @return array
     */
    protected function parseUserAgent(array $uaData, $majorVer, $minorVer)
    {
        $output = array($uaData['userAgent'] => $this->parseProperties($uaData['properties'], $majorVer, $minorVer));

        if (isset($uaData['children']) && is_array($uaData['children'])) {
            if (isset($uaData['children']['match'])) {
                $output += $this->parseChildren($uaData['userAgent'], $uaData['children'], $majorVer, $minorVer);
            } else {
                foreach ($uaData['children'] as $child) {
                    if (!is_array($child) || !isset($child['match'])) {
                        continue;
                    }

                    $co = $this->parseChildren($uaData['userAgent'], $child, $majorVer, $minorVer);
                    $output += $co;
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
     * @return string
     */
    protected function parseChildren($ua, array $uaDataChild, $majorVer, $minorVer)
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
    protected function parseProperties(array $properties, $majorVer, $minorVer)
    {
        $output = array();
        foreach ($properties as $property => $value) {
            if ((!$this->includeExtraProperties) && $this->isExtraProperty($property)) {
                continue;
            }

            $value = str_replace(
                array('#MAJORVER#', '#MINORVER#'),
                array($majorVer, $minorVer),
                $value
            );

            $output[$property] = $value;
        }
        return $output;
    }

    /**
     * Get the type of a property
     *
     * @param string $propertyName
     * @throws \Exception
     * @return string
     */
    public function getPropertyType($propertyName)
    {
        switch ($propertyName) {
            case 'Comment':
            case 'Browser':
            case 'Platform':
            case 'Platform_Description':
            case 'Device_Name':
            case 'Device_Maker':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Description':
                return 'string';
            case 'Parent':
            case 'Platform_Version':
            case 'RenderingEngine_Version':
                return 'generic';
            case 'Version':
            case 'MajorVer':
            case 'MinorVer':
            case 'CssVersion':
            case 'AolVersion':
                return 'number';
            case 'Alpha':
            case 'Beta':
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
            case 'isSyndicationReader':
            case 'Crawler':
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
    public function isExtraProperty($propertyName)
    {
        switch ($propertyName) {
            case 'Device_Name':
            case 'Device_Maker':
            case 'Platform_Description':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Version':
            case 'RenderingEngine_Description':
                return true;
            default:
                return false;
        }
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
     * renders all found useragents into a string
     *
     * @param $allDivisions
     * @param $output
     * @param $allProperties
     *
     * @return string
     */
    private function render($allDivisions, $output, $allProperties)
    {
        foreach ($allDivisions as $key => $properties) {
            if (!isset($properties['Version'])) {
                continue;
            }

            if (!isset($properties['Parent'])
                && 'DefaultProperties' !== $key
                && '*' !== $key
            ) {
                continue;
            }

            if ('DefaultProperties' !== $key && '*' !== $key) {
                if (!isset($allDivisions[$properties['Parent']])) {
                    continue;
                }

                $parent = $allDivisions[$properties['Parent']];
            } else {
                $parent = array();
            }

            if (isset($parent['Version'])) {
                $completeVersions = explode('.', $parent['Version'], 2);

                $parent['MajorVer'] = (string) $completeVersions[0];

                if (isset($completeVersions[1])) {
                    $parent['MinorVer'] = (string) $completeVersions[1];
                } else {
                    $parent['MinorVer'] = 0;
                }
            }

            $propertiesToOutput = $properties;

            foreach ($propertiesToOutput as $property => $value) {
                if (!isset($parent[$property])) {
                    continue;
                }

                $parentProperty = $parent[$property];

                switch ((string) $parentProperty) {
                    case 'true':
                        $parentProperty = true;
                        break;
                    case 'false':
                        $parentProperty = false;
                        break;
                    default:
                        $parentProperty = trim($parentProperty);
                        break;
                }

                if ($parentProperty != $value) {
                    continue;
                }

                unset($propertiesToOutput[$property]);
            }

            // create output - php

            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $output .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $key . "\n\n";
            }

            $output .= '[' . $key . ']' . "\n";

            foreach ($allProperties as $property) {
                if (!isset($propertiesToOutput[$property])) {
                    continue;
                    //$value    = '!! not set';
                } else {

                    $value = $propertiesToOutput[$property];
                }
                $valuePhp = $value;

                switch ($this->getPropertyType($property)) {
                    case 'string':
                        if ($this->quoteStringProperties) {
                            $valuePhp = '"' . $value . '"';
                        }
                        break;
                    case 'boolean':
                        if (true === $value || $value === 'true') {
                            $valuePhp = 'true';
                        } elseif (false === $value || $value === 'false') {
                            $valuePhp = 'false';
                        }
                        break;
                    case 'generic':
                    case 'number':
                    default:
                        // nothing t do here
                        break;
                }

                $output .= $property . '=' . $valuePhp . "\n";
            }

            $output .= "\n";
        }
        return $output;
    }
}

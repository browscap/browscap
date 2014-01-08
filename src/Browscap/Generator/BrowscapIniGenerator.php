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
                    $dots = explode($version, '.', 2);

                    $majorVer = $dots[0];
                    $minorVer = (isset($dots[1]) ? $dots[1] : 0);

                    $tmp = json_encode($division['userAgents']);
                    $tmp = str_replace(
                        array('#MAJORVER#', '#MINORVER#'),
                        array($majorVer, $minorVer),
                        $tmp
                    );

                    $userAgents = json_decode($tmp, true);

                    $allDivisions += $this->renderDivision($userAgents);
                }
            } else {
                $allDivisions += $this->renderDivision($division['userAgents']);
            }
        }

        $allProperties = array_keys($allDivisions['DefaultProperties']);

        /*
         * full expand of all data
         *
         *
         */

        foreach ($allDivisions as $key => $properties) {
            if (!isset($properties['Parent'])) {
                continue;
            }

            $userAgent = $key;
            $parents   = array($userAgent);

            while (isset($allDivisions[$userAgent]['Parent'])) {
                if ($userAgent === $allDivisions[$userAgent]['Parent']) {
                    break;
                }

                $parents[] = $allDivisions[$userAgent]['Parent'];
                $userAgent = $allDivisions[$userAgent]['Parent'];
            }
            unset($userAgent);

            $parents     = array_reverse($parents);
            $browserData = array();

            foreach ($parents as $parent) {
                if (!isset($allDivisions[$parent])) {
                    continue;
                }

                if (!is_array($allDivisions[$parent])) {
                    continue;
                }

                $browserData = array_merge($browserData, $allDivisions[$parent]);
            }

            array_pop($parents);
            $browserData['Parents'] = implode(',', $parents);

            foreach ($browserData as $propertyName => $propertyValue) {
                switch ($propertyValue) {
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

            if (!isset($properties['Version']) || !isset($properties['Browser'])) {
                continue;
            }

            $allDivisions[$key] = $properties;

            $completeVersions = explode('.', $properties['Version'], 2);

            $properties['MajorVer'] = (int)$completeVersions[0];

            if (isset($completeVersions[1])) {
                $properties['MinorVer'] = $completeVersions[1];
            } else {
                $properties['MinorVer'] = 0;
            }

            $properties['isMobileDevice']        = false;
            $properties['Platform_Description']  = '';

            if ('DefaultProperties' !== $key
                && '*' !== $key
            ) {
                switch ($properties['Platform']) {
                    case 'RIM OS':
                        $properties['isMobileDevice']        = true;
                        break;
                    case 'WinMobile':
                    case 'Windows Mobile OS':
                        $properties['isMobileDevice']        = true;
                        $properties['Platform']              = 'Windows Mobile OS';

                        break;
                    case 'Windows Phone OS':
                        $properties['isMobileDevice']        = true;
                        break;
                    case 'Symbian OS':
                    case 'SymbianOS':
                        $properties['isMobileDevice']        = true;
                        break;
                    case 'iOS':
                        $properties['isMobileDevice']        = true;
                        break;
                    case 'Android':
                    case 'Dalvik':
                        $properties['isMobileDevice']        = true;
                        break;
                    default:
                        $properties['Platform_Description'] = '';
                        break;
                }
            }

            $allDivisions[$key] = $properties;
        }

        foreach ($allDivisions as $key => $properties) {
            if (!empty($properties['Parents'])) {
                $groups[$properties['Parents']][] = $key;
            }
        }

        //sort
        $sort1  = array();
        $sort2  = array();
        $sort3  = array();
        $sort4  = array();
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
            $sort7, SORT_ASC, // Parents
            $sort8, SORT_ASC, // Parent first
            $sort2, SORT_ASC, // Browser Name
            $sort3, SORT_NUMERIC, // Browser Version::Major
            $sort4, SORT_ASC, // Platform Name
            $sort10, SORT_ASC,
            $allDivisions
        );

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

            if ('DefaultProperties' !== $key
                && '*' !== $key
            ) {
                if (!isset($allDivisions[$properties['Parent']])) {
                    continue;
                }

                $parent = $allDivisions[$properties['Parent']];
            } else {
                $parent = array();
            }

            $propertiesToOutput = $properties;

            foreach ($propertiesToOutput as $property => $value) {
                if (!isset($parent[$property])) {
                    continue;
                }

                if ($parent[$property] != $value) {
                    continue;
                }

                unset($propertiesToOutput[$property]);
            }

            // create output - php

            if ('DefaultProperties' == $key
                || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $output .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;' . "\n" . '; '
                    . $key . "\n" . ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;'
                    . "\n\n"
                ;
            }

            if ('DefaultProperties' != $key
                && !empty($properties['Parent'])
                && 'DefaultProperties' != $properties['Parent']
            ) {
                if (false !== strpos($key, ' on ')) {
                    $output .= '; ' . $key . "\n\n";
                } else {
                    $output .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $key . "\n\n";
                }
            }

            $output .= '[' . $key . ']' . "\n";

            foreach ($allProperties as $property) {
                if (!isset($propertiesToOutput[$property])) {
                    continue;
                }

                $value = $propertiesToOutput[$property];

                if (true === $value || $value === 'true') {
                    $valuePhp = 'true';
                } elseif (false === $value || $value === 'false') {
                    $valuePhp = 'false';
                } elseif ('0' === $value
                    || 'Parent' === $property
                    || 'Version' === $property
                    || 'MajorVer' === $property
                    || 'MinorVer' === $property
                    || 'RenderingEngine_Version' === $property
                    || 'Platform_Version' === $property
                    || 'Browser_Version' === $property
                ) {
                    $valuePhp = $value;
                } else {
                    $valuePhp = '"' . $value . '"';
                }

                $output .= $property . '=' . $valuePhp . "\n";
            }

            $output .= "\n";
        }

        return $output;
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
     * @param array $userAgents
     * @return array
     */
    protected function renderDivision(array $userAgents)
    {
        $output = array();

        foreach ($userAgents as $uaData) {
            $output += $this->renderUserAgent($uaData);
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param array $uaData
     * @return array
     */
    protected function renderUserAgent(array $uaData)
    {
        $ua = $uaData['userAgent'];

        $output = array($uaData['userAgent'] => $this->renderProperties($uaData['properties']));

        if (isset($uaData['children'])) {
            if (is_array($uaData['children']) && is_numeric(array_keys($uaData['children'])[0])) {

                foreach ($uaData['children'] as $child) {
                    if (!is_array($child)) {
                        continue;
                    }

                    $output += $this->renderChildren($ua, $child);
                }
            } elseif (is_array($uaData['children'])) {
                $output += $this->renderChildren($ua, $uaData['children']);
            }
        }

        return $output;
    }

    /**
     * Render the children section in a single User Agent block
     *
     * @param string $ua
     * @param array  $uaDataChild
     * @return string
     */
    protected function renderChildren($ua, array $uaDataChild)
    {
        $uaBase = $uaDataChild['match'];
        $output = array();

        // @todo This needs work here. What if we specify platforms AND versions?
        // We need to make it so it does as many permutations as necessary.
        if (isset($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $platformData = $this->getDataCollection()->getPlatform($platform);

                $ua         = '[' . str_replace('#PLATFORM#', $platformData['match'], $uaBase) . ']';
                $properties = $this->renderProperties(['Parent' => $ua]);

                if (isset($uaDataChild['properties'])
                    && is_array($uaDataChild['properties'])
                ) {
                    $properties += $this->renderProperties(
                        $uaDataChild['properties'] + $platformData['properties']
                    );
                } else {
                    $properties +=  $this->renderProperties($platformData['properties']);
                }

                $output[$ua] = $properties;
            }
        } else {
            $properties = $this->renderProperties(['Parent' => $ua]);

            if (isset($uaDataChild['properties'])
                && is_array($uaDataChild['properties'])
            ) {
                $properties += $this->renderProperties($uaDataChild['properties']);
            }

            $output[$uaBase] = $properties;
        }

        return $output;
    }

    /**
     * Render the properties of a single User Agent
     *
     * @param array $properties
     * @return array
     */
    protected function renderProperties(array $properties)
    {
        $output = array();
        foreach ($properties as $property => $value) {
            if ((!$this->includeExtraProperties) && $this->isExtraProperty($property)) {
                continue;
            }

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
}

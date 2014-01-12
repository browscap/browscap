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
        $this->quoteStringProperties  = false;
        $this->includeExtraProperties = true;
        $this->liteOnly               = false;
    }

    /**
     * Set the data collection
     *
     * @param  \Browscap\Generator\DataCollection $collection
     *
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
     * @param  boolean $quoteStringProperties
     * @param  boolean $includeExtraProperties
     * @param  boolean $liteOnly
     *
     * @return \Browscap\Generator\BrowscapIniGenerator
     */
    public function setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $this->quoteStringProperties  = (bool) $quoteStringProperties;
        $this->includeExtraProperties = (bool) $includeExtraProperties;
        $this->liteOnly               = (bool) $liteOnly;

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

        foreach ($this->getDataCollection()
                     ->getDivisions() as $division) {
            if ($division['division'] == 'Browscap Version') {
                continue;
            }

            if ($this->liteOnly && (!isset($division['lite']) || $division['lite'] == false)) {
                continue;
            }

            if (isset($division['versions']) && is_array($division['versions'])) {
                foreach ($division['versions'] as $version) {
                    $dotPos = strpos($version, ".");
                    if ($dotPos === false) {
                        $majorVer = $version;
                        $minorVer = '0';
                    } else {
                        $majorVer = substr($version, 0, $dotPos);
                        $minorVer = substr($version, ($dotPos + 1));
                    }

                    $tmp = json_encode($division['userAgents']);
                    $tmp = str_replace('#MAJORVER#', $majorVer, $tmp);
                    $tmp = str_replace('#MINORVER#', $minorVer, $tmp);

                    $userAgents = json_decode($tmp, true);

                    $divisionName = str_replace('#MAJORVER#', $majorVer, $division['division']);
                    $divisionName = str_replace('#MINORVER#', $minorVer, $divisionName);

                    $output .= $this->renderDivision($userAgents, $divisionName);
                }
            } else {
                $output .= $this->renderDivision($division['userAgents'], $division['division']);
            }
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
        $version = $this->getDataCollection()
            ->getVersion();
        $dateUtc = $this->getDataCollection()
            ->getGenerationDate()
            ->format('l, F j, Y \a\t h:i A T');
        $date    = $this->getDataCollection()
            ->getGenerationDate()
            ->format('r');

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
     * @param  array  $userAgents
     * @param  string $divisionName
     *
     * @return string
     */
    protected function renderDivision(array $userAgents, $divisionName)
    {
        $output = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $divisionName . "\n\n";

        foreach ($userAgents as $uaData) {
            $output .= $this->renderUserAgent($uaData);
        }

        return $output;
    }

    /**
     * Render a single User Agent block
     *
     * @param  array $uaData
     *
     * @return string
     */
    protected function renderUserAgent(array $uaData)
    {
        $ua = $uaData['userAgent'];

        $output = "[" . $ua . "]\n";
        $output .= $this->renderProperties($uaData['properties']);
        $output .= "\n";

        if (isset($uaData['children'])) {
            if (is_array($uaData['children']) && is_numeric(array_keys($uaData['children'])[0])) {

                foreach ($uaData['children'] as $child) {
                    if (!is_array($child)) {
                        continue;
                    }

                    $output .= $this->renderChildren($ua, $child);
                }
            } elseif (is_array($uaData['children'])) {
                $output .= $this->renderChildren($ua, $uaData['children']);
            }
        }

        return $output;
    }

    /**
     * Render the children section in a single User Agent block
     *
     * @param  string $ua
     * @param  array  $uaDataChild
     *
     * @return string
     */
    protected function renderChildren($ua, array $uaDataChild)
    {
        $uaBase = $uaDataChild['match'];
        $output = '';

        // @todo This needs work here. What if we specify platforms AND versions?
        // We need to make it so it does as many permutations as necessary.
        if (isset($uaDataChild['platforms'])) {
            foreach ($uaDataChild['platforms'] as $platform) {
                $platformData = $this->getDataCollection()
                    ->getPlatform($platform);

                $output .= '[' . str_replace('#PLATFORM#', $platformData['match'], $uaBase) . "]\n";
                $output .= $this->renderProperties(['Parent' => $ua]);

                if (isset($uaDataChild['properties']) && is_array($uaDataChild['properties'])
                ) {
                    $output .= $this->renderProperties(
                        $uaDataChild['properties'] + $platformData['properties']
                    );
                } else {
                    $output .= $this->renderProperties($platformData['properties']);
                }

                $output .= "\n";
            }
        } else {
            $output .= '[' . $uaBase . "]\n";
            $output .= $this->renderProperties(['Parent' => $ua]);

            if (isset($uaDataChild['properties']) && is_array($uaDataChild['properties'])
            ) {
                $output .= $this->renderProperties($uaDataChild['properties']);
            }

            $output .= "\n";
        }

        return $output;
    }

    /**
     * Render the properties of a single User Agent
     *
     * @param  array $properties
     *
     * @return string
     */
    protected function renderProperties(array $properties)
    {
        $output = '';
        foreach ($properties as $property => $value) {
            if ((!$this->includeExtraProperties) && $this->isExtraProperty($property)) {
                continue;
            }

            if ($this->quoteStringProperties && $this->getPropertyType($property) == 'string') {
                $format = "%s=\"%s\"\n";
            } else {
                $format = "%s=%s\n";
            }

            $output .= sprintf($format, $property, $value);
        }

        return $output;
    }

    /**
     * Get the type of a property
     *
     * @param  string $propertyName
     *
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
     * @param  string $propertyName
     *
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

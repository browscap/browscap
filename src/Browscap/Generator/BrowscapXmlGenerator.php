<?php

namespace Browscap\Generator;

class BrowscapXmlGenerator implements GeneratorInterface
{
    /**
     * @var bool
     */
    private $quoteStringProperties;

    /**
     * @var bool
     */
    private $includeExtraProperties;

    /**
     * @var bool
     */
    private $liteOnly;

    /**
     * @var array
     */
    private $collectionData;

    /**
     * @var array
     */
    private $comments = array();

    /**
     * @var array
     */
    private $versionData = array();

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
     * @param array $collectionData
     * @return \Browscap\Generator\BrowscapIniGenerator
     */
    public function setCollectionData(array $collectionData)
    {
        $this->collectionData = $collectionData;
        return $this;
    }

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return array
     */
    public function getCollectionData()
    {
        if (!isset($this->collectionData)) {
            throw new \LogicException("Data collection has not been set yet - call setDataCollection");
        }

        return $this->collectionData;
    }

    /**
     * @param array $comments
     *
     * @return \Browscap\Generator\BrowscapIniGenerator
     */
    public function setComments(array $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $versionData
     *
     * @return \Browscap\Generator\BrowscapIniGenerator
     */
    public function setVersionData(array $versionData)
    {
        $this->versionData = $versionData;

        return $this;
    }

    /**
     * @return array
     */
    public function getVersionData()
    {
        return $this->versionData;
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
        return $this->render(
            $this->collectionData,
            $this->renderHeader(),
            array_keys(array('Parent' => '') + $this->collectionData['DefaultProperties'])
        );
    }

    /**
     * Generate the header
     *
     * @return string
     */
    private function renderHeader()
    {
        $header = '<comments>' . "\n";

        foreach ($this->getComments() as $comment) {
            $header .= '<comment>' . $comment . '</comment>' . "\n";
        }

        $header .= '</comments>' . "\n";
        $header .= '<gjk_browscap_version>' . "\n";

        $versionData = $this->getVersionData();

        $header .= '<item name="Version" value="' . $versionData['version'] . '" />' . "\n";
        $header .= '<item name="Released" value="' . $versionData['released'] . '" />' . "\n";
        $header .= '</gjk_browscap_version>' . "\n";

        return $header;
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
        $output = '<?xml version="1.0" encoding="utf-8" ?>' . "\n"
            . '<browsercaps>' . "\n"
            . $output
            . '<browsercapitems>' . "\n";

        $counter = 1;

        foreach ($allDivisions as $key => $properties) {
            $counter++;

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

            // create output - xml

            $output .= '<browscapitem name="' . $key . '">' . "\n";
            $output .= '<item name="PropertyName" value="' . $key . '" />' . "\n";
            $output .= '<item name="AgentID" value="' . $counter . '" />' . "\n";

            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $masterParent = 'true';
            } else {
                $masterParent = 'false';
            }

            $output .= '<item name="MasterParent" value="' . $masterParent . '" />' . "\n";

            $output .= '<item name="LiteMode" value="'
                . ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true') . '" />' . "\n";

            foreach ($allProperties as $property) {
                if (!isset($properties[$property])) {
                    continue;
                }

                if ('lite' === $property || 'sortIndex' === $property || 'Parents' === $property) {
                    continue;
                }

                $value       = $properties[$property];
                $valueOutput = $value;

                switch ($this->getPropertyType($property)) {
                    case 'boolean':
                        if (true === $value || $value === 'true') {
                            $valueOutput = 'true';
                        } elseif (false === $value || $value === 'false') {
                            $valueOutput = 'false';
                        }
                        break;
                    case 'string':
                    case 'generic':
                    case 'number':
                    default:
                        // nothing t do here
                        break;
                }

                if ('unknown' === $valueOutput) {
                    $valueOutput = '';
                }

                $output .= '<item name="' . $property . '" value="' . $valueOutput . '" />' . "\n";
            }

            $output .= '</browscapitem>' . "\n";
        }

        return $output;
    }
}

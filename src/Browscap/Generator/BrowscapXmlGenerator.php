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
            $header .= '<comment><![CDATA[' . $comment . ']]></comment>' . "\n";
        }

        $header .= '</comments>' . "\n";
        $header .= $this->renderVersion();

        return $header;
    }

    /**
     * Quick nasty method that escapes the main XML entities
     *
     * @param string $value
     * @return string
     */
    protected function escapeXml($value)
    {
        $translations = array(
            '&' => '&amp;',
            '"' => '&quot;',
            "'" => '&apos;',
            '<' => '&lt;',
            '>' => '&gt;',
        );

        return strtr($value, $translations);
    }

    /**
     * renders all found useragents into a string
     *
     * @param array  $allDivisions
     * @param string $output
     * @param array  $allProperties
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

            $output .= '<browscapitem name="' . $this->escapeXml($key) . '">' . "\n";
            $output .= '<item name="PropertyName" value="' . $this->escapeXml($key) . '" />' . "\n";
            $output .= '<item name="AgentID" value="' . $counter . '" />' . "\n";

            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $masterParent = 'true';
            } else {
                $masterParent = 'false';
            }

            $output .= '<item name="MasterParent" value="' . $this->escapeXml($masterParent) . '" />' . "\n";

            $output .= '<item name="LiteMode" value="'
                . ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true') . '" />' . "\n";

            foreach ($allProperties as $property) {
                if (!isset($properties[$property])) {
                    continue;
                }

                if (in_array($property, array('lite', 'sortIndex', 'Parents', 'division'))) {
                    continue;
                }

                $value       = $properties[$property];
                $valueOutput = $value;

                switch (CollectionParser::getPropertyType($property)) {
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

                $output .= '<item name="' . $property . '" value="' . $this->escapeXml($valueOutput) . '" />' . "\n";
            }

            $output .= '</browscapitem>' . "\n";
        }

        $output .= '</browsercapitems>' . "\n";
        $output .= '</browsercaps>' . "\n\n";

        return $output;
    }

    /**
     * renders the version information
     *
     * @return string
     */
    private function renderVersion()
    {
        $header = '<gjk_browscap_version>' . "\n";

        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $header .= '<item name="Version" value="' . $versionData['version'] . '" />' . "\n";
        $header .= '<item name="Released" value="' . $versionData['released'] . '" />' . "\n";
        $header .= '</gjk_browscap_version>' . "\n";

        return $header;
    }
}

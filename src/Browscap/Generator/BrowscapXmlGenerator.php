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
            array_keys(array('Parent' => '') + $this->collectionData['DefaultProperties'])
        );
    }

    /**
     * Generate the header
     *
     * @param \DOMDocument $dom
     *
     * @return \DOMElement
     */
    private function renderHeader(\DOMDocument $dom)
    {
        $comments = $dom->createElement('comments');

        foreach ($this->getComments() as $text) {
            $comment = $dom->createElement('comment');
            $cdata   = $dom->createCDATASection($text);
            $comment->appendChild($cdata);
            $comments->appendChild($comment);
        }

        return $comments;
    }

    /**
     * renders all found useragents into a string
     *
     * @param array  $allDivisions
     * @param array  $allProperties
     *
     * @return string
     */
    private function render(array $allDivisions, array $allProperties)
    {
        $dom      = new \DOMDocument('1.0', 'utf-8');
        $xmlRoot  = $dom->createElement('browsercaps');

        $xmlRoot->appendChild($this->renderHeader($dom));
        $xmlRoot->appendChild($this->renderVersion($dom));

        $items = $dom->createElement('browsercapitems');

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

            $browscapitem = $dom->createTextNode('browscapitem');
            $name = $dom->createAttribute('name');
            $name->value = htmlentities($key);
            $browscapitem->appendChild($name);

            $item = $dom->createTextNode('item');
            $name = $dom->createAttribute('name');
            $name->value = 'PropertyName';
            $item->appendChild($name);
            $value = $dom->createAttribute('value');
            $value->value = htmlentities($key);
            $item->appendChild($value);
            $browscapitem->appendChild($item);

            $item = $dom->createTextNode('item');
            $name = $dom->createAttribute('name');
            $name->value = 'AgentID';
            $item->appendChild($name);
            $value = $dom->createAttribute('value');
            $value->value = htmlentities($counter);
            $item->appendChild($value);
            $browscapitem->appendChild($item);

            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $masterParent = 'true';
            } else {
                $masterParent = 'false';
            }

            $item = $dom->createTextNode('item');
            $name = $dom->createAttribute('name');
            $name->value = 'MasterParent';
            $item->appendChild($name);
            $value = $dom->createAttribute('value');
            $value->value = $masterParent;
            $item->appendChild($value);
            $browscapitem->appendChild($item);

            $item = $dom->createTextNode('item');
            $name = $dom->createAttribute('name');
            $name->value = 'LiteMode';
            $item->appendChild($name);
            $value = $dom->createAttribute('value');
            $value->value = ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true');
            $item->appendChild($value);
            $browscapitem->appendChild($item);

            foreach ($allProperties as $property) {
                if (in_array($property, array('lite', 'sortIndex', 'Parents', 'division'))) {
                    continue;
                }

                if (!isset($properties[$property])) {
                    $value = '';
                } else {
                    $value = $properties[$property];
                }

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

                $item = $dom->createTextNode('item');
                $name = $dom->createAttribute('name');
                $name->value = htmlentities($property);
                $item->appendChild($name);
                $value = $dom->createAttribute('value');
                $value->value = htmlentities($valueOutput);
                $item->appendChild($value);
                $browscapitem->appendChild($item);
            }

            $items->appendChild($browscapitem);
        }

        $xmlRoot->appendChild($items);
        $dom->appendChild($xmlRoot);

        return str_replace(
            array('>', ']]>' . PHP_EOL, '<comment>' . PHP_EOL),
            array('>' . PHP_EOL, ']]>', '<comment>'),
            $dom->saveXML()
        );
    }

    /**
     * renders the version information
     *
     * @param \DOMDocument $dom
     *
     * @return \DOMElement
     */
    private function renderVersion(\DOMDocument $dom)
    {
        $version     = $dom->createElement('gjk_browscap_version');
        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $item = $dom->createElement('item');
        $name = $dom->createAttribute('name');
        $name->value = 'Version';
        $value = $dom->createAttribute('value');
        $value->value = $versionData['version'];
        $item->appendChild($name);
        $item->appendChild($value);
        $version->appendChild($item);

        $item = $dom->createElement('item');
        $name = $dom->createAttribute('name');
        $name->value = 'Released';
        $value = $dom->createAttribute('value');
        $value->value = $versionData['released'];
        $item->appendChild($name);
        $item->appendChild($value);
        $version->appendChild($item);

        return $version;
    }
}

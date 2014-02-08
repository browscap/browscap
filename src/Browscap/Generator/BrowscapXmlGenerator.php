<?php

namespace Browscap\Generator;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

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
     * @return \Browscap\Generator\BrowscapXmlGenerator
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
     * @param string[] $comments
     *
     * @return \Browscap\Generator\BrowscapXmlGenerator
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
     * @return \Browscap\Generator\BrowscapXmlGenerator
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
     *
     * @return \Browscap\Generator\BrowscapXmlGenerator
     */
    public function setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $this->quoteStringProperties = (bool)$quoteStringProperties;
        $this->includeExtraProperties = (bool)$includeExtraProperties;
        $this->liteOnly = (bool)$liteOnly;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\BrowscapXmlGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

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
        $this->log('rendering comments');
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
     * @param array[] $allDivisions
     * @param array[] $allProperties
     *
     * @return string
     */
    private function render(array $allDivisions, array $allProperties)
    {
        $this->log('rendering XML structure');

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xmlRoot   = $dom->createElement('browsercaps');
        $xmlRoot->appendChild($this->renderHeader($dom));
        $xmlRoot->appendChild($this->renderVersion($dom));

        $items = $dom->createElement('browsercapitems');

        $counter = 1;

        $this->log('rendering all divisions');
        foreach ($allDivisions as $key => $properties) {
            $this->log('rendering division "' . $properties['division'] . '" - "' . $key . '"');

            $counter++;

            if (!isset($properties['Version'])) {
                $this->log('skipping division "' . $properties['division'] . '" - version information is missing');
                continue;
            }

            if (!isset($properties['Parent'])
                && 'DefaultProperties' !== $key
                && '*' !== $key
            ) {
                $this->log('skipping division "' . $properties['division'] . '" - no parent defined');
                continue;
            }

            if ('DefaultProperties' !== $key && '*' !== $key) {
                if (!isset($allDivisions[$properties['Parent']])) {
                    $this->log('skipping division "' . $properties['division'] . '" - parent not found');
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
            $browscapitem = $dom->createElement('browscapitem');
            $name = $dom->createAttribute('name');
            $name->value = htmlentities($key);
            $browscapitem->appendChild($name);

            $this->createItem($dom, $browscapitem, 'PropertyName', $key);
            $this->createItem($dom, $browscapitem, 'AgentID', $counter);

            if ('DefaultProperties' === $key
                || '*' === $key || empty($properties['Parent'])
                || 'DefaultProperties' == $properties['Parent']
            ) {
                $masterParent = 'true';
            } else {
                $masterParent = 'false';
            }

            $this->createItem($dom, $browscapitem, 'MasterParent', $masterParent);

            $valueOutput = ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true');
            $this->createItem($dom, $browscapitem, 'LiteMode', $valueOutput);

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

                $this->createItem($dom, $browscapitem, $property, $valueOutput);
            }

            $items->appendChild($browscapitem);
        }

        $xmlRoot->appendChild($items);
        $dom->appendChild($xmlRoot);

        return str_replace('  ', '', $dom->saveXML());
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
        $this->log('rendering version information');
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

    /**
     * @param \DOMDocument $dom
     * @param \DOMNode     $browscapitem
     * @param string       $property
     * @param mixed        $valueOutput
     */
    private function createItem(\DOMDocument $dom, \DOMNode $browscapitem, $property, $valueOutput)
    {
        $item        = $dom->createElement('item');

        $name        = $dom->createAttribute('name');
        $name->value = htmlentities($property);
        $item->appendChild($name);

        $value        = $dom->createAttribute('value');
        $value->value = htmlentities($valueOutput);
        $item->appendChild($value);

        $browscapitem->appendChild($item);
    }

    /**
     * @param string $message
     *
     * @return \Browscap\Generator\BuildGenerator
     */
    private function log($message)
    {
        if (null === $this->logger) {
            return $this;
        }

        $this->logger->log(Logger::DEBUG, $message);

        return $this;
    }
}

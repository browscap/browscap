<?php

namespace Browscap\Generator;

use Browscap\Helper\CollectionParser;
use XMLWriter;

/**
 * Class BrowscapXmlGenerator
 *
 * @package Browscap\Generator
 */
class BrowscapXmlGenerator extends AbstractGenerator
{
    private $file = '';

    /**
     * Constructs the XML Generator
     *
     * @param string $file
     *
     * @return BrowscapXmlGenerator
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        $this->logger->debug('build output for xml file');

        return $this->render(
            $this->collectionData,
            array_keys(array('Parent' => '') + $this->collectionData['DefaultProperties'])
        );
    }

    /**
     * Generate the header
     *
     * @param \XMLWriter $xmlWriter
     */
    private function renderHeader(XMLWriter $xmlWriter)
    {
        $this->logger->debug('rendering comments');

        $xmlWriter->startElement('comments');

        foreach ($this->getComments() as $text) {
            $xmlWriter->startElement('comment');
            $xmlWriter->writeCData($text);
            $xmlWriter->endElement();
        }

        $xmlWriter->endElement();
    }

    /**
     * renders the version information
     *
     * @param \XMLWriter $xmlWriter
     */
    private function renderVersion(XMLWriter $xmlWriter)
    {
        $this->logger->debug('rendering version information');

        $xmlWriter->startElement('gjk_browscap_version');
        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $xmlWriter->startElement('item');
        $xmlWriter->writeAttribute('name', 'Version');
        $xmlWriter->writeAttribute('value', $versionData['version']);
        $xmlWriter->endElement();

        $xmlWriter->startElement('item');
        $xmlWriter->writeAttribute('name', 'Released');
        $xmlWriter->writeAttribute('value', $versionData['released']);
        $xmlWriter->endElement();

        $xmlWriter->endElement();
    }

    /**
     * renders all found useragents into a string
     *
     * @param array[] $allDivisions
     * @param array   $allProperties
     *
     * @return string
     */
    private function render(array $allDivisions, array $allProperties)
    {
        $this->logger->debug('rendering XML structure');

        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->setIndent(true);
        $xmlWriter->setIndentString('');
        $xmlWriter->startDocument('1.0', 'UTF-8');
        file_put_contents($this->file, $xmlWriter->flush(true));

        $xmlWriter->startElement('browsercaps');
        $this->renderHeader($xmlWriter);
        file_put_contents($this->file, $xmlWriter->flush(true), FILE_APPEND);

        $this->renderVersion($xmlWriter);
        file_put_contents($this->file, $xmlWriter->flush(true), FILE_APPEND);

        $xmlWriter->startElement('browsercapitems');

        $counter = 1;

        $this->logger->debug('rendering all divisions');

        foreach ($allDivisions as $key => $properties) {
            $this->logger->debug('rendering division "' . $properties['division'] . '" - "' . $key . '"');

            $counter++;

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                $this->logger->debug('first check failed on key "' . $key . '" -> skipped');

                continue;
            }

            if (!in_array($key, array('DefaultProperties', '*'))) {
                $parent = $allDivisions[$properties['Parent']];
            } else {
                $parent = array();
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

            // create output - xml
            $xmlWriter->startElement('browscapitem');
            $xmlWriter->writeAttribute('name', $key);

            foreach ($allProperties as $property) {
                if (!isset($propertiesToOutput[$property])) {
                    continue;
                }

                if (!CollectionParser::isOutputProperty($property)) {
                    continue;
                }

                if (CollectionParser::isExtraProperty($property)) {
                    continue;
                }

                $this->createItem($xmlWriter, $property, $this->formatValue($property, $propertiesToOutput));
            }

            $xmlWriter->endElement(); // browscapitem
            file_put_contents($this->file, $xmlWriter->flush(true), FILE_APPEND);

            unset($propertiesToOutput);
        }

        unset($allDivisions, $allProperties, $counter, $key, $properties, $property);

        $xmlWriter->endElement(); // browsercapitems
        $xmlWriter->endElement(); // browsercaps
        $xmlWriter->endDocument();

        file_put_contents($this->file, $xmlWriter->flush(true), FILE_APPEND);

        return '';
    }

    /**
     * @param \XMLWriter $xmlWriter
     * @param string     $property
     * @param mixed      $valueOutput
     */
    private function createItem(XMLWriter $xmlWriter, $property, $valueOutput)
    {
        $this->logger->debug('create item for property "' . $property . '"');
        $xmlWriter->startElement('item');
        $xmlWriter->writeAttribute('name', $property);
        $xmlWriter->writeAttribute('value', $valueOutput);
        $xmlWriter->endElement();
    }
}

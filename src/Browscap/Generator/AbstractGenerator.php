<?php

namespace Browscap\Generator;

use Psr\Log\LoggerInterface;

/**
 * Class AbstractGenerator
 *
 * @package Browscap\Generator
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    protected $collectionData;

    /**
     * @var array
     */
    protected $comments = array();

    /**
     * @var array
     */
    protected $versionData = array();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * Set the data collection
     *
     * @param array $collectionData
     * @return \Browscap\Generator\AbstractGenerator
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
     * @return \Browscap\Generator\AbstractGenerator
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
     * @return \Browscap\Generator\AbstractGenerator
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
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string  $key
     * @param array   $properties
     * @param array[] $allDivisions
     *
     * @return bool
     */
    protected function firstCheckProperty($key, array $properties, array $allDivisions)
    {
        $this->logger->debug('check if all required propeties are available');
        if (!isset($properties['Version'])) {
            $this->logger->debug('Version is missing');
            return false;
        }

        if (!isset($properties['Parent']) && !in_array($key, array('DefaultProperties', '*'))) {
            $this->logger->debug('Parent property is missing');
            return false;
        }

        if (!in_array($key, array('DefaultProperties', '*')) && !isset($allDivisions[$properties['Parent']])) {
            $this->logger->debug('Parent property is set, but the parent element was not found');
            return false;
        }

        return true;
    }

    /**
     * extracts the minor version and the major version from the complete version
     *
     * @param array &$parent
     */
    protected function extractVersion(array &$parent)
    {
        $this->logger->debug('extract major and minor version parts from version property');
        $completeVersions = explode('.', $parent['Version'], 2);

        $parent['MajorVer'] = (string) $completeVersions[0];

        if (isset($completeVersions[1])) {
            $parent['MinorVer'] = (string) $completeVersions[1];
        } else {
            $parent['MinorVer'] = 0;
        }
    }

    /**
     * @param string $key
     * @param array  $properties
     *
     * @return string
     */
    protected function detectMasterParent($key, array $properties)
    {
        $this->logger->debug('check if the element can be marked as "MasterParent"');
        if (in_array($key, array('DefaultProperties', '*'))
            || empty($properties['Parent'])
            || 'DefaultProperties' == $properties['Parent']
        ) {
            $masterParent = 'true';
        } else {
            $masterParent = 'false';
        }

        return $masterParent;
    }

    /**
     * formats the value for the CSV and the XML output
     *
     * @param string $property
     * @param array  $properties
     *
     * @return string
     */
    protected function formatValue($property, array $properties)
    {
        $value = '';

        if (!isset($properties[$property])) {

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

        return $valueOutput;
    }
}

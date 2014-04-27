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
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @return bool
     */
    protected function firstCheckProperty($key, array $properties, array $allDivisions)
    {
        $this->logger->debug('check if all required propeties are available');

        if (!isset($properties['Version'])) {
            throw new \UnexpectedValueException('Version property not found for key "' . $key . '"');
        }

        if (!isset($properties['Parent']) && !in_array($key, array('DefaultProperties', '*'))) {
            throw new \UnexpectedValueException('Parent property is missing for key "' . $key . '"');
        }

        if (!in_array($key, array('DefaultProperties', '*')) && !isset($allDivisions[$properties['Parent']])) {
            throw new \UnexpectedValueException(
                'Parent "' . $properties['Parent'] . '" not found for key "' . $key . '"'
            );
        }

        if (!isset($properties['Device_Type'])) {
            throw new \InvalidArgumentException('property "Device_Type" is missing for key "' . $key . '"');
        }

        if (!isset($properties['isTablet'])) {
            throw new \InvalidArgumentException('property "isTablet" is missing for key "' . $key . '"');
        }

        if (!isset($properties['isMobileDevice'])) {
            throw new \InvalidArgumentException('property "isMobileDevice" is missing for key "' . $key . '"');
        }

        switch ($properties['Device_Type']) {
            case 'Tablet':
            case 'FonePad':
                if (true !== $properties['isTablet']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type'] . '" is NOT marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }
                break;
            case 'Mobile Phone':
            case 'Mobile Device':
            case 'Ebook Reader':
                if (true === $properties['isTablet']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true !== $properties['isMobileDevice']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type']
                        . '" is NOT marked as Mobile Device for key "' . $key . '"'
                    );
                }
                break;
            case 'Console':
                if (true === $properties['isTablet']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                break;
            case 'TV Device':
            case 'Desktop':
            default:
                if (true === $properties['isTablet']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Tablet for key "'
                        . $key . '"'
                    );
                }
                if (true === $properties['isMobileDevice']) {
                    throw new \InvalidArgumentException(
                        'the device of type "' . $properties['Device_Type'] . '" is marked as Mobile Device for key "'
                        . $key . '"'
                    );
                }
                break;
        }

        return true;
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
            return 'true';
        }

        return 'false';
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

        if (isset($properties[$property])) {
            $value = $properties[$property];
        }

        $valueOutput = $value;

        switch (CollectionParser::getPropertyType($property)) {
            case CollectionParser::TYPE_BOOLEAN:
                if (true === $value || $value === 'true') {
                    $valueOutput = 'true';
                } elseif (false === $value || $value === 'false') {
                    $valueOutput = 'false';
                }
                break;
            case CollectionParser::TYPE_IN_ARRAY:
                $valueOutput = CollectionParser::checkValueInArray($property, $value);
                break;
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

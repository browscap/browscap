<?php

namespace Browscap\Generator;

/**
 * Class BrowscapIniGenerator
 *
 * @package Browscap\Generator
 */
class BrowscapJsonGenerator extends AbstractGenerator
{
    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        $this->logger->debug('build output for json file');

        if (!empty($this->collectionData['DefaultProperties'])) {
            $defaultPropertyData = $this->collectionData['DefaultProperties'];
        } else {
            $defaultPropertyData = array();
        }

        return $this->render(
            $this->collectionData,
            array_keys(array('Parent' => '') + $defaultPropertyData)
        );
    }

    /**
     * Generate the header
     *
     * @return array
     */
    private function renderHeader()
    {
        $this->logger->debug('rendering comments');
        $header = array();

        foreach ($this->getComments() as $comment) {
            $header[] = $comment;
        }

        return $header;
    }

    /**
     * renders all found useragents into a string
     *
     * @param array[] $allDivisions
     * @param array   $allProperties
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    private function render(array $allDivisions, array $allProperties)
    {
        $this->logger->debug('rendering all divisions');

        $output = array(
            'comments'             => $this->renderHeader(),
            'GJK_Browscap_Version' => $this->renderVersion(),
        );

        foreach ($allDivisions as $key => $properties) {
            if (!isset($properties['division'])) {
                throw new \InvalidArgumentException('"division" is missing for key "' . $key . '"');
            }

            $this->logger->debug(
                'rendering division "' . $properties['division'] . '" - "' . $key . '"'
            );

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                $this->logger->debug('first check failed on key "' . $key . '" -> skipped');

                continue;
            }

            $output[$key] = array();

            foreach ($allProperties as $property) {
                if (!isset($properties[$property])) {
                    continue;
                }

                if (!CollectionParser::isOutputProperty($property)) {
                    continue;
                }

                $value       = $properties[$property];
                $valueOutput = $value;

                switch (CollectionParser::getPropertyType($property)) {
                    case CollectionParser::TYPE_BOOLEAN:
                        if (true === $value || $value === 'true') {
                            $valueOutput = true;
                        } elseif (false === $value || $value === 'false') {
                            $valueOutput = false;
                        }
                        break;
                    case CollectionParser::TYPE_IN_ARRAY:
                        $valueOutput = CollectionParser::checkValueInArray($property, $value);
                        break;
                    default:
                        // nothing t do here
                        break;
                }

                $output[$key][$property] = $valueOutput;

                unset($value, $valueOutput);
            }
        }

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * renders the version information
     *
     * @return array
     */
    private function renderVersion()
    {
        $this->logger->debug('rendering version information');

        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        return array(
            'Version'  => $versionData['version'],
            'Released' => $versionData['released'],
        );
    }
}

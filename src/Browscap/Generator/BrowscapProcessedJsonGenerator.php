<?php

namespace Browscap\Generator;

/**
 * Class BrowscapJsonGenerator
 *
 * @package Browscap\Generator
 */
class BrowscapProcessedJsonGenerator extends AbstractGenerator
{
    /**
     * Options for regex patterns.
     *
     * REGEX_DELIMITER: Delimiter of all the regex patterns in the whole class.
     * REGEX_MODIFIERS: Regex modifiers.
     */
    const REGEX_DELIMITER = '@';
    const REGEX_MODIFIERS = 'i';
    const COMPRESSION_PATTERN_START = '@';
    const COMPRESSION_PATTERN_DELIMITER = '|';

    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        $this->logger->debug('build output for processed json file');

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
        $this->logger->info('rendering all divisions');

        $output = array(
            'comments'             => $this->renderHeader(),
            'GJK_Browscap_Version' => $this->renderVersion(),
            'patterns'             => array(),
            'browsers'             => array(),
            'userAgents'           => array(),
            'properties'           => array(),
        );

        array_unshift(
            $allProperties,
            'browser_name',
            'browser_name_regex',
            'browser_name_pattern'
        );
        ksort($allProperties);

        $tmp_user_agents = array_keys($allDivisions);
        
        $this->logger->info('sort useragent rules by length');

        $fullLength    = array();
        $reducedLength = array();
        $sortindex     = array();
        
        foreach ($tmp_user_agents as $k => $a) {
            $fullLength[$k]    = strlen($a);
            $reducedLength[$k] = strlen(str_replace(array('*', '?'), '', $a));
            $sortindex[$k]     = $allDivisions[$a]['sortIndex'];
        }
        
        array_multisort(
            $fullLength, SORT_DESC, SORT_NUMERIC,
            $reducedLength, SORT_DESC, SORT_NUMERIC,
            $sortindex, SORT_ASC, SORT_NUMERIC,
            $tmp_user_agents
        );

        $user_agents_keys = array_flip($tmp_user_agents);
        $properties_keys  = array_flip($allProperties);

        $output['properties'] = $allProperties;
        $tmp_patterns = array();
        
        $this->logger->info('process all useragents');

        foreach ($tmp_user_agents as $i => $user_agent) {
            if (empty($allDivisions[$user_agent]['Comment'])
                || false !== strpos($user_agent, '*')
                || false !== strpos($user_agent, '?')
            ) {
                $pattern = $this->_pregQuote($user_agent);

                $matches_count = preg_match_all(self::REGEX_DELIMITER . '\d' . self::REGEX_DELIMITER, $pattern, $matches);

                if (!$matches_count) {
                    $tmp_patterns[$pattern] = $i . '.0';
                } else {
                    $compressed_pattern = preg_replace(self::REGEX_DELIMITER . '\d' . self::REGEX_DELIMITER, '(\d)', $pattern);

                    if (!isset($tmp_patterns[$compressed_pattern])) {
                        $tmp_patterns[$compressed_pattern] = array('first' => $pattern);
                    }

                    $tmp_patterns[$compressed_pattern][$i . '.0'] = $matches[0];
                }
            }

            if (!empty($allDivisions[$user_agent]['Parent'])) {
                $parent = $allDivisions[$user_agent]['Parent'];

                $parent_key = $user_agents_keys[$parent];

                $allDivisions[$user_agent]['Parent']       = $parent_key;
                $output['userAgents'][$parent_key . '.0'] = $tmp_user_agents[$parent_key];
            };

            $browser = array();
            foreach ($allDivisions[$user_agent] as $property => $value) {
                if (!isset($properties_keys[$property]) || !CollectionParser::isOutputProperty($property)) {
                    continue;
                }

                $key           = $properties_keys[$property];
                $browser[$key] = $value;
            }
            
            ksort($browser);

            $output['browsers'][$i . '.0'] = json_encode($browser);
        }

        // reducing memory usage by unsetting $tmp_user_agents
        unset($tmp_user_agents);
        
        ksort($output['userAgents']);
        ksort($output['browsers']);
        
        $this->logger->info('process all patterns');

        foreach ($tmp_patterns as $pattern => $pattern_data) {
            if (is_int($pattern_data) || is_string($pattern_data)) {
                $output['patterns'][$pattern] = $pattern_data;
            } elseif (2 == count($pattern_data)) {
                end($pattern_data);
                $output['patterns'][$pattern_data['first']] = key($pattern_data);
            } else {
                unset($pattern_data['first']);

                $pattern_data = $this->deduplicateCompressionPattern($pattern_data, $pattern);

                $output['patterns'][$pattern] = $pattern_data;
            }
        }

        // reducing memory usage by unsetting $tmp_user_agents
        unset($tmp_patterns);

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Converts browscap match patterns into preg match patterns.
     *
     * @param string $user_agent
     *
     * @return string
     */
    private function _pregQuote($user_agent)
    {
        $pattern = preg_quote($user_agent, self::REGEX_DELIMITER);

        // the \\x replacement is a fix for "Der gro\xdfe BilderSauger 2.00u" user agent match

        return self::REGEX_DELIMITER
        . '^'
        . str_replace(array('\*', '\?', '\\x'), array('.*', '.', '\\\\x'), $pattern)
        . '$'
        . self::REGEX_DELIMITER;
    }

    /**
     * That looks complicated...
     *
     * All numbers are taken out into $matches, so we check if any of those numbers are identical
     * in all the $matches and if they are we restore them to the $pattern, removing from the $matches.
     * This gives us patterns with "(\d)" only in places that differ for some matches.
     *
     * @param array  $matches
     * @param string $pattern
     *
     * @return array of $matches
     */
    private function deduplicateCompressionPattern($matches, &$pattern)
    {
        $tmpMatches  = $matches;
        $first_match = array_shift($tmpMatches);
        $differences = array();

        foreach ($tmpMatches as $someMatch) {
            $differences += array_diff_assoc($first_match, $someMatch);
        }

        $identical = array_diff_key($first_match, $differences);

        $preparedMatches = array();

        foreach ($matches as $i => $someMatch) {
            $key = self::COMPRESSION_PATTERN_START
                . implode(self::COMPRESSION_PATTERN_DELIMITER, array_diff_assoc($someMatch, $identical));

            $preparedMatches[$key] = $i;
        }

        $patternParts = explode('(\d)', $pattern);

        foreach ($identical as $position => $value) {
            $patternParts[$position + 1] = $patternParts[$position] . $value . $patternParts[$position + 1];
            unset($patternParts[$position]);
        }

        $pattern = implode('(\d)', $patternParts);

        return $preparedMatches;
    }
}

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
        $this->logger->debug('rendering all divisions');

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
            'browser_name_pattern',
            'Parent'
        );

        $tmp_user_agents = array_keys($allDivisions);

        usort($tmp_user_agents, array($this, 'compareBcStrings'));

        $user_agents_keys = array_flip($tmp_user_agents);
        $properties_keys  = array_flip($allProperties);

        $output['properties'] = $allProperties;
        $tmp_patterns = array();

        foreach ($tmp_user_agents as $i => $user_agent) {

            if (empty($browsers[$user_agent]['Comment'])
                || false !== strpos($user_agent, '*')
                || false !== strpos($user_agent, '?')
            ) {
                $pattern = $this->_pregQuote($user_agent);

                $matches_count = preg_match_all('@\d@', $pattern, $matches);

                if (!$matches_count) {
                    $tmp_patterns[$pattern] = $i;
                } else {
                    $compressed_pattern = preg_replace('@\d@', '(\d)', $pattern);

                    if (!isset($tmp_patterns[$compressed_pattern])) {
                        $tmp_patterns[$compressed_pattern] = array('first' => $pattern);
                    }

                    $tmp_patterns[$compressed_pattern][$i] = $matches[0];
                }
            }

            if (!empty($browsers[$user_agent]['Parent'])) {
                $parent = $browsers[$user_agent]['Parent'];

                $parent_key = $user_agents_keys[$parent];

                $browsers[$user_agent]['Parent']       = $parent_key;
                $output['userAgents'][$parent_key . '.0'] = $tmp_user_agents[$parent_key];
            };

            $browser = array();
            foreach ($browsers[$user_agent] as $key => $value) {
                if (!isset($properties_keys[$key])) {
                    continue;
                }

                $key           = $properties_keys[$key];
                $browser[$key] = $value;
            }

            $output['browsers'][] = $browser;
        }

        // reducing memory usage by unsetting $tmp_user_agents
        unset($tmp_user_agents);

        foreach ($tmp_patterns as $pattern => $pattern_data) {
            if (is_int($pattern_data)) {
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

        $output['properties'] = $this->_array2string($output['properties']);
        $output['userAgents'] = $this->_array2string($output['userAgents']);
        $output['browsers']   = $this->_array2string($output['browsers']);
        $output['patterns']   = $this->_array2string($output['patterns']);

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $a
     * @param string $b
     *
     * @return int
     */
    private function compareBcStrings($a, $b)
    {
        $a_len = strlen($a);
        $b_len = strlen($b);

        if ($a_len > $b_len) {
            return -1;
        }

        if ($a_len < $b_len) {
            return 1;
        }

        $a_len = strlen(str_replace(array('*', '?'), '', $a));
        $b_len = strlen(str_replace(array('*', '?'), '', $b));

        if ($a_len > $b_len) {
            return -1;
        }

        if ($a_len < $b_len) {
            return 1;
        }

        return 0;
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
     * Converts preg match patterns back to browscap match patterns.
     *
     * @param string        $pattern
     * @param array|boolean $matches
     *
     * @return string
     */
    private function _pregUnQuote($pattern, $matches)
    {
        // list of escaped characters: http://www.php.net/manual/en/function.preg-quote.php
        // to properly unescape '?' which was changed to '.', I replace '\.' (real dot) with '\?', then change '.' to '?' and then '\?' to '.'.
        $search  = array(
            '\\' . self::REGEX_DELIMITER, '\\.', '\\\\', '\\+', '\\[', '\\^', '\\]', '\\$', '\\(', '\\)', '\\{', '\\}',
            '\\=', '\\!', '\\<', '\\>', '\\|', '\\:', '\\-', '.*', '.', '\\?'
        );
        $replace = array(
            self::REGEX_DELIMITER, '\\?', '\\', '+', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|',
            ':', '-', '*', '?', '.'
        );

        $result = substr(str_replace($search, $replace, $pattern), 2, -2);

        if ($matches) {
            foreach ($matches as $one_match) {
                $num_pos = strpos($result, '(\d)');
                $result  = substr_replace($result, $one_match, $num_pos, 4);
            }
        }

        return $result;
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

    /**
     * Converts the given array to the PHP string which represent it.
     * This method optimizes the PHP code and the output differs form the
     * var_export one as the internal PHP function does not strip whitespace or
     * convert strings to numbers.
     *
     * @param array $array the array to parse and convert
     *
     * @return string the array parsed into a PHP string
     */
    private function _array2string($array)
    {
        $strings = array();

        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $key = '';
            } elseif (ctype_digit((string) $key) || '.0' === substr($key, -2)) {
                $key = intval($key) . '=>';
            } else {
                $key = "'" . str_replace("'", "\'", $key) . "'=>";
            }

            if (is_array($value)) {
                $value = "'" . addcslashes(serialize($value), "'") . "'";
            } elseif (ctype_digit((string) $value)) {
                $value = intval($value);
            } else {
                $value = "'" . str_replace("'", "\'", $value) . "'";
            }

            $strings[] = $key . $value;
        }

        return "array(\n" . implode(",\n", $strings) . "\n)";
    }
}

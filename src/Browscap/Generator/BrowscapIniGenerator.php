<?php

namespace Browscap\Generator;

class BrowscapIniGenerator
{
    protected $platforms;

    protected $divisions;

    protected $divisionsHaveBeenSorted = false;

    public function addPlatformsFile($src)
    {
        if (!file_exists($src)) {
            throw new \Exception("File {$src} does not exist.");
        }

        $fileContent = file_get_contents($src);
        $json = json_decode($fileContent, true);

        $this->platforms = $json['platforms'];

        if (is_null($this->platforms)) {
            throw new \Exception("File {$src} had invalid JSON.");
        }
    }

    /**
     * Load a JSON file, parse it's JSON and add it to our divisions list
     *
     * @param string $src Name of the file
     * @throws \Exception if the file does not exist
     */
    public function addSourceFile($src)
    {
        if (!file_exists($src)) {
            throw new \Exception("File {$src} does not exist.");
        }

        $fileContent = file_get_contents($src);
        $json = json_decode($fileContent, true);

        $this->divisions[] = $json;

        if (is_null($json)) {
            throw new \Exception("File {$src} had invalid JSON.");
        }
    }

    /**
     * Sort the divisions (if they haven't already been sorted)
     *
     * @return boolean
     */
    public function sortDivisions()
    {
        if (!$this->divisionsHaveBeenSorted) {
            usort($this->divisions, function($arrayA, $arrayB) {
                $a = $arrayA['sortIndex'];
                $b = $arrayB['sortIndex'];

                if ($a < $b) {
                    return -1;
                } else if ($a > $b) {
                    return +1;
                } else {
                    return 0;
                }
            });
        }

        return false;
    }

    public function generateBrowscapIni($version)
    {
        $this->sortDivisions();

        $output = $this->generateHeader($version);

        foreach ($this->divisions as $division) {
            if ($division['division'] == 'Browscap Version') continue;

            $output .= ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $division['division'] . "\n\n";

            foreach ($division['userAgents'] as $uaData) {
                $output .= $this->renderUserAgent($uaData);
            }
        }

        return $output;
    }

    public function generateHeader($version)
    {
        $dateUtc = date('l, F j, Y \a\t h:i A T');
        $date = date('r');

        $header = "";

        $header .= ";;; Provided courtesy of http://tempdownloads.browserscap.com/\n";
        $header .= ";;; Created on {$dateUtc}\n\n";

        $header .= ";;; Keep up with the latest goings-on with the project:\n";
        $header .= ";;; Follow us on Twitter <https://twitter.com/browscap>, or...\n";
        $header .= ";;; Like us on Facebook <https://facebook.com/browscap>, or...\n";
        $header .= ";;; Collaborate on GitHub <https://github.com/GaryKeith/browscap>, or...\n";
        $header .= ";;; Discuss on Google Groups <https://groups.google.com/d/forum/browscap>.\n\n";

        $header .= ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version\n\n";

        $header .= "[GJK_Browscap_Version]\n";
        $header .= "Version={$version}\n";
        $header .= "Released={$date}\n\n";

        return $header;
    }

    public function renderUserAgent(array $uaData)
    {
        $ua = $uaData['userAgent'];

        $output = "[" . $ua . "]\n";
        $output .= $this->renderProperties($uaData['properties']);
        $output .= "\n";

        if (isset($uaData['children'])) {
            $uaBase = $uaData['children']['match'];

            // @todo This needs work here. What if we specify platforms AND versions?
            // We need to make it so it does as many permutations as necessary.
            if (isset($uaData['children']['platforms'])) {
                foreach ($uaData['children']['platforms'] as $platform) {
                    $platformData = $this->platforms[$platform];

                    $output .= '[' . str_replace('#PLATFORM#', $platformData['match'], $uaBase) . "]\n";
                    $output .= $this->renderProperties(['Parent' => $ua]);
                    $output .= $this->renderProperties($platformData['properties']);
                    $output .= "\n";
                }
            }
        }

        return $output;
    }

    public function renderProperties(array $properties)
    {
        $output = '';
        foreach ($properties as $property => $value) {
            $output .= sprintf("%s=%s\n", $property, $value);
        }
        return $output;
    }
}
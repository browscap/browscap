<?php

namespace Browscap\Generator;

class BrowscapIniGenerator
{
    protected $platforms;

    protected $divisions;

    protected $divisionsHaveBeenSorted = false;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var \DateTime
     */
    protected $generationDate;

    /**
     * @var bool
     */
    protected $quoteStringProperties;

    /**
     * @var bool
     */
    protected $includeExtraProperties;

    public function __construct($version)
    {
        $this->version = $version;
        $this->generationDate = new \DateTime();
    }

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

    public function generateBrowscapIni($quoteStringProperties, $includeExtraProperties)
    {
        $this->sortDivisions();

        $this->quoteStringProperties = $quoteStringProperties;
        $this->includeExtraProperties = $includeExtraProperties;

        $output = $this->generateHeader();

        foreach ($this->divisions as $division) {
            if ($division['division'] == 'Browscap Version') continue;

            if (isset($division['versions']) && is_array($division['versions'])) {
                foreach ($division['versions'] as $version) {
                    $dotPos = strpos($version, ".");
                    if ($dotPos === false) {
                        $majorVer = $version;
                        $minorVer = '0';
                    } else {
                        $majorVer = substr($version, 0, $dotPos);
                        $minorVer = substr($version, ($dotPos+1));
                    }

                    $tmp = json_encode($division['userAgents']);
                    $tmp = str_replace('#MAJORVER#', $majorVer, $tmp);
                    $tmp = str_replace('#MINORVER#', $minorVer, $tmp);

                    $userAgents = json_decode($tmp, true);

                    $divisionName = str_replace('#MAJORVER#', $majorVer, $division['division']);
                    $divisionName = str_replace('#MINORVER#', $minorVer, $divisionName);

                    $output .= $this->renderDivision($userAgents, $divisionName);
                }
            } else {
                $output .= $this->renderDivision($division['userAgents'], $division['division']);
            }
        }

        return $output;
    }

    public function generateHeader()
    {
        $version = $this->version;
        $dateUtc = $this->generationDate->format('l, F j, Y \a\t h:i A T');
        $date = $this->generationDate->format('r');

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

    public function renderDivision($userAgents, $divisionName)
    {
        $output = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; ' . $divisionName . "\n\n";

        foreach ($userAgents as $uaData) {
            $output .= $this->renderUserAgent($uaData);
        }

        return $output;
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
            if ((!$this->includeExtraProperties) && $this->isExtraProperty($property)) {
                continue;
            }

            if ($this->quoteStringProperties && $this->getPropertyType($property) == 'string') {
                $format = "%s=\"%s\"\n";
            } else {
                $format = "%s=%s\n";
            }

            $output .= sprintf($format, $property, $value);
        }
        return $output;
    }

    public function getPropertyType($propertyName)
    {
        switch ($propertyName) {
            case 'Parent':
            case 'Comment':
            case 'Browser':
            case 'Platform':
            case 'Platform_Version':
            case 'Platform_Description':
            case 'Device_Name':
            case 'Device_Maker':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Version':
            case 'RenderingEngine_Description':
                return 'string';

            case 'Version':
            case 'MajorVer':
            case 'MinorVer':
            case 'CssVersion':
            case 'AolVersion':
                return 'number';

            case 'Alpha':
            case 'Beta':
            case 'Win16':
            case 'Win32':
            case 'Win64':
            case 'Frames':
            case 'IFrames':
            case 'Tables':
            case 'Cookies':
            case 'BackgroundSounds':
            case 'JavaScript':
            case 'VBScript':
            case 'JavaApplets':
            case 'ActiveXControls':
            case 'isMobileDevice':
            case 'isSyndicationReader':
            case 'Crawler':
                return 'boolean';

            default:
                throw new \Exception("Property {$propertyName} did not have a defined property type");
        }
    }

    public function isExtraProperty($propertyName)
    {
        switch ($propertyName) {
            case 'Device_Name':
            case 'Device_Maker':
            case 'Platform_Description':
            case 'RenderingEngine_Name':
            case 'RenderingEngine_Version':
            case 'RenderingEngine_Description':
                return true;

            default:
                return false;
        }
    }
}

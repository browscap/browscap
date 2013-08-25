<?php

namespace Browscap\Generator;

use Browscap\Entity\UserAgent;
class BrowscapIniGenerator extends AbstractIniGenerator
{
    protected $iniFormat;

    public static $propMap = array(
        'Win16' => 'win16',
        'Win32' => 'win32',
        'Win64' => 'win64',
        'Frames' => 'frames',
        'IFrames' => 'iframes',
        'Tables' => 'tables',
        'Cookies' => 'cookies',
        'BackgroundSounds' => 'bgsounds',
        'JavaScript' => 'js',
        'VBScript' => 'vbs',
        'JavaApplets' => 'java',
        'ActiveXControls' => 'activex',
        'TabletDevice' => 'tablet',
        'MobileDevice' => 'mobile',
        'SyndicationReader' => 'rssreader',
        'Crawler' => 'crawler',
        'CssVersion' => 'cssVersion',
    );

    public function generate(array $userAgents, $escapeStrings = false)
    {
        $output = '';

        $this->iniFormat = ($escapeStrings ? "%s=\"%s\"\n" : "%s=%s\n");

        foreach ($userAgents as $userAgent) {
            /* @var $userAgent \Browscap\Entity\UserAgent */

            if (!empty($userAgent->versions)) {
                foreach ($userAgent->versions as $version) {

                    $output .= sprintf(";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; %s %s\n\n", $userAgent->name, $version);
                    $output .= sprintf("[%s %s]\n", $userAgent->name, $version);

                    $output .= sprintf($this->iniFormat, "Parent", $userAgent->parent);
                    $output .= sprintf($this->iniFormat, "Comment", $userAgent->name . ' ' . $version);
                    $output .= sprintf($this->iniFormat, "Browser", $userAgent->name);
                    $output .= sprintf($this->iniFormat, "Version", $version);

                    if (strpos($version, '.') === false) {
                        $output .= sprintf($this->iniFormat, "MajorVer", $version);
                    } else {
                        $dotPos = strpos($version, '.');
                        $output .= sprintf($this->iniFormat, "MajorVer", substr($version, 0, $dotPos));
                        $output .= sprintf($this->iniFormat, "MinorVer", substr($version, $dotPos+1));
                    }

                    $output .= $this->renderProperties($userAgent);

                    if (is_array($userAgent->renderingEngines)) {
                        $renderingEngine = reset($userAgent->renderingEngines);
                    }

                    if (is_array($userAgent->platforms)) {
                        foreach ($userAgent->platforms as $platform)
                        {
                            $match = str_replace("#VERSION#", $version, $userAgent->match);
                            $match = str_replace("#RENDERER#", $renderingEngine->match, $match);
                            $match = str_replace("#PLATFORM#", $platform->match, $match);

                            $output .= sprintf("\n[%s]\n", $match);
                            $output .= sprintf($this->iniFormat, "Parent", $userAgent->name . ' ' . $version);
                            $output .= sprintf($this->iniFormat, "Platform", $platform->name);
                        }
                    }
                }
            } else {
                $output .= sprintf(";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; %s\n\n", $userAgent->name);

                $output .= sprintf("[%s]\n", $userAgent->name);
                if ($userAgent->parent) {
                    $output .= sprintf($this->iniFormat, "Parent", $userAgent->parent);
                }
                $output .= sprintf($this->iniFormat, "Comment", $userAgent->name);
                $output .= sprintf($this->iniFormat, "Browser", $userAgent->name);
                $output .= sprintf($this->iniFormat, "Version", "0.0");
                $output .= sprintf($this->iniFormat, "MajorVer", "0");
                $output .= sprintf($this->iniFormat, "MinorVer", "0");

                $output .= $this->renderProperties($userAgent);
            }

            $output .= "\n";
        }

        return $output;
    }

    public function renderProperties(UserAgent $userAgent)
    {
        $output = '';

        foreach ($userAgent->properties as $propertyName => $propertyValue)
        {
            switch ($this->propertyType($propertyName))
            {
                case 'boolean':
                    $output .= sprintf("%s=%s\n", $this->propertyMap($propertyName), ($propertyValue == 1 ? 'true' : 'false'));
                    break;
                case 'string':
                    $output .= sprintf($this->iniFormat, $this->propertyMap($propertyName), $propertyValue);
                    break;
            }

        }

        return $output;
    }

    public function propertyMap($propertyName)
    {
        $propMap = array_flip(self::$propMap);

        if (!isset($propMap[$propertyName])) {
            $msg = sprintf('Property %s was not mapped in %s', $propertyName, __CLASS__);
            throw new \Exception($msg);
        }

        return $propMap[$propertyName];
    }

    public function propertyType($propertyName)
    {
        switch ($propertyName)
        {
            case 'frames':
            case 'iframes':
            case 'javascript':
            case 'win16':
            case 'win32':
            case 'win64':
            case 'tables':
            case 'iframes':
            case 'tables':
            case 'cookies':
            case 'bgsounds':
            case 'js':
            case 'vbs':
            case 'java':
            case 'activex':
            case 'tablet':
            case 'mobile':
            case 'rssreader':
            case 'crawler':
                return 'boolean';
            case 'cssVersion':
                return 'string';
            default:
                $msg = sprintf('Property type for %s was not defined in %s', $propertyName, __CLASS__);
                throw new \Exception($msg);
        }
    }
}

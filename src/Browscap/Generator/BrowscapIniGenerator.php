<?php

namespace Browscap\Generator;

use Browscap\Entity\UserAgent;
class BrowscapIniGenerator extends AbstractIniGenerator
{
    protected $iniFormat;

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
                    $output .= $this->renderProperties($userAgent);

                    $renderingEngine = reset($userAgent->renderingEngines);

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
            } else {
                $output .= sprintf(";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; %s\n\n", $userAgent->name);

                $output .= sprintf("[%s]\n", $userAgent->name);
                if ($userAgent->parent) {
                    $output .= sprintf($this->iniFormat, "Parent", $userAgent->parent);
                }
                $output .= sprintf($this->iniFormat, "Comment", $userAgent->name);
                $output .= sprintf($this->iniFormat, "Browser", $userAgent->name);
                $output .= sprintf($this->iniFormat, "Version", "0.0");
            }
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
        switch ($propertyName)
        {
        	case 'frames': return 'Frames';
        	case 'iframes': return 'IFrames';
        	case 'javascript': return 'JavaScript';
        	default:
        	    $msg = sprintf('Property %s was not mapped in %s', $propertyName, __CLASS__);
        	    throw new \Exception($msg);
        }
    }

    public function propertyType($propertyName)
    {
        switch ($propertyName)
        {
        	case 'frames':
        	case 'iframes':
        	case 'javascript':
        	    return 'boolean';
        	default:
        	    $msg = sprintf('Property type for %s was not defined in %s', $propertyName, __CLASS__);
        	    throw new \Exception($msg);
        }
    }
}

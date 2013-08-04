<?php

namespace Browscap\Adapters;

use Browscap\Parser\ParserInterface;
use Browscap\Entity\UserAgent;

class UserAgentAdapter
{
    protected $parser;

    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * @return Browscap\Entity\UserAgent
     */
    public function generateEntity()
    {
        $sourceUserAgent = $this->parser->getParsed();

        $userAgent = new UserAgent();
        $userAgent->name = $sourceUserAgent->name;

        if (isset($sourceUserAgent->parent)) {
            $userAgent->parent = $sourceUserAgent->parent;
        }

        if (isset($sourceUserAgent->match)) {
            $userAgent->match = $sourceUserAgent->match;
        }

        if (isset($sourceUserAgent->versions)) {
            $userAgent->versions = $sourceUserAgent->versions;
        }

        if (isset($sourceUserAgent->renderingEngines)) {
            $userAgent->renderingEngines = $sourceUserAgent->renderingEngines;
        }

        if (isset($sourceUserAgent->platforms)) {
            $userAgent->platforms = $sourceUserAgent->platforms;
        }

        if (isset($sourceUserAgent->devices)) {
            $userAgent->devices = $sourceUserAgent->devices;
        }

        $userAgent->properties = $sourceUserAgent->properties;

        return $userAgent;
    }
}

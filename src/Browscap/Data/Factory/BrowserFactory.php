<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Browser;
use UaBrowserType\TypeLoader;

class BrowserFactory
{
    /**
     * validates the $browserData array and creates Browser objects from it
     *
     * @param array  $browserData
     * @param string $browserName
     *
     * @throws \RuntimeException                if the file does not exist or has invalid JSON
     * @throws \Assert\AssertionFailedException
     *
     * @return \Browscap\Data\Browser
     */
    public function build(array $browserData, $browserName)
    {
        if (!isset($browserData['properties']) || !is_array($browserData['properties'])) {
            $browserData['properties'] = [];
        }

        Assertion::keyExists($browserData, 'standard', 'the value for "standard" key is missing for browser "' . $browserName . '"');
        Assertion::boolean($browserData['standard']);

        Assertion::keyExists($browserData, 'lite', 'the value for "lite" key is missing for browser "' . $browserName . '"');
        Assertion::boolean($browserData['lite']);

        Assertion::keyExists($browserData, 'type', 'the value for "type" key is missing for browser "' . $browserName . '"');
        Assertion::string($browserData['type']);

        // check for available values in external library
        if (!(new TypeLoader())->has($browserData['type'])) {
            throw new \UnexpectedValueException('unsupported browser type given for browser "' . $browserName . '"');
        }

        // check for supported values (browscap-php) @todo remove asap
        Assertion::inArray($browserData['type'], ['application', 'bot', 'browser', 'email-client', 'feed-reader', 'library', 'multimedia-player', 'offline-browser', 'tool', 'transcoder', 'unknown', 'useragent-anonymizer']);

        return new Browser($browserData['properties'], $browserData['type'], $browserData['lite'], $browserData['standard']);
    }
}

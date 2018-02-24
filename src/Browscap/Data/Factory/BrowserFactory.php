<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Browser;

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

        return new Browser($browserData['properties'], $browserData['type'], $browserData['lite'], $browserData['standard']);
    }
}

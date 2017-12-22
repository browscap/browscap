<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Browser;

class BrowserFactory
{
    /**
     * Load a engines.json file and parse it into the platforms data array
     *
     * @param array  $browserData
     * @param string $browserName
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     * @return \Browscap\Data\Browser
     * @throws \Assert\AssertionFailedException
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

        return new Browser($browserData['properties'], $browserData['lite'], $browserData['standard']);
    }
}

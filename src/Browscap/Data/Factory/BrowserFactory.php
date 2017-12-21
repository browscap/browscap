<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

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
     *
     * @return \Browscap\Data\Browser
     */
    public function build(array $browserData, $browserName)
    {
        if (!isset($browserData['properties'])) {
            $browserData['properties'] = [];
        }

        if (!array_key_exists('standard', $browserData)) {
            throw new \UnexpectedValueException(
                'the value for "standard" key is missing for browser "' . $browserName . '"'
            );
        }

        if (!array_key_exists('lite', $browserData)) {
            throw new \UnexpectedValueException(
                'the value for "lite" key is missing for browser "' . $browserName . '"'
            );
        }

        return new Browser($browserData['properties'], $browserData['lite'], $browserData['standard']);
    }
}

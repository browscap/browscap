<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Engine;

class EngineFactory
{
    /**
     * validates the $engineData array and creates Engine objects from it
     *
     * @param array  $engineData The Engine data for the current object
     * @param array  $json       The Engine data for all engines
     * @param string $engineName The name for the current engine
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return Engine
     */
    public function build(array $engineData, array $json, string $engineName) : Engine
    {
        if (!isset($engineData['properties'])) {
            $engineData['properties'] = [];
        }

        if (array_key_exists('inherits', $engineData)) {
            $parentName = $engineData['inherits'];

            Assertion::keyExists($json['engines'], $parentName, 'parent Engine "' . $parentName . '" is missing for engine "' . $engineName . '"');

            $parentEngine     = $this->build($json['engines'][$parentName], $json, $parentName);
            $parentEngineData = $parentEngine->getProperties();

            $engineProperties = $engineData['properties'];

            foreach ($engineProperties as $name => $value) {
                if (isset($parentEngineData[$name])
                    && $parentEngineData[$name] === $value
                ) {
                    throw new \UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $engineName
                        . '" and its parent "' . $parentName . '"'
                    );
                }
            }

            $engineData['properties'] = array_merge(
                $parentEngineData,
                $engineProperties
            );
        }

        return new Engine($engineData['properties']);
    }
}

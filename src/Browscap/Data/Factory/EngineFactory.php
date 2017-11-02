<?php
declare(strict_types = 1);
namespace Browscap\Data\Factory;

use Assert\Assertion;
use Browscap\Data\Engine;

final class EngineFactory
{
    /**
     * validates the $engineData array and creates Engine objects from it
     *
     * @param array  $engineData     The Engine data for the current object
     * @param array  $dataAllEngines The Engine data for all engines
     * @param string $engineName     The name for the current engine
     *
     * @throws \RuntimeException if the file does not exist or has invalid JSON
     *
     * @return Engine
     */
    public function build(array $engineData, array $dataAllEngines, string $engineName) : Engine
    {
        Assertion::isArray($engineData, 'each entry inside the "engines" structure has to be an array');

        if (!array_key_exists('properties', $engineData) && !array_key_exists('inherits', $engineData)) {
            throw new \UnexpectedValueException('required attibute "properties" is missing');
        }

        if (!array_key_exists('properties', $engineData) || !is_array($engineData['properties'])) {
            $engineData['properties'] = [];
        }

        if (array_key_exists('inherits', $engineData)) {
            Assertion::string($engineData['inherits'], 'parent Engine key has to be a string for engine "' . $engineName . '"');

            $parentName = $engineData['inherits'];

            Assertion::keyExists($dataAllEngines, $parentName, 'parent Engine "' . $parentName . '" is missing for engine "' . $engineName . '"');

            $parentEngine     = $this->build($dataAllEngines[$parentName], $dataAllEngines, $parentName);
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

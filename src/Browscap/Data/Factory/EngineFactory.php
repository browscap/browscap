<?php

declare(strict_types=1);

namespace Browscap\Data\Factory;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Browscap\Data\Engine;
use RuntimeException;
use UnexpectedValueException;

use function array_key_exists;
use function array_merge;
use function is_array;

/** @phpstan-import-type EngineData from Engine */
final class EngineFactory
{
    /**
     * validates the $engineData array and creates Engine objects from it
     *
     * @param mixed[]   $engineData     The Engine data for the current object
     * @param mixed[][] $dataAllEngines The Engine data for all engines
     * @param string    $engineName     The name for the current engine
     * @phpstan-param EngineData $engineData
     * @phpstan-param array<string, EngineData> $dataAllEngines
     *
     * @throws RuntimeException if the file does not exist or has invalid JSON.
     * @throws AssertionFailedException
     */
    public function build(array $engineData, array $dataAllEngines, string $engineName): Engine
    {
        Assertion::isArray($engineData, 'each entry inside the "engines" structure has to be an array');

        if (! array_key_exists('properties', $engineData) && ! array_key_exists('inherits', $engineData)) {
            throw new UnexpectedValueException('required attibute "properties" is missing');
        }

        if (! array_key_exists('properties', $engineData) || ! is_array($engineData['properties'])) {
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
                if (
                    array_key_exists($name, $parentEngineData)
                    && $parentEngineData[$name] === $value
                ) {
                    throw new UnexpectedValueException(
                        'the value for property "' . $name . '" has the same value in the keys "' . $engineName
                        . '" and its parent "' . $parentName . '"',
                    );
                }
            }

            $engineData['properties'] = array_merge(
                $parentEngineData,
                $engineProperties,
            );
        }

        return new Engine($engineData['properties']);
    }
}

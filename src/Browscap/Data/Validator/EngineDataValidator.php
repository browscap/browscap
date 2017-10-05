<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

class EngineDataValidator
{
    /**
     * checks if platform properties are set inside a properties array
     *
     * @param array  $properties
     * @param string $message
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function check(array $properties, string $message) : void
    {
        if (array_key_exists('RenderingEngine_Name', $properties)
            || array_key_exists('RenderingEngine_Version', $properties)
            || array_key_exists('RenderingEngine_Description', $properties)
            || array_key_exists('RenderingEngine_Maker', $properties)
            || array_key_exists('VBScript', $properties)
            || array_key_exists('ActiveXControls', $properties)
            || array_key_exists('BackgroundSounds', $properties)
        ) {
            throw new \LogicException($message);
        }
    }
}

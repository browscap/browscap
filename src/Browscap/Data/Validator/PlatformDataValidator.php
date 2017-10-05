<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

class PlatformDataValidator
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
        if (array_key_exists('Platform', $properties)
            || array_key_exists('Platform_Description', $properties)
            || array_key_exists('Platform_Maker', $properties)
            || array_key_exists('Platform_Bits', $properties)
            || array_key_exists('Platform_Version', $properties)
            || array_key_exists('Win16', $properties)
            || array_key_exists('Win32', $properties)
            || array_key_exists('Win64', $properties)
            || array_key_exists('Browser_Bits', $properties)
        ) {
            throw new \LogicException($message);
        }
    }
}

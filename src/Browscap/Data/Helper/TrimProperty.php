<?php
declare(strict_types = 1);
namespace Browscap\Data\Helper;

class TrimProperty
{
    /**
     * trims the value of a property and converts the string values "true" and "false" to boolean
     *
     * @param string $propertyValue
     *
     * @return bool|string
     */
    public function trimProperty(string $propertyValue)
    {
        switch ($propertyValue) {
            case 'true':
                $propertyValue = true;

                break;
            case 'false':
                $propertyValue = false;

                break;
            default:
                $propertyValue = trim($propertyValue);

                break;
        }

        return $propertyValue;
    }
}

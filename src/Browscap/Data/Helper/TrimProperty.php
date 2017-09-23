<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Data\Helper;

/**
 * Class Expander
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
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

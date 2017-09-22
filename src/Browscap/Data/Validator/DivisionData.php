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
namespace Browscap\Data\Validator;

/**
 * Class DivisionData
 *
 * @category   Browscap
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DivisionData
{
    /**
     * @param array  $divisionData
     * @param string $fileName
     *
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public static function validate(array $divisionData, string $fileName) : void
    {
        if (!array_key_exists('division', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "division" is missing in File ' . $fileName);
        }

        if (!is_string($divisionData['division'])) {
            throw new \UnexpectedValueException('required attibute "division" has to be a string in File ' . $fileName);
        }

        if (!array_key_exists('sortIndex', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "sortIndex" is missing in File ' . $fileName);
        }

        if (!is_int($divisionData['sortIndex']) || 0 > $divisionData['sortIndex']) {
            throw new \UnexpectedValueException('required attibute "sortIndex" has to be a positive integer in File ' . $fileName);
        }

        if (!array_key_exists('lite', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "lite" is missing in File ' . $fileName);
        }

        if (!is_bool($divisionData['lite'])) {
            throw new \UnexpectedValueException('required attibute "lite" has to be an boolean in File ' . $fileName);
        }

        if (!array_key_exists('standard', $divisionData)) {
            throw new \UnexpectedValueException('required attibute "standard" is missing in File ' . $fileName);
        }

        if (!is_bool($divisionData['standard'])) {
            throw new \UnexpectedValueException('required attibute "standard" has to be an boolean in File ' . $fileName);
        }

        if (!isset($divisionData['userAgents'])) {
            throw new \UnexpectedValueException('required attibute "userAgents" is missing in File ' . $fileName);
        }

        if (!is_array($divisionData['userAgents']) || empty($divisionData['userAgents'])) {
            throw new \UnexpectedValueException('required attibute "userAgents" should be an non-empty array in File ' . $fileName);
        }
    }
}

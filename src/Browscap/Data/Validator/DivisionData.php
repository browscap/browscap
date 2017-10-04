<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

/**
 * Class DivisionData
 *
 * @author     Thomas Müller <mimmi20@live.de>
 */
class DivisionData
{
    /**
     * @param array  $divisionData
     * @param string $filename
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function validate(array $divisionData, string $filename) : void
    {
        if (!array_key_exists('division', $divisionData)) {
            throw new \LogicException('required attibute "division" is missing in File ' . $filename);
        }

        if (!is_string($divisionData['division'])) {
            throw new \LogicException('required attibute "division" has to be a string in File ' . $filename);
        }

        if (!array_key_exists('sortIndex', $divisionData)) {
            throw new \LogicException('required attibute "sortIndex" is missing in File ' . $filename);
        }

        if (!is_int($divisionData['sortIndex']) || 0 > $divisionData['sortIndex']) {
            throw new \LogicException('required attibute "sortIndex" has to be a positive integer in File ' . $filename);
        }

        if (!array_key_exists('lite', $divisionData)) {
            throw new \LogicException('required attibute "lite" is missing in File ' . $filename);
        }

        if (!is_bool($divisionData['lite'])) {
            throw new \LogicException('required attibute "lite" has to be an boolean in File ' . $filename);
        }

        if (!array_key_exists('standard', $divisionData)) {
            throw new \LogicException('required attibute "standard" is missing in File ' . $filename);
        }

        if (!is_bool($divisionData['standard'])) {
            throw new \LogicException('required attibute "standard" has to be an boolean in File ' . $filename);
        }

        if (!isset($divisionData['userAgents'])) {
            throw new \LogicException('required attibute "userAgents" is missing in File ' . $filename);
        }

        if (!is_array($divisionData['userAgents']) || empty($divisionData['userAgents'])) {
            throw new \LogicException('required attibute "userAgents" should be an non-empty array in File ' . $filename);
        }
    }
}

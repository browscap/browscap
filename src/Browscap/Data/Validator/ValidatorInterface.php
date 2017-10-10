<?php
declare(strict_types = 1);
namespace Browscap\Data\Validator;

interface ValidatorInterface
{
    /**
     * validates the fully expanded properties
     *
     * @param array  $properties Data to validate
     * @param string $key
     *
     * @throws \LogicException
     */
    public function validate(array $properties, string $key) : void;
}

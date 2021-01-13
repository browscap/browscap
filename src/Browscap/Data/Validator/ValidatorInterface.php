<?php

declare(strict_types=1);

namespace Browscap\Data\Validator;

use LogicException;

interface ValidatorInterface
{
    /**
     * validates the fully expanded properties
     *
     * @param mixed[] $properties Data to validate
     *
     * @throws LogicException
     */
    public function validate(array $properties, string $key): void;
}

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
     * @return void|string[]
     *
     * @throws LogicException
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function validate(array $properties, string $key);
}

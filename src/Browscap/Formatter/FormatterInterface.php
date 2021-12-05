<?php

declare(strict_types=1);

namespace Browscap\Formatter;

use Exception;
use JsonException;

interface FormatterInterface
{
    public const TYPE_ASP  = 'asp';
    public const TYPE_CSV  = 'csv';
    public const TYPE_PHP  = 'php';
    public const TYPE_JSON = 'json';
    public const TYPE_XML  = 'xml';

    /**
     * returns the Type of the formatter
     *
     * @throws void
     */
    public function getType(): string;

    /**
     * formats the name of a property
     *
     * @throws JsonException
     */
    public function formatPropertyName(string $name): string;

    /**
     * formats the name of a property
     *
     * @param bool|int|string $value
     *
     * @throws Exception
     * @throws JsonException
     */
    public function formatPropertyValue($value, string $property): string;
}

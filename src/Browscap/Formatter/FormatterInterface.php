<?php
declare(strict_types = 1);
namespace Browscap\Formatter;

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
     * @return string
     */
    public function getType() : string;

    /**
     * formats the name of a property
     *
     * @param string $name
     *
     * @return string
     */
    public function formatPropertyName(string $name) : string;

    /**
     * formats the name of a property
     *
     * @param bool|string $value
     * @param string      $property
     *
     * @return string
     */
    public function formatPropertyValue($value, string $property) : string;
}

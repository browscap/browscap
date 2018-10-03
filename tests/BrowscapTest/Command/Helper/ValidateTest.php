<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Besitzer
 * Date: 03.10.2018
 * Time: 14:18
 */
namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\Validate;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    /**
     * @var Validate
     */
    private $object;

    /**
     * @throws \Exception
     */
    public function setUp() : void
    {
        $this->object = new Validate();
    }

    public function testGetName() : void
    {
        self::assertSame('validate', $this->object->getName());
    }
}

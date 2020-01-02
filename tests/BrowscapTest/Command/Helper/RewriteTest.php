<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Besitzer
 * Date: 03.10.2018
 * Time: 14:07
 */
namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\Rewrite;
use PHPUnit\Framework\TestCase;

class RewriteTest extends TestCase
{
    /**
     * @var Rewrite
     */
    private $object;

    /**
     * @throws \Exception
     */
    protected function setUp() : void
    {
        $this->object = new Rewrite();
    }

    public function testGetName() : void
    {
        static::assertSame('rewrite', $this->object->getName());
    }
}

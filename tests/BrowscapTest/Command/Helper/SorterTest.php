<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Besitzer
 * Date: 03.10.2018
 * Time: 14:11
 */
namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\Sorter;
use PHPUnit\Framework\TestCase;

class SorterTest extends TestCase
{
    /**
     * @var Sorter
     */
    private $object;

    /**
     * @throws \Exception
     */
    public function setUp() : void
    {
        $this->object = new Sorter();
    }

    public function testGetName() : void
    {
        self::assertSame('sorter', $this->object->getName());
    }

    public function testSort() : void
    {
        $data         = '{"b": "1","a": "2"}';
        $expectedData = '{"a":"2","b":"1"}';

        self::assertSame($expectedData, $this->object->sort($data));
    }
}

<?php

namespace BrowscapTest\Parser;

use Browscap\Parser\IniParser;

class IniParserTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsFilename()
    {
        $parser = new IniParser('foobar');
        $this->assertSame('foobar', $parser->getFilename());
    }

    public function testSetShouldSort()
    {
        $parser = new IniParser('');

        // Test the default value
        $this->assertAttributeEquals(false, 'shouldSort', $parser);

        // Test setting it to true
        $parser->setShouldSort(true);
        $this->assertAttributeEquals(true, 'shouldSort', $parser);

        // Test setting it back to false
        $parser->setShouldSort(false);
        $this->assertAttributeEquals(false, 'shouldSort', $parser);
    }

    public function testShouldSort()
    {
        $parser = new IniParser('');

        $this->assertFalse($parser->shouldSort());

        $parser->setShouldSort(true);
        $this->assertTrue($parser->shouldSort());

        $parser->setShouldSort(false);
        $this->assertFalse($parser->shouldSort());
    }

    public function sortArrayDataProvider()
    {
        return [
            'flatArray' => [
                ['d' => 'lemon', 'a' => 'orange', 'b' => 'banana', 'c' => 'apple'],
                ['a' => 'orange', 'b' => 'banana', 'c' => 'apple', 'd' => 'lemon'],
            ],
            'twoDimensionalArray' => [
                ['z' => 'zzz', 'x' => ['b' => 'bbb', 'a' => 'aaa', 'c' => 'ccc'], 'y' => ['k' => 'kkk', 'j' => 'jjj', 'i' => 'iii']],
                ['x' => ['a' => 'aaa', 'b' => 'bbb', 'c' => 'ccc'], 'y' => ['i' => 'iii', 'j' => 'jjj', 'k' => 'kkk'], 'z' => 'zzz'],
            ],
        ];
    }

    /**
     * @dataProvider sortArrayDataProvider
     */
    public function testSortArrayAndChildArrays($unsorted, $sorted)
    {
        $parser = new IniParser('');

        $sortMethod = new \ReflectionMethod('\Browscap\Parser\IniParser', 'sortArrayAndChildArrays');
        $sortMethod->setAccessible(true);
        $this->assertSame($sorted, $sortMethod->invokeArgs($parser, [$unsorted]));
    }
}
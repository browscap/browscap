<?php
/**
 * Copyright (c) 1998-2014 Browser Capabilities Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the LICENSE file distributed with this package.
 *
 * @category   BrowscapTest
 * @package    Parser
 * @copyright  1998-2014 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Parser;

use Browscap\Parser\IniParser;

/**
 * Class IniParserTest
 *
 * @category   BrowscapTest
 * @package    Parser
 * @author     James Titcumb <james@asgrim.com>
 */
class IniParserTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsFilename()
    {
        $parser = new IniParser('foobar');
        self::assertSame('foobar', $parser->getFilename());
    }

    public function testSetShouldSort()
    {
        $parser = new IniParser('');

        // Test the default value
        self::assertAttributeEquals(false, 'shouldSort', $parser);

        // Test setting it to true
        $parser->setShouldSort(true);
        self::assertAttributeEquals(true, 'shouldSort', $parser);

        // Test setting it back to false
        $parser->setShouldSort(false);
        self::assertAttributeEquals(false, 'shouldSort', $parser);
    }

    public function testShouldSort()
    {
        $parser = new IniParser('');

        self::assertFalse($parser->shouldSort());

        $parser->setShouldSort(true);
        self::assertTrue($parser->shouldSort());

        $parser->setShouldSort(false);
        self::assertFalse($parser->shouldSort());
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
        self::assertSame($sorted, $sortMethod->invokeArgs($parser, [$unsorted]));
    }

    public function testGetLinesFromFileReturnsArrayWithLines()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
; comment

[test]
test=test
HERE;

        file_put_contents($tmpfile, $in);

        $parser = new IniParser($tmpfile);

        $out = $parser->getLinesFromFile();

        unlink($tmpfile);

        $expected = [
            '; comment',
            '[test]',
            'test=test',
        ];

        self::assertSame($expected, $out);
    }

    public function testGetLinesFromFileThrowsExceptionIfFileDoesNotExist()
    {
        $file = '/hopefully/this/file/does/not/exist';
        $parser = new IniParser($file);
        $this->setExpectedException('\InvalidArgumentException', 'File not found: ' . $file);
        $parser->getLinesFromFile();
    }

    public function testGetFileLinesReturnsLinesFromFile()
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'browscaptest');

        $in = <<<HERE
; comment

[test]
test=test
HERE;

        file_put_contents($tmpfile, $in);

        $parser = new IniParser($tmpfile);

        $out = $parser->getFileLines();

        unlink($tmpfile);

        $expected = [
            '; comment',
            '[test]',
            'test=test',
        ];

        self::assertSame($expected, $out);
    }

    public function testGetFileLinesReturnsLinesFromPreviouslySetLines()
    {
        $lines = ['first','second','third'];

        $parser = new IniParser('');
        $parser->setFileLines($lines);

        self::assertSame($lines, $parser->getFileLines());
    }

    public function testParseWithoutSorting()
    {
        $lines = [
            '',
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; division1',
            '',
            '; this line is a comment',
            '[section1]',
            'property11=value11',
            'property12=value12',
            '',
            '; this line is a comment',
            '[section2]',
            'property21=value21',
            'property22=value22',
            '',
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; division2',
            '',
            '; this line is a comment',
            '[section3]',
            'property31=value31',
            'property32=value32',
        ];

        $parser = new IniParser('');
        $parser->setFileLines($lines);
        $parser->setShouldSort(true);

        $expected = [
            'section1' => ['property11' => 'value11', 'property12' => 'value12', 'Division' => 'division1'],
            'section2' => ['property21' => 'value21', 'property22' => 'value22', 'Division' => 'division1'],
            'section3' => ['property31' => 'value31', 'property32' => 'value32', 'Division' => 'division2'],
        ];

        $data = $parser->parse();

        self::assertSame($data, $parser->getParsed());

        self::assertEquals($expected, $data);
    }

    public function testParseWithSorting()
    {
        $lines = [
            '',
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; division1',
            '',
            '; this line is a comment',
            '[section1]',
            'property11=value11',
            'property12=value12',
            '',
            '; this line is a comment',
            '[section2]',
            'property21=value21',
            'property22=value22',
            '',
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; division2',
            '',
            '; this line is a comment',
            '[section3]',
            'property31=value31',
            'property32=value32',
        ];

        $parser = new IniParser('');
        $parser->setFileLines($lines);
        $parser->setShouldSort(true);

        $expected = [
            'section1' => ['Division' => 'division1', 'property11' => 'value11', 'property12' => 'value12'],
            'section2' => ['Division' => 'division1', 'property21' => 'value21', 'property22' => 'value22'],
            'section3' => ['Division' => 'division2', 'property31' => 'value31', 'property32' => 'value32'],
        ];

        $data = $parser->parse();

        self::assertSame($expected, $data);
    }

    public function testParseThrowsExceptionWhenInvalidFormatting()
    {
        $lines = [
            'double=equals=here',
        ];

        $parser = new IniParser('');
        $parser->setFileLines($lines);

        $this->setExpectedException('\RuntimeException', 'Too many equals in line: double=equals=here');
        $parser->parse();
    }
}

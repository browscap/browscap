<?php
/**
 * Copyright (c) 1998-2017 Browser Capabilities Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   BrowscapTest
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace BrowscapTest\Parser;

use Browscap\Parser\IniParser;

/**
 * Class IniParserTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class IniParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests creating the parser class
     *
     * @group parser
     * @group sourcetest
     */
    public function testConstructorSetsFilename()
    {
        $parser = new IniParser('foobar');
        self::assertSame('foobar', $parser->getFilename());
    }

    /**
     * tests setting the should sort flag
     *
     * @group parser
     * @group sourcetest
     */
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

    /**
     * tests setting and getting the should sort flag
     *
     * @group parser
     * @group sourcetest
     */
    public function testShouldSort()
    {
        $parser = new IniParser('');

        self::assertFalse($parser->shouldSort());

        $parser->setShouldSort(true);
        self::assertTrue($parser->shouldSort());

        $parser->setShouldSort(false);
        self::assertFalse($parser->shouldSort());
    }

    /**
     * tests setting and getting a logger
     */
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
     *
     * @group parser
     * @group sourcetest
     */
    public function testSortArrayAndChildArrays($unsorted, $sorted)
    {
        $parser = new IniParser('');

        $sortMethod = new \ReflectionMethod('\Browscap\Parser\IniParser', 'sortArrayAndChildArrays');
        $sortMethod->setAccessible(true);
        self::assertSame($sorted, $sortMethod->invokeArgs($parser, [$unsorted]));
    }

    /**
     * tests getting lines from a file
     *
     * @group parser
     * @group sourcetest
     */
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

    /**
     * tests throwing an exception if the input file does not exist
     *
     * @group parser
     * @group sourcetest
     */
    public function testGetLinesFromFileThrowsExceptionIfFileDoesNotExist()
    {
        $file   = '/hopefully/this/file/does/not/exist';
        $parser = new IniParser($file);

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('File not found: ' . $file);

        $parser->getLinesFromFile();
    }

    /**
     * tests getting lines from a file
     *
     * @group parser
     * @group sourcetest
     */
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

    /**
     * tests setting and getting lines of a file
     *
     * @group parser
     * @group sourcetest
     */
    public function testGetFileLinesReturnsLinesFromPreviouslySetLines()
    {
        $lines = ['first', 'second', 'third'];

        $parser = new IniParser('');
        $parser->setFileLines($lines);

        self::assertSame($lines, $parser->getFileLines());
    }

    /**
     * tests parsing sections without sorting
     *
     * @group parser
     * @group sourcetest
     */
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
            'property33',
        ];

        $parser = new IniParser('');
        $parser->setFileLines($lines);
        $parser->setShouldSort(true);

        $expected = [
            'section1' => ['property11' => 'value11', 'property12' => 'value12', 'Division' => 'division1'],
            'section2' => ['property21' => 'value21', 'property22' => 'value22', 'Division' => 'division1'],
            'section3' => ['property31' => 'value31', 'property32' => 'value32', 'property33' => '', 'Division' => 'division2'],
        ];

        $data = $parser->parse();

        self::assertSame($data, $parser->getParsed());

        self::assertEquals($expected, $data);
    }

    /**
     * tests parsing sections with sorting
     *
     * @group parser
     * @group sourcetest
     */
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

    /**
     * tests throwing an exception if more than one eual sign is present in a line
     *
     * @group parser
     * @group sourcetest
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Too many equals in line: double=equals=here
     */
    public function testParseThrowsExceptionWhenInvalidFormatting()
    {
        $lines = [
            'double=equals=here',
        ];

        $parser = new IniParser('');
        $parser->setFileLines($lines);

        $parser->parse();
    }
}

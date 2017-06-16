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

namespace BrowscapTest\Data;

use Browscap\Data\Division;

/**
 * Class DivisionTest
 *
 * @category   BrowscapTest
 * @author     James Titcumb <james@asgrim.com>
 */
class DivisionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter
     *
     * @group data
     * @group sourcetest
     */
    public function testGetter()
    {
        $name       = 'TestName';
        $sortIndex  = 42;
        $userAgents = ['abc' => 'def'];
        $versions   = [1, 2, 3];

        $object = new Division($name, $sortIndex, $userAgents, true, false, $versions);

        self::assertSame($name, $object->getName());
        self::assertSame($sortIndex, $object->getSortIndex());
        self::assertSame($userAgents, $object->getUserAgents());
        self::assertTrue($object->isLite());
        self::assertFalse($object->isStandard());
        self::assertSame($versions, $object->getVersions());
    }
}

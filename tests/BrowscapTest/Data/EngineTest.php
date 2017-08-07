<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowscapTest\Data;

use Browscap\Data\Engine;

/**
 * Class EngineTest
 *
 * @category   BrowscapTest
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class EngineTest extends \PHPUnit\Framework\TestCase
{
    /**
     * tests setter and getter for the engine properties
     *
     * @group data
     * @group sourcetest
     */
    public function testGetProperties()
    {
        $properties = ['abc' => 'def'];

        $object = new Engine($properties);

        self::assertSame($properties, $object->getProperties());
    }
}

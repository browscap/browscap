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
 * @category   Browscap
 * @copyright  1998-2017 Browser Capabilities Project
 * @license    MIT
 */

namespace Browscap\Generator;

use Browscap\Helper\CollectionCreator;
use Browscap\Writer;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildCustomFileGenerator extends AbstractBuildGenerator
{
    /**@+
     * @var string
     */
    const OUTPUT_FORMAT_PHP  = 'php';
    const OUTPUT_FORMAT_ASP  = 'asp';
    const OUTPUT_FORMAT_CSV  = 'csv';
    const OUTPUT_FORMAT_XML  = 'xml';
    const OUTPUT_FORMAT_JSON = 'json';
    /**@-*/

    /**@+
     * @var string
     */
    const OUTPUT_TYPE_FULL    = 'full';
    const OUTPUT_TYPE_DEFAULT = 'normal';
    const OUTPUT_TYPE_LITE    = 'lite';
    /**@-*/

    /**
     * Entry point for generating builds for a specified version
     *
     * @param string      $version
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     *
     * @return \Browscap\Generator\BuildCustomFileGenerator
     */
    public function run(
        $version,
        $fields = [],
        $file = null,
        $format = self::OUTPUT_FORMAT_PHP
    ) {
        return $this
            ->preBuild($fields, $file, $format)
            ->build($version)
            ->postBuild();
    }

    /**
     * runs before the build
     *
     * @param array       $fields
     * @param string|null $file
     * @param string      $format
     *
     * @return \Browscap\Generator\BuildCustomFileGenerator
     */
    protected function preBuild(
        $fields = [],
        $file = null,
        $format = self::OUTPUT_FORMAT_PHP
    ) {
        parent::preBuild();

        $this->getLogger()->info('started creating the custom output file');

        if (null === $this->collectionCreator) {
            $this->setCollectionCreator(new CollectionCreator());
        }

        if (null === $this->writerCollection) {
            $factory = new Writer\Factory\CustomWriterFactory();

            $this->setWriterCollection(
                $factory->createCollection(
                    $this->getLogger(),
                    $this->buildFolder,
                    $file,
                    $fields,
                    $format
                )
            );
        }

        return $this;
    }

    /**
     * runs after the build
     *
     * @return \Browscap\Generator\BuildCustomFileGenerator
     */
    protected function postBuild()
    {
        $this->getLogger()->info('finished creating the custom output file');

        return $this;
    }
}

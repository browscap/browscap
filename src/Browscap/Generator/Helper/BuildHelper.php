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

namespace Browscap\Generator\Helper;

use Browscap\Data\DataCollection;
use Browscap\Data\Expander;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class BuildHelper
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string                             $version
     * @param string                             $resourceFolder
     * @param \Psr\Log\LoggerInterface           $logger
     * @param \Browscap\Writer\WriterCollection  $writerCollection
     * @param \Browscap\Helper\CollectionCreator $collectionCreator
     *
     * @return void
     * @throws \Exception
     */
    public static function run(
        string $version,
        string $resourceFolder,
        LoggerInterface $logger,
        WriterCollection $writerCollection,
        CollectionCreator $collectionCreator,
        bool $collectPatternIds = false
    ) {
        $logger->info('started creating a data collection');

        $dataCollection = new DataCollection($version);
        $dataCollection->setLogger($logger);

        $collectionCreator
            ->setLogger($logger)
            ->setDataCollection($dataCollection);

        $collection = $collectionCreator->createDataCollection($resourceFolder);

        $logger->info('finished creating a data collection');

        $logger->info('started initialisation of writers');

        $expander = new Expander();
        $expander
            ->setDataCollection($collection)
            ->setLogger($logger);

        $logger->info('finished initialisation of writers');

        $logger->info('started output of header and version');

        $comments = [
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T'),
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.',
        ];

        $writerCollection
            ->setExpander($expander)
            ->fileStart()
            ->renderHeader($comments)
            ->renderVersion($version, $collection);

        $logger->info('finished output of header and version');

        $output = [];

        $logger->info('started output of divisions');

        $division = $collection->getDefaultProperties();

        $logger->info('handle division ' . $division->getName());

        $writerCollection
            ->renderAllDivisionsHeader($collection)
            ->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = [$ua[0]['userAgent'] => $ua[0]['properties']];

        foreach (array_keys($sections) as $sectionName) {
            $section = $sections[$sectionName];

            if (!$collectPatternIds) {
                unset($section['PatternId']);
            }

            $writerCollection
                ->setSilent($division)
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections, $sectionName)
                ->renderSectionFooter($sectionName);
        }

        $writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            /** @var \Browscap\Data\Division $division */

            // run checks on division before expanding versions because the checked properties do not change between
            // versions
            $sections = $expander->expand($division, $division->getName());

            $logger->info('checking division ' . $division->getName());

            foreach (array_keys($sections) as $sectionName) {
                $section = $sections[$sectionName];

                $collection->checkProperty($sectionName, $section);
            }

            $writerCollection->setSilent($division);

            $versions = $division->getVersions();

            foreach ($versions as $version) {
                list($majorVer, $minorVer) = $expander->getVersionParts($version);

                $divisionName = $expander->parseProperty($division->getName(), $majorVer, $minorVer);

                $logger->info('handle division ' . $divisionName);

                $encodedSections = json_encode($sections);
                $encodedSections = $expander->parseProperty($encodedSections, $majorVer, $minorVer);

                $sectionsWithVersion = json_decode($encodedSections, true);
                $firstElement        = current($sectionsWithVersion);

                $writerCollection->renderDivisionHeader($divisionName, $firstElement['Parent']);

                foreach (array_keys($sectionsWithVersion) as $sectionName) {
                    if (array_key_exists($sectionName, $output)) {
                        $logger->error(
                            'tried to add section "' . $sectionName . '" from "' . $division->getName() . '" more than once -> skipped'
                        );
                        continue;
                    }

                    $section = $sectionsWithVersion[$sectionName];

                    if (!$collectPatternIds) {
                        unset($section['PatternId']);
                    }

                    $writerCollection->setSilentSection($section);

                    $writerCollection
                        ->renderSectionHeader($sectionName)
                        ->renderSectionBody($section, $collection, $sectionsWithVersion, $sectionName)
                        ->renderSectionFooter($sectionName);

                    $output[$sectionName] = 1;
                }

                $writerCollection->renderDivisionFooter();

                unset($divisionName, $majorVer, $minorVer);
            }
        }

        $division = $collection->getDefaultBrowser();

        $logger->info('handle division ' . $division->getName());

        $writerCollection->renderDivisionHeader($division->getName());

        $ua       = $division->getUserAgents();
        $sections = [
            $ua[0]['userAgent'] => array_merge(
                ['Parent' => 'DefaultProperties'],
                $ua[0]['properties']
            ),
        ];

        foreach (array_keys($sections) as $sectionName) {
            $section = $sections[$sectionName];

            if (!$collectPatternIds) {
                unset($section['PatternId']);
            }

            $writerCollection
                ->setSilent($division)
                ->renderSectionHeader($sectionName)
                ->renderSectionBody($section, $collection, $sections, $sectionName)
                ->renderSectionFooter($sectionName);
        }

        $writerCollection
            ->renderDivisionFooter()
            ->renderAllDivisionsFooter();

        $logger->info('finished output of divisions');

        $logger->info('started closing writers');

        $writerCollection
            ->fileEnd()
            ->close();

        $logger->info('finished closing writers');
    }
}

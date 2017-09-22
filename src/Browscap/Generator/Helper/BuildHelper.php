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
namespace Browscap\Generator\Helper;

use Browscap\Data\Expander;
use Browscap\Data\Helper\CheckProperties;
use Browscap\Data\Helper\SplitVersion;
use Browscap\Data\Helper\VersionNumber;
use Browscap\Helper\CollectionCreator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

/**
 * Class BuildGenerator
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 * @author     Thomas Müller <mimmi20@live.de>
 */
class BuildHelper
{
    /**
     * Entry point for generating builds for a specified version
     *
     * @param string                             $buildVersion
     * @param string                             $resourceFolder
     * @param \Psr\Log\LoggerInterface           $logger
     * @param \Browscap\Writer\WriterCollection  $writerCollection
     * @param \Browscap\Helper\CollectionCreator $collectionCreator
     * @param bool                               $collectPatternIds
     *
     * @throws \Exception
     *
     * @return void
     */
    public static function run(
        string $buildVersion,
        string $resourceFolder,
        LoggerInterface $logger,
        WriterCollection $writerCollection,
        CollectionCreator $collectionCreator,
        bool $collectPatternIds = false
    ) : void {
        $logger->info('started creating a data collection');

        $collection = $collectionCreator->createDataCollection($resourceFolder);

        $logger->info('finished creating a data collection');

        $logger->info('started initialisation of writers');

        $expander = new Expander($logger, $collection);

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

        $writerCollection->setExpander($expander);
        $writerCollection->fileStart();
        $writerCollection->renderHeader($comments);
        $writerCollection->renderVersion($buildVersion, $collection);

        $logger->info('finished output of header and version');

        $output = [];

        $logger->info('started output of divisions');

        $defaultProperties = $collection->getDefaultProperties();

        $logger->info('handle division ' . $defaultProperties->getName());

        $writerCollection->renderAllDivisionsHeader($collection);
        $writerCollection->renderDivisionHeader($defaultProperties->getName());

        $ua       = $defaultProperties->getUserAgents()[0];
        $sections = [$ua->getUserAgent() => $ua->getProperties()];

        foreach (array_keys($sections) as $sectionName) {
            $section = $sections[$sectionName];

            if (!$collectPatternIds) {
                unset($section['PatternId']);
            }

            $writerCollection->setSilent($defaultProperties);
            $writerCollection->renderSectionHeader($sectionName);
            $writerCollection->renderSectionBody($section, $collection, $sections, $sectionName);
            $writerCollection->renderSectionFooter($sectionName);
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

                (new CheckProperties())->check($sectionName, $section);
            }

            $writerCollection->setSilent($division);

            $versions = $division->getVersions();

            foreach ($versions as $version) {
                [$majorVer, $minorVer] = (new SplitVersion())->getVersionParts((string) $version);

                $divisionName = (new VersionNumber())->replace($division->getName(), (string) $majorVer, (string) $minorVer);

                $logger->info('handle division ' . $divisionName);

                $encodedSections = json_encode($sections);
                $encodedSections = (new VersionNumber())->replace($encodedSections, $majorVer, $minorVer);

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

                    $writerCollection->renderSectionHeader($sectionName);
                    $writerCollection->renderSectionBody($section, $collection, $sectionsWithVersion, $sectionName);
                    $writerCollection->renderSectionFooter($sectionName);

                    $output[$sectionName] = 1;
                }

                $writerCollection->renderDivisionFooter();

                unset($divisionName, $majorVer, $minorVer);
            }
        }

        $defaultBrowser = $collection->getDefaultBrowser();

        $logger->info('handle division ' . $defaultBrowser->getName());

        $writerCollection->renderDivisionHeader($defaultBrowser->getName());

        $ua       = $defaultBrowser->getUserAgents()[0];
        $sections = [
            $ua->getUserAgent() => array_merge(
                ['Parent' => 'DefaultProperties'],
                $ua->getProperties()
            ),
        ];

        foreach (array_keys($sections) as $sectionName) {
            $section = $sections[$sectionName];

            if (!$collectPatternIds) {
                unset($section['PatternId']);
            }

            $writerCollection->setSilent($defaultBrowser);
            $writerCollection->renderSectionHeader($sectionName);
            $writerCollection->renderSectionBody($section, $collection, $sections, $sectionName);
            $writerCollection->renderSectionFooter($sectionName);
        }

        $writerCollection->renderDivisionFooter();
        $writerCollection->renderAllDivisionsFooter();

        $logger->info('finished output of divisions');

        $logger->info('started closing writers');

        $writerCollection->fileEnd();
        $writerCollection->close();

        $logger->info('finished closing writers');
    }
}

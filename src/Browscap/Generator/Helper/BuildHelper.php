<?php
declare(strict_types = 1);
namespace Browscap\Generator\Helper;

use Browscap\Data\Expander;
use Browscap\Data\Factory\DataCollectionFactory;
use Browscap\Data\Helper\SplitVersion;
use Browscap\Data\Helper\VersionNumber;
use Browscap\Data\Validator\PropertiesValidator;
use Browscap\Writer\WriterCollection;
use Psr\Log\LoggerInterface;

final class BuildHelper
{
    /**
     * @param string                $buildVersion
     * @param string                $resourceFolder
     * @param LoggerInterface       $logger
     * @param WriterCollection      $writerCollection
     * @param DataCollectionFactory $dataCollectionFactory
     * @param bool                  $collectPatternIds
     *
     * @throws \Exception
     * @throws \Assert\AssertionFailedException
     */
    public static function run(
        string $buildVersion,
        string $resourceFolder,
        LoggerInterface $logger,
        WriterCollection $writerCollection,
        DataCollectionFactory $dataCollectionFactory,
        bool $collectPatternIds = false
    ) : void {
        $logger->info('started creating a data collection');

        $collection = $dataCollectionFactory->createDataCollection($resourceFolder);

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

        $writerCollection->fileStart();
        $writerCollection->renderHeader($comments);
        $writerCollection->renderVersion($buildVersion, $collection);

        $logger->info('finished output of header and version');

        $allSections = [];

        $logger->info('started output of divisions');

        $defaultProperties = $collection->getDefaultProperties();

        $logger->info('handle division ' . $defaultProperties->getName());

        $writerCollection->renderAllDivisionsHeader($collection);
        $writerCollection->renderDivisionHeader($defaultProperties->getName());

        $ua          = $defaultProperties->getUserAgents()[0];
        $sectionName = $ua->getUserAgent();
        $section     = $ua->getProperties();

        if (!$collectPatternIds) {
            unset($section['PatternId']);
        }

        $writerCollection->setSilent($defaultProperties);
        $writerCollection->renderSectionHeader($sectionName);
        $writerCollection->renderSectionBody($section, $collection, [$sectionName => $section], $sectionName);
        $writerCollection->renderSectionFooter($sectionName);
        $writerCollection->renderDivisionFooter();

        foreach ($collection->getDivisions() as $division) {
            /** @var \Browscap\Data\Division $division */

            // run checks on division before expanding versions because the checked properties do not change between
            // versions
            $sections = $expander->expand($division, $division->getName());

            $logger->info('checking division ' . $division->getName());

            foreach (array_keys($sections) as $sectionName) {
                $section = $sections[$sectionName];

                (new PropertiesValidator())->validate($section, $sectionName);
            }

            $writerCollection->setSilent($division);

            foreach ($division->getVersions() as $version) {
                [$majorVer, $minorVer] = (new SplitVersion())->getVersionParts((string) $version);

                $divisionName = (new VersionNumber())->replace($division->getName(), $majorVer, $minorVer);

                $logger->info('handle division ' . $divisionName);

                $encodedSections = (string) json_encode($sections);
                $encodedSections = (new VersionNumber())->replace($encodedSections, $majorVer, $minorVer);

                $sectionsWithVersion = json_decode($encodedSections, true);
                $firstElement        = current($sectionsWithVersion);

                $writerCollection->renderDivisionHeader($divisionName, $firstElement['Parent']);

                foreach (array_keys($sectionsWithVersion) as $sectionName) {
                    $sectionName = (string) $sectionName;

                    if (array_key_exists($sectionName, $allSections)) {
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

                    $allSections[$sectionName] = 1;
                }

                $writerCollection->renderDivisionFooter();

                unset($divisionName, $majorVer, $minorVer);
            }
        }

        $defaultBrowser = $collection->getDefaultBrowser();

        $logger->info('handle division ' . $defaultBrowser->getName());

        $writerCollection->renderDivisionHeader($defaultBrowser->getName());

        $ua          = $defaultBrowser->getUserAgents()[0];
        $sectionName = $ua->getUserAgent();
        $section     = array_merge(
            ['Parent' => 'DefaultProperties'],
            $ua->getProperties()
        );

        if (!$collectPatternIds) {
            unset($section['PatternId']);
        }

        $writerCollection->setSilent($defaultBrowser);
        $writerCollection->renderSectionHeader($sectionName);
        $writerCollection->renderSectionBody($section, $collection, [$sectionName => $section], $sectionName);
        $writerCollection->renderSectionFooter($sectionName);

        $writerCollection->renderDivisionFooter();
        $writerCollection->renderAllDivisionsFooter();

        $logger->info('finished output of divisions');

        $logger->info('started closing writers');

        $writerCollection->fileEnd();
        $writerCollection->close();

        $logger->info('finished closing writers');
    }
}

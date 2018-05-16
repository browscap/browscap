<?php
declare(strict_types = 1);
namespace Browscap\Command;

use Browscap\Helper\LoggerHelper;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class RewriteDivisionsCommand extends Command
{
    /**
     * @var string
     */
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    protected function configure() : void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('rewrite-divisions')
            ->setDescription('rewrites the resource files for the divisions')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        $browserResourcePath = $input->getOption('resources') . '/user-agents';

        $logger->info('Resource folder: ' . $input->getOption('resources'));

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../templates/');
        $twig   = new \Twig_Environment($loader, [
            'cache' => false,
            'optimizations' => 0,
            'autoescape' => false,
        ]);

        $jsonParser = new JsonParser();

        try {
            $allPlatforms = $jsonParser->parse(file_get_contents($input->getOption('resources') . '/platforms.json'), JsonParser::DETECT_KEY_CONFLICTS | JsonParser::PARSE_TO_ASSOC);
        } catch (ParsingException $e) {
            $logger->critical('File "' . $input->getOption('resources') . '/platforms.json" had invalid JSON. [JSON error: ' . json_last_error_msg() . ']');

            return 1;
        }

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();
        $finder->in($browserResourcePath);

        foreach ($finder as $file) {
            $logger->info('read source file ' . $file->getPathname());

            $json = file_get_contents($file->getPathname());

            try {
                $divisionData = $jsonParser->parse($json, JsonParser::DETECT_KEY_CONFLICTS | JsonParser::PARSE_TO_ASSOC);
            } catch (ParsingException $e) {
                $logger->critical('File "' . $file->getPathname() . '" had invalid JSON. [JSON error: ' . json_last_error_msg() . ']');

                continue;
            }

            if (!isset($divisionData['userAgents']) || !is_array($divisionData['userAgents'])) {
                continue;
            }

            if (isset($divisionData['versions'])
                && is_array($divisionData['versions'])
                && 1 < count($divisionData['versions'])
            ) {
                $majorVersions = [];
                $minorVersions = [];
                $keyVersions   = [];

                foreach ($divisionData['versions'] as $key => $version) {
                    $parts = explode('.', (string) $version, 2);

                    $majorVersions[$key] = (int) $parts[0];

                    if (!isset($parts[1]) || '0' === $parts[1]) {
                        $divisionData['versions'][$key] = (int) $version;
                    } else {
                        $divisionData['versions'][$key] = (string) $version;
                    }

                    if (isset($parts[1])) {
                        $minorVersions[$key] = (int) $parts[1];
                    } else {
                        $minorVersions[$key] = 0;
                    }

                    $keyVersions[$key] = $key;
                }

                array_multisort(
                    $majorVersions,
                    SORT_DESC,
                    SORT_NUMERIC,
                    $minorVersions,
                    SORT_DESC,
                    SORT_NUMERIC,
                    $keyVersions,
                    SORT_ASC,
                    SORT_NUMERIC,
                    $divisionData['versions']
                );

                foreach ($divisionData['versions'] as $key => $version) {
                    $divisionData['versions'][$key] = json_encode($version);
                }
            } elseif (isset($divisionData['versions']) && is_array($divisionData['versions'])) {
                foreach ($divisionData['versions'] as $key => $version) {
                    $majorVersions[$key] = (float) $version;

                    $parts = explode('.', (string) $version, 2);

                    if (!isset($parts[1]) || '0' === $parts[1]) {
                        $divisionData['versions'][$key] = json_encode((int) $version);
                    } else {
                        $divisionData['versions'][$key] = json_encode((string) $version);
                    }
                }
            }

            foreach ($divisionData['userAgents'] as &$useragentData) {
                if (isset($useragentData['properties'])) {
                    unset(
                        $useragentData['properties']['Browser'],
                        $useragentData['properties']['Browser_Type'],
                        $useragentData['properties']['Browser_Maker'],
                        $useragentData['properties']['isSyndicationReader'],
                        $useragentData['properties']['Crawler'],
                        $useragentData['properties']['Division']
                    );

                    if (empty($useragentData['properties'])) {
                        unset($useragentData['properties']);
                    }
                }

                if (!isset($useragentData['children'])) {
                    continue;
                }

                if (!is_array($useragentData['children'])) {
                    continue;
                }

                foreach ($useragentData['children'] as &$childData) {
                    if (isset($childData['properties'])) {
                        unset(
                            $childData['properties']['Browser'],
                            $childData['properties']['Browser_Type'],
                            $childData['properties']['Browser_Maker'],
                            $childData['properties']['isSyndicationReader'],
                            $childData['properties']['Crawler'],
                            $childData['properties']['Division']
                        );

                        if (empty($childData['properties'])) {
                            unset($childData['properties']);
                        }
                    }

                    if (!isset($childData['platforms'])) {
                        continue;
                    }

                    if (!is_array($childData['platforms'])) {
                        continue;
                    }

                    if (1 >= count($childData['platforms'])) {
                        foreach ($childData['platforms'] as $key => $platformkey) {
                            unset($childData['platforms'][$key]);
                            $childData['platforms'][] = [$key => json_encode($platformkey)];
                        }

                        continue;
                    }

                    $platforms = $this->sortPlatforms(array_unique($childData['platforms']), array_keys($allPlatforms['platforms']));

                    $currentPlatform = ['name' => '', 'major-version' => 0, 'minor-version' => 0, 'key' => ''];
                    $currentChunk    = -1;
                    $chunk           = [];

                    foreach ($platforms as $key => $platformkey) {
                        $platform = $allPlatforms['platforms'][$platformkey];

                        if ((!isset($platform['properties']['Platform']) || !isset($platform['properties']['Platform_Version']))
                            && isset($platform['inherits'])
                        ) {
                            if (isset($platform['properties'])) {
                                $platformProperties = $platform['properties'];
                            } else {
                                $platformProperties = [];
                            }

                            do {
                                $parentPlatform     = $allPlatforms['platforms'][$platform['inherits']];
                                $platformProperties = array_merge($parentPlatform['properties'], $platformProperties);
                                unset($platform['inherits']);

                                if (isset($parentPlatform['inherits'])) {
                                    $platform['inherits'] = $parentPlatform['inherits'];
                                }
                            } while (isset($platform['inherits']));
                        } else {
                            $platformProperties = $platform['properties'];
                        }

                        $split = explode('.', $platformProperties['Platform_Version'], 2);

                        if (!isset($split[1])) {
                            $split[1] = 0;
                        }

                        if (in_array($platformkey, ['OSX', 'OSX_B', 'iOS_C', 'iOS_A', 'OSX_C', 'OSX_PPC', 'iOS_A_dynamic', 'iOS_C_dynamic'])) {
                            ++$currentChunk;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif (false !== mb_strpos($currentPlatform['key'], 'WinXPb') && false !== mb_strpos($platformkey, 'WinXPa')) {
                            ++$currentChunk;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif (false !== mb_strpos($currentPlatform['key'], 'WinXPa') && false !== mb_strpos($platformkey, 'WinXPb')) {
                            ++$currentChunk;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif ($platformProperties['Platform'] !== $currentPlatform['name']
                            || $split[0] !== $currentPlatform['major-version']
                        ) {
                            ++$currentChunk;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif (is_numeric($platformProperties['Platform_Version']) && $split[1] > $currentPlatform['minor-version']) {
                            ++$currentChunk;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } else {
                            $chunk[$currentChunk][] = json_encode($platformkey);
                        }
                    }

                    $childData['platforms'] = $chunk;
                }
            }

            try {
                $normalized = $twig->render('division.json.twig', ['divisionData' => $divisionData]);
            } catch (\Twig_Error_Loader | \Twig_Error_Runtime | \Twig_Error_Syntax $e) {
                $logger->critical($e);

                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }

        $output->writeln('Done');

        return 0;
    }

    /**
     * @param array $platforms
     * @param array $allPlatforms
     *
     * @return array
     */
    private function sortPlatforms(array $platforms, array $allPlatforms)
    {
        $platformVersions = [];

        foreach ($platforms as $key => $platform) {
            $x = array_intersect($allPlatforms, [$platform]);

            $platformVersions[$key] = key($x);
        }

        array_multisort(
            $platformVersions,
            SORT_NUMERIC,
            SORT_ASC,
            $platforms
        );

        return $platforms;
    }
}

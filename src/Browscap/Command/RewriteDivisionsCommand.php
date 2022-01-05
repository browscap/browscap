<?php

declare(strict_types=1);

namespace Browscap\Command;

use Browscap\Helper\LoggerHelper;
use Exception;
use JsonException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

use function array_intersect;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_multisort;
use function array_unique;
use function assert;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function in_array;
use function is_array;
use function is_int;
use function is_numeric;
use function is_string;
use function json_decode;
use function json_encode;
use function key;
use function mb_strpos;
use function sprintf;
use function uksort;

use const JSON_THROW_ON_ERROR;
use const SORT_ASC;
use const SORT_DESC;
use const SORT_NUMERIC;

class RewriteDivisionsCommand extends Command
{
    private const DEFAULT_RESOURCES_FOLDER = '/../../../resources';

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $defaultResourceFolder = __DIR__ . self::DEFAULT_RESOURCES_FOLDER;

        $this
            ->setName('rewrite-divisions')
            ->setDescription('rewrites the resource files for the divisions')
            ->addOption('resources', null, InputOption::VALUE_REQUIRED, 'Where the resource files are located', $defaultResourceFolder);
    }

    /**
     * @return int 0 if everything went fine, or an error code
     *
     * @throws InvalidArgumentException
     * @throws DirectoryNotFoundException
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loggerHelper = new LoggerHelper();
        $logger       = $loggerHelper->create($output);

        $resources = $input->getOption('resources');
        assert(is_string($resources));

        $divisionsResourcePath = $resources . '/user-agents';

        $logger->info('Resource folder: ' . $resources);

        $loader = new FilesystemLoader(__DIR__ . '/../../../templates/');
        $twig   = new Environment($loader, [
            'cache' => false,
            'optimizations' => 0,
            'autoescape' => false,
        ]);

        $content = file_get_contents($resources . '/platforms/platforms.json');

        if ($content === false) {
            $logger->critical('could not read File "' . $resources . '/platforms.json"');

            return self::FAILURE;
        }

        try {
            $allPlatforms = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $logger->critical(new Exception(sprintf('file "%s" is not valid', $resources . '/platforms.json'), 0, $e));

            return self::FAILURE;
        }

        assert(is_array($allPlatforms));

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();
        $finder->in($divisionsResourcePath);

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $logger->info('read source file ' . $file->getPathname());

            try {
                $json = $file->getContents();
            } catch (RuntimeException $e) {
                $logger->critical(new Exception(sprintf('could not read file "%s"', $file->getPathname()), 0, $e));

                continue;
            }

            try {
                $divisionData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));

                continue;
            }

            assert(is_array($divisionData));

            if (! array_key_exists('division', $divisionData)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! "division" property is missing', $file->getPathname())));

                continue;
            }

            if (! array_key_exists('sortIndex', $divisionData)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! "sortIndex" property is missing', $file->getPathname())));

                continue;
            }

            if (! array_key_exists('lite', $divisionData)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! "lite" property is missing', $file->getPathname())));

                continue;
            }

            if (! array_key_exists('standard', $divisionData)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! "standard" property is missing', $file->getPathname())));

                continue;
            }

            if (! array_key_exists('userAgents', $divisionData)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! userAgents section is missing', $file->getPathname())));

                continue;
            }

            if (! is_array($divisionData['userAgents'])) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! userAgents section is not an array', $file->getPathname())));
                unset($divisionData['userAgents']);

                continue;
            }

            if (array_key_exists('versions', $divisionData)) {
                if (is_array($divisionData['versions'])) {
                    $divisionData['versions'] = $this->sortVersions($divisionData);
                } else {
                    $logger->critical(new Exception(sprintf('file "%s" is not valid! versions section is not an array', $file->getPathname())));
                    unset($divisionData['versions']);
                }
            }

            foreach ($divisionData['userAgents'] as $key => $useragentData) {
                if (! is_int($key)) {
                    $logger->critical(new Exception(sprintf('file "%s" is not valid! not-numeric key in userAgents section found', $file->getPathname())));
                    unset($divisionData['userAgents'][$key]);

                    continue;
                }

                $useragentData = $this->rewriteUserAgents(
                    $useragentData,
                    $file,
                    $logger,
                    $allPlatforms
                );

                if (empty($useragentData)) {
                    $logger->critical(new Exception(sprintf('file "%s" is not valid! userAgents section is empty', $file->getPathname())));
                    unset($divisionData['userAgents'][$key]);

                    continue;
                }

                $divisionData['userAgents'][$key] = $useragentData;
            }

            try {
                $normalized = $twig->render('division.json.twig', ['divisionData' => $divisionData]);
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                $logger->critical($e);

                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }

        $output->writeln('Done');

        return self::SUCCESS;
    }

    /**
     * @param array<string>            $platforms
     * @param array<int, (int|string)> $allPlatforms
     *
     * @return array<string>
     */
    private function sortPlatforms(array $platforms, array $allPlatforms): array
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

    /**
     * @param array<array<string>> $divisionData
     *
     * @return array<string|int>
     *
     * @throws JsonException
     */
    private function sortVersions(array $divisionData): array
    {
        $majorVersions = [];
        $minorVersions = [];
        $keyVersions   = [];

        foreach ($divisionData['versions'] as $key => $version) {
            $parts = explode('.', (string) $version, 2);

            $majorVersions[$key] = (int) $parts[0];

            if (! isset($parts[1]) || $parts[1] === '0') {
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

        assert(is_array($divisionData['versions']));

        foreach ($divisionData['versions'] as $key => $version) {
            $divisionData['versions'][$key] = json_encode($version, JSON_THROW_ON_ERROR);
        }

        assert(is_array($divisionData['versions']));

        return $divisionData['versions'];
    }

    /**
     * @param mixed[]   $useragentData
     * @param mixed[][] $allPlatforms
     *
     * @return mixed[]
     *
     * @throws JsonException
     */
    private function rewriteUserAgents(
        array $useragentData,
        SplFileInfo $file,
        LoggerInterface $logger,
        array $allPlatforms
    ): array {
        if (! array_key_exists('userAgent', $useragentData)) {
            $logger->critical(new Exception(sprintf('file "%s" is not valid! userAgent property is missing', $file->getPathname())));

            return [];
        }

        if (array_key_exists('properties', $useragentData)) {
            if (! is_array($useragentData['properties'])) {
                unset($useragentData['properties']);
            } else {
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
        }

        if (! array_key_exists('children', $useragentData)) {
            return $useragentData;
        }

        if (! is_array($useragentData['children'])) {
            $logger->critical(new Exception(sprintf('file "%s" is not valid! children section is not an array', $file->getPathname())));
            unset($useragentData['children']);

            return $useragentData;
        }

        foreach ($useragentData['children'] as $key => $childData) {
            if (! is_int($key)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! not-numeric key in children section found', $file->getPathname())));
                unset($useragentData['children'][$key]);

                continue;
            }

            $childData = $this->rewriteChildren(
                $childData,
                $file,
                $logger,
                $allPlatforms
            );

            if (empty($childData)) {
                $logger->critical(new Exception(sprintf('file "%s" is not valid! children section is empty', $file->getPathname())));
                unset($useragentData['children'][$key]);

                continue;
            }

            $useragentData['children'][$key] = $childData;
        }

        return $useragentData;
    }

    /**
     * @param mixed[]   $childData
     * @param mixed[][] $allPlatforms
     *
     * @return mixed[]
     *
     * @throws JsonException
     */
    private function rewriteChildren(
        array $childData,
        SplFileInfo $file,
        LoggerInterface $logger,
        array $allPlatforms
    ): array {
        if (! array_key_exists('match', $childData)) {
            $logger->critical(new Exception(sprintf('file "%s" is not valid! match property is missing', $file->getPathname())));

            return [];
        }

        if (array_key_exists('properties', $childData)) {
            if (! is_array($childData['properties'])) {
                unset($childData['properties']);
            } else {
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
        }

        if (array_key_exists('devices', $childData)) {
            if (is_array($childData['devices'])) {
                //ksort($childData['devices']);
                uksort($childData['devices'], 'strcasecmp');
            } else {
                unset($childData['devices']);
            }
        }

        if (array_key_exists('device', $childData)) {
            $logger->warning(sprintf('file "%s" is not valid! device property is used in section "%s", try to use the devices property', $file->getPathname(), $childData['match']));
        }

        if (! array_key_exists('platforms', $childData)) {
            return $childData;
        }

        if (! is_array($childData['platforms'])) {
            unset($childData['platforms']);

            return $childData;
        }

        if (1 >= count($childData['platforms'])) {
            foreach ($childData['platforms'] as $key => $platformkey) {
                unset($childData['platforms'][$key]);
                $childData['platforms'][] = [$key => json_encode($platformkey, JSON_THROW_ON_ERROR)];
            }

            return $childData;
        }

        $platforms = $this->sortPlatforms(array_unique($childData['platforms']), array_keys($allPlatforms));

        $currentPlatform = ['name' => '', 'major-version' => 0, 'minor-version' => 0, 'key' => ''];
        $currentChunk    = -1;
        $chunk           = [];

        foreach ($platforms as $platformkey) {
            $platform = $allPlatforms[$platformkey];

            assert(is_array($platform));

            if (
                (! isset($platform['properties']['Platform']) || ! isset($platform['properties']['Platform_Version']))
                && isset($platform['inherits'])
            ) {
                if (isset($platform['properties'])) {
                    $platformProperties = $platform['properties'];
                } else {
                    $platformProperties = [];
                }

                do {
                    $parentPlatform = $allPlatforms[$platform['inherits']];

                    assert(is_array($parentPlatform));

                    $platformProperties = array_merge($parentPlatform['properties'], $platformProperties);
                    unset($platform['inherits']);

                    if (! isset($parentPlatform['inherits'])) {
                        continue;
                    }

                    $platform['inherits'] = $parentPlatform['inherits'];
                } while (isset($platform['inherits']));
            } else {
                $platformProperties = $platform['properties'];
            }

            $split = explode('.', $platformProperties['Platform_Version'], 2);

            if (! isset($split[1])) {
                $split[1] = 0;
            }

            if (in_array($platformkey, ['OSX', 'OSX_B', 'iOS_C', 'iOS_A', 'OSX_C', 'OSX_PPC', 'iOS_A_dynamic', 'iOS_A_dynamic_11+', 'iOS_C_dynamic', 'iOS_C_dynamic_11+', 'ipadOS_dynamic'])) {
                ++$currentChunk;
                $chunk[$currentChunk] = [json_encode($platformkey, JSON_THROW_ON_ERROR)];
                $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
            } elseif (mb_strpos($currentPlatform['key'], 'WinXPb') !== false && mb_strpos($platformkey, 'WinXPa') !== false) {
                ++$currentChunk;
                $chunk[$currentChunk] = [json_encode($platformkey, JSON_THROW_ON_ERROR)];
                $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
            } elseif (mb_strpos($currentPlatform['key'], 'WinXPa') !== false && mb_strpos($platformkey, 'WinXPb') !== false) {
                ++$currentChunk;
                $chunk[$currentChunk] = [json_encode($platformkey, JSON_THROW_ON_ERROR)];
                $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
            } elseif (
                $platformProperties['Platform'] !== $currentPlatform['name']
                || $split[0] !== $currentPlatform['major-version']
            ) {
                ++$currentChunk;
                $chunk[$currentChunk] = [json_encode($platformkey, JSON_THROW_ON_ERROR)];
                $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
            } elseif (is_numeric($platformProperties['Platform_Version']) && $split[1] > $currentPlatform['minor-version']) {
                ++$currentChunk;
                $chunk[$currentChunk] = [json_encode($platformkey, JSON_THROW_ON_ERROR)];
                $currentPlatform      = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
            } else {
                $chunk[$currentChunk][] = json_encode($platformkey, JSON_THROW_ON_ERROR);
            }
        }

        $childData['platforms'] = $chunk;

        return $childData;
    }
}

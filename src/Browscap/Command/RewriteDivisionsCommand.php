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
use Localheinz\Json\Normalizer;
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
        $twig = new \Twig_Environment($loader, array(
            'cache' => false,
            'optimizations' => 0,
            'autoescape' => false,
        ));

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

                    $platforms = $this->sortPlatforms(array_unique($childData['platforms']));

                    $currentPlatform = ['name' => '', 'major-version' => 0, 'minor-version' => 0, 'key' => ''];
                    $currentChunk = -1;
                    $chunk = [];

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
                                $parentPlatform = $allPlatforms['platforms'][$platform['inherits']];
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
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif (false !== strpos($currentPlatform['key'], 'WinXPb') && false !== strpos($platformkey, 'WinXPa')) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif (false !== strpos($currentPlatform['key'], 'WinXPa') && false !== strpos($platformkey, 'WinXPb')) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif ($platformProperties['Platform'] !== $currentPlatform['name']
                            || $split[0] != $currentPlatform['major-version']
                        ) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
                        } elseif (is_numeric($platformProperties['Platform_Version']) && $split[1] > $currentPlatform['minor-version']) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $currentPlatform = ['name' => $platformProperties['Platform'], 'major-version' => $split[0], 'minor-version' => $split[1], 'key' => $platformkey];
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
     *
     * @return array
     */
    private function sortPlatforms(array $platforms)
    {
        $platformVersions = [];

        $allplatforms = [
            'WinXPa_cygwin',
            'CygWin',
            'Win10_x64_B',
            'Win10_64_B',
            'Win10_32_B',
            'Win10_x64',
            'Win10_64',
            'Win10_32',
            'WinRT8_1_32',
            'Win8_1_x64',
            'Win8_1_64',
            'Win8_1_32',
            'WinRT8_32',
            'Win8_x64_2',
            'Win8_x64',
            'Win8_64',
            'Win8_32',
            'Win7_x64_2',
            'Win7_x64_Trident',
            'Win7_x64',
            'Win7_64_Trident',
            'Win7_64',
            'Win7_32',
            'Win7_32_2',
            'Vista_x64_2',
            'Vista_x64',
            'Vista_64_Trident',
            'Vista_64',
            'Vista_32',
            'WinXPb_x64_3',
            'WinXPb_x64_2',
            'WinXPb_x64',
            'WinXPb_64',
            'WinXPb_32',
            'WinXPa_x64_2',
            'WinXPa_x64',
            'WinXPa_64',
            'WinXPa_32',
            'WinXPa_v2',
            'Win2000_3',
            'Win2000_2',
            'Win2000_x64',
            'Win2000_64',
            'Win2000',
            'Win2000_5',
            'NT4_1',
            'NT4_64',
            'NT4',
            'NT3.5',
            'NT3.1',
            'NT_x64',
            'NT_64',
            'NT',
            'NT4_1_B',
            'WinME_2',
            'WinME_3',
            'Win98_2',
            'Win98_3',
            'Win95_2',
            'Win3.11_B',
            'Win3.1_B',
            'Windows_64',
            'Windows',
            'WinXPb_v2',
            'WinXPa_v3',
            'Win2000_6',
            'Win2000_4',
            'NT4_B',
            'NT3.51',
            'NT_2',
            'WinME',
            'Win98',
            'Win95',
            'Win3.11',
            'Win3.1',
            'Win32',
            'Win16',
            'OSX_PPC_3',
            'OSX_PPC_10_5',
            'OSX_PPC_10_4',
            'OSX_PPC',
            'OSX_PPC_2',
            'OSX_10_13',
            'OSX_10_12',
            'OSX_10_11',
            'OSX_10_10',
            'OSX_10_9',
            'OSX_10_8',
            'OSX_10_7',
            'OSX_10_6',
            'OSX_10_5',
            'OSX_10_4',
            'OSX_10_3',
            'OSX_10_2',
            'OSX_10_1',
            'OSX',
            'OSX_B_10_12',
            'OSX_B_10_11',
            'OSX_B_10_10',
            'OSX_B_10_9',
            'OSX_B_10_8',
            'OSX_B_10_7',
            'OSX_B_10_6',
            'OSX_B_10_5',
            'OSX_B_10_4',
            'OSX_B_10_3',
            'OSX_B_10_2',
            'OSX_B_10_1',
            'OSX_B',
            'OSX_C_10_12',
            'OSX_C_10_11',
            'OSX_C_10_10',
            'OSX_C_10_9',
            'OSX_C_10_8',
            'OSX_C_10_7',
            'OSX_C_10_6',
            'OSX_C_10_5',
            'OSX_C',
            'ATV_OSX_8_3',
            'ATV_OSX',
            'MacPPC_3',
            'MacPPC_2',
            'MacPPC',
            'Mac68K_2',
            'Mac68K',
            'FreeBSD_64',
            'FreeBSD_64_2',
            'FreeBSD',
            'OpenBSD_64',
            'OpenBSD_64_2',
            'OpenBSD_64_3',
            'OpenBSD',
            'NetBSD_64_2',
            'NetBSD',
            'DragonFlyBSD_64',
            'DragonFlyBSD',
            'BSD Four',
            'HPUX',
            'HPUX_2',
            'IRIX64',
            'IRIX64_2',
            'BeOS',
            'SunOS',
            'OS2',
            'Unix',
            'AIX',
            'Tru64_Unix',
            'Tru64_Unix_B',
            'Solaris',
            'iOS_A_dynamic',
            'iOS_A_11_2',
            'iOS_A_11_1',
            'iOS_A_11_0',
            'iOS_A_10_3',
            'iOS_A_10_2',
            'iOS_A_10_1',
            'iOS_A_10_0',
            'iOS_A_9_3',
            'iOS_A_9_2',
            'iOS_A_9_1',
            'iOS_A_9_0',
            'iOS_A_8_4',
            'iOS_A_8_3',
            'iOS_A_8_2',
            'iOS_A_8_1',
            'iOS_A_8_0',
            'iOS_A_8_0_B',
            'iOS_A_7_1',
            'iOS_A_7_0',
            'iOS_A_6_1',
            'iOS_A_6_0',
            'iOS_A_5_1',
            'iOS_A_5_0',
            'iOS_A_4_3',
            'iOS_A_4_2',
            'iOS_A_4_1',
            'iOS_A_4_0',
            'iOS_A_3_2',
            'iOS_A_3_1',
            'iOS_A_3_0',
            'iOS_A_2_2',
            'iOS_A_2_1',
            'iOS_A_2_0',
            'iOS_A',
            'iOS_C_dynamic',
            'iOS_C_11_2',
            'iOS_C_11_1',
            'iOS_C_11_0',
            'iOS_C_10_3',
            'iOS_C_10_2',
            'iOS_C_10_1',
            'iOS_C_10_0',
            'iOS_C_9_3',
            'iOS_C_9_2',
            'iOS_C_9_1',
            'iOS_C_9_0',
            'iOS_C_8_4',
            'iOS_C_8_3',
            'iOS_C_8_2',
            'iOS_C_8_1',
            'iOS_C_8_0',
            'iOS_C_7_1',
            'iOS_C_7_0',
            'iOS_C_6_1',
            'iOS_C_6_0',
            'iOS_C_5_1',
            'iOS_C_5_0',
            'iOS_C_4_3',
            'iOS_C_4_2',
            'iOS_C_4_1',
            'iOS_C_4_0',
            'iOS_C_3_2',
            'iOS_C_3_1',
            'iOS_C_3_0',
            'iOS_C',
            'iOS_N',
            'iOS_I_10_3',
            'iOS_I_10_2',
            'iOS_I_10_1',
            'iOS_I_8_4',
            'iOS_I_8_0',
            'iOS_I_7_1',
            'iOS_I_7_0',
            'iOS_I',
            'iOS_J_8_0',
            'iOS_J_7_1',
            'iOS_J_7_0',
            'iOS_J_6_1',
            'iOS_J_6_0',
            'iOS_J_5_1',
            'iOS_J',
            'iOS_E_9_3',
            'iOS_E_9_2',
            'iOS_E_9_1',
            'iOS_E_9_0',
            'iOS_E_8_1',
            'iOS_E_8_0',
            'iOS_E_7_1',
            'iOS_E_7_0',
            'iOS_E_6_1',
            'iOS_E_6_0',
            'iOS_E_5_1',
            'iOS_E_5_0',
            'iOS_E_4_3',
            'iOS_E',
            'iOS_G_4_3',
            'iOS_G',
            'iOS_K_8_1',
            'iOS_K_8_0',
            'iOS_K',
            'iOS_O_10_3',
            'iOS_O_10_2',
            'iOS_O_10_1',
            'iOS_O_10_0',
            'iOS_O_9_3',
            'iOS_O_9_2',
            'iOS_O_9_1',
            'iOS_O_9_0',
            'iOS_O_8_4',
            'iOS_O_8_3',
            'iOS_O_8_2',
            'iOS_O_8_1',
            'iOS_O_8_0',
            'iOS_O',
            'iOS_L_8_1',
            'iOS_L_8_0',
            'iOS_L_7_1',
            'iOS_L_7_0',
            'iOS_L',
            'iOS_H_5_0',
            'iOS_H',
            'iOS_M',
            'iOS_B_1_0',
            'iOS_B',
            'iOS_D_8_3',
            'iOS_D_8_2',
            'iOS_D_8_1',
            'iOS_D_8_0',
            'iOS_D_7_1',
            'iOS_D_7_0',
            'iOS_D_6_1',
            'iOS_D_6_0',
            'iOS_D_5_1',
            'iOS_D_5_0',
            'iOS_D',
            'iOS',
            'Darwin_64',
            'Darwin',
            'Tizen_3_0',
            'Tizen_2_4',
            'Tizen_2_3',
            'Tizen_2_2',
            'Tizen_2_1',
            'Tizen_2_0',
            'Tizen',
            'Maemo_ARM',
            'Maemo',
            'ChromeOS_ARM',
            'ChromeOS_x64',
            'ChromeOS',
            'Ubuntu_Touch_15_04',
            'Ubuntu_Touch_14_04',
            'Ubuntu_Touch',
            'Ubuntu_14_04_64',
            'Ubuntu_12_04_64',
            'Ubuntu_11_04_64',
            'Ubuntu_10_10_64',
            'Ubuntu_10_04_64',
            'Ubuntu_08_10_64',
            'Ubuntu_08_04_64',
            'Ubuntu_64',
            'Ubuntu_14_04',
            'Ubuntu_12_04',
            'Ubuntu_11_10',
            'Ubuntu_11_04',
            'Ubuntu_10_10',
            'Ubuntu_10_04',
            'Ubuntu_09_25',
            'Ubuntu_08_10',
            'Ubuntu_08_04',
            'Ubuntu',
            'CentOS',
            'Debian_32_on_x64_2',
            'Debian_64',
            'Debian',
            'Fedora',
            'Red_Hat',
            'Linux_32_on_x64',
            'Linux_32_on_x64_2',
            'Linux_x64',
            'Linux_64_ARM',
            'Linux_64_sparc',
            'Linux_64_amd',
            'Linux_64',
            'Linux_64_B',
            'Linux_PPC',
            'Linux_MIPS',
            'Linux_ARM',
            'Linux_SH4',
            'Mobilinux',
            'Linux',
            'WinPhone10_Continuum',
            'WinPhone10_B',
            'WinPhone10',
            'WinPhone8.1_B',
            'WinPhone8.1',
            'WinPhone8_osmeta',
            'WinPhone8_B',
            'WinPhone8',
            'WinPhone710',
            'WinPhone78',
            'WinPhone75',
            'WinPhone7_C',
            'WinPhone7',
            'WinPhone65',
            'WinPhone',
            'WinMobile',
            'WinCE',
            'Miui_OS_Dynamic',
            'Miui_OS_7_3',
            'Miui_OS',
            'Android_Dynamic',
            'Android_8_1',
            'Android_8_0',
            'Android_7_1',
            'Android_7_0',
            'Android_6_0',
            'Android_5_1',
            'Android_5_0',
            'Android_4_4',
            'Android_4_3',
            'Android_4_2',
            'Android_4_1',
            'Android_4_1_E',
            'Android_4_0',
            'Android_3_2',
            'Android_3_1',
            'Android_3_0',
            'Android_2_3',
            'Android_2_2',
            'Android_2_1',
            'Android_2_0',
            'Android_1_6',
            'Android_1_5',
            'Android_1_1',
            'Android_1_0',
            'Android_1_0_B',
            'Android',
            'Android_H_8_0',
            'Android_H_7_1',
            'Android_H_7_0',
            'Android_H_6_0',
            'Android_H_5_1',
            'Android_H_5_0',
            'Android_H_4_4',
            'Android_H_4_3',
            'Android_H_4_2',
            'Android_H_4_1',
            'Android_H_4_0',
            'Android_H_2_3',
            'Android_H_2_2',
            'Android_H_2_1',
            'Android_H_2_0',
            'Android_H',
            'Android_B_2_3',
            'Android_B',
            'Android_F_8_0',
            'Android_F_7_1',
            'Android_F_7_0',
            'Android_F_6_0',
            'Android_F_5_1',
            'Android_F_5_0',
            'Android_F_4_4',
            'Android_F_4_3',
            'Android_F_4_2',
            'Android_F_4_1',
            'Android_F_4_0',
            'Android_F_3_2',
            'Android_F_3_1',
            'Android_F_3_0',
            'Android_F_2_3',
            'Android_F_2_2',
            'Android_F_2_1',
            'Android_F_2_0',
            'Android_F_1_6',
            'Android_F',
            'Android_D_5_1',
            'Android_D_5_0',
            'Android_D_4_4',
            'Android_D_4_3',
            'Android_D_4_2',
            'Android_D_4_1',
            'Android_D_4_0',
            'Android_D_2_3',
            'Android_D_2_2',
            'Android_D_2_1',
            'Android_D_2_0',
            'Android_D_1_6',
            'Android_D_1_5',
            'Android_D',
            'Android_E_8_0',
            'Android_E_7_1',
            'Android_E_7_0',
            'Android_E_6_0',
            'Android_E_5_1',
            'Android_E_5_0',
            'Android_E_4_4',
            'Android_E_4_3',
            'Android_E_4_2',
            'Android_E_4_1',
            'Android_E_4_0',
            'Android_E_3_2',
            'Android_E_3_1',
            'Android_E_3_0',
            'Android_E_2_3',
            'Android_E_2_2',
            'Android_E_2_1',
            'Android_E_2_0',
            'Android_E',
            'Android_C_4_4',
            'Android_C_4_3',
            'Android_C_4_2',
            'Android_C_4_1',
            'Android_C_4_0',
            'Android_C_3_2',
            'Android_C_3_1',
            'Android_C_3_0',
            'Android_C_2_3',
            'Android_C_2_2',
            'Android_C_2_1',
            'Android_C_2_0',
            'Android_C_1_6',
            'Android_C_1_5',
            'Android_C',
            'Android_G_2_3',
            'Android_G_2_2',
            'Android_G_2_1',
            'Android_G_1_5',
            'Android_G',
            'Android_I_4_4',
            'Android_I_4_3',
            'Android_I_4_2',
            'Android_I_4_1',
            'Android_I_4_0',
            'Android_I',
            'AndroidTablet_8_1',
            'AndroidTablet_8_0',
            'AndroidTablet_7_1',
            'AndroidTablet_7_0',
            'AndroidTablet_6_0',
            'AndroidTablet_5_1',
            'AndroidTablet_5_0',
            'AndroidTablet_4_4',
            'AndroidTablet_4_3',
            'AndroidTablet_4_2',
            'AndroidTablet_4_1',
            'AndroidTablet_4_0',
            'AndroidTablet_3_2',
            'AndroidTablet_3_1',
            'AndroidTablet_3_0',
            'AndroidTablet_2_3',
            'AndroidTablet_2_2',
            'AndroidTablet_2_1',
            'AndroidTablet_2_0',
            'AndroidTablet',
            'AndroidMobile_8_1',
            'AndroidMobile_8_0',
            'AndroidMobile_7_1',
            'AndroidMobile_7_0',
            'AndroidMobile_6_0',
            'AndroidMobile_5_1',
            'AndroidMobile_5_0',
            'AndroidMobile_4_4',
            'AndroidMobile_4_3',
            'AndroidMobile_4_2',
            'AndroidMobile_4_1',
            'AndroidMobile_4_0',
            'AndroidMobile_2_3',
            'AndroidMobile_2_2',
            'AndroidMobile_2_1',
            'AndroidMobile_2_0',
            'AndroidMobile',
            'Android_GoogleTV_3_2',
            'Android_GoogleTV',
            'AndroidGeneric',
            'Maui',
            'Chromecast_OS_64_ARM',
            'Chromecast_OS_ARM',
            'Brew_MP',
            'Brew_4_0',
            'Brew_3_1',
            'Brew_3_0',
            'Brew',
            'Syllable',
            'SymbianOS',
            'SymbianOS_B',
            'SymbianOS_C',
            'SymbianOS_D',
            'JAVA',
            'Amiga',
            'RIM_Tablet_OS_2_1',
            'RIM_Tablet_OS_2_0',
            'RIM_Tablet_OS_1',
            'RIM_Tablet_OS',
            'RIMOS_10_X',
            'RIMOS_Dynamic',
            'RIMOS_7_1',
            'RIMOS_7_0',
            'RIMOS_6_0',
            'RIMOS_5_0',
            'RIMOS_4_7',
            'RIMOS_4_6',
            'RIMOS_4_5',
            'RIMOS_4_3',
            'RIMOS_4_2_B',
            'RIMOS_4_2',
            'RIMOS_4_1',
            'RIMOS_4_0',
            'RIMOS_3_8',
            'RIMOS_3_7',
            'RIMOS_3_6',
            'RIMOS',
            'webOS_B_30',
            'webOS_B',
            'webOS_30',
            'webOS_22',
            'webOS_21',
            'webOS_20',
            'webOS_14',
            'webOS_13',
            'webOS_12',
            'webOS_11',
            'webOS_10',
            'webOS',
            'Bada_2_0',
            'Bada_1_2',
            'Bada_1_0',
            'Bada',
            'Xbox_OS_10_Mobile',
            'Xbox_OS_10',
            'Xbox_OS_Mobile_A',
            'Xbox_OS_Mobile',
            'Xbox_OS',
            'Xbox_360_Mobile',
            'Xbox_360',
            'RISC_OS',
            'FirefoxOS_2_5',
            'FirefoxOS_2_2',
            'FirefoxOS_2_1',
            'FirefoxOS_2_0',
            'FirefoxOS_1_4',
            'FirefoxOS_1_3',
            'FirefoxOS_1_2',
            'FirefoxOS_1_1',
            'FirefoxOS_1_0',
            'FirefoxOS_0_x',
            'FirefoxOS',
            'SailfishOS',
            'MeeGo',
            'OpenVMS',
            'Nintendo3DS_OS',
            'NintendoWiiU_OS',
            'NintendoSwitch_OS',
            'NintendoDS_OS',
            'NintendoWii_OS',
            'NintendoDSi_OS',
            'Series30',
            'Asha',
            'Haiku',
            'CellOS',
            'Inferno',
            'Liberate',
            'OrbisOS',
            'WyderOS',
            'PS_Vita',
            'PalmOS_3',
            'PalmOS',
            'Series40',
            'any',
        ];

        foreach ($platforms as $key => $platform) {
            $x = array_intersect($allplatforms, [$platform]);

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

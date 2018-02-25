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

                    $platforms = $this->sortPlatforms($childData['platforms']);

                    $curentPlatform = ['name' => '', 'version' => 0, 'key' => ''];
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

                        $split = explode('.', $platformProperties['Platform_Version']);

                        if (in_array($platformkey, ['OSX', 'OSX_B', 'iOS_C', 'iOS_A', 'OSX_C', 'OSX_PPC'])) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $curentPlatform = ['name' => $platformProperties['Platform'], 'version' => $split[0], 'key' => $platformkey];
                        } elseif (false !== strpos($curentPlatform['key'], 'WinXPb') && false !== strpos($platformkey, 'WinXPa')) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $curentPlatform = ['name' => $platformProperties['Platform'], 'version' => $split[0], 'key' => $platformkey];
                        } elseif (false !== strpos($curentPlatform['key'], 'WinXPa') && false !== strpos($platformkey, 'WinXPb')) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $curentPlatform = ['name' => $platformProperties['Platform'], 'version' => $split[0], 'key' => $platformkey];
                        } elseif ($platformProperties['Platform'] !== $curentPlatform['name']
                            || $split[0] != $curentPlatform['version']
                        ) {
                            $currentChunk++;
                            $chunk[$currentChunk] = [json_encode($platformkey)];
                            $curentPlatform = ['name' => $platformProperties['Platform'], 'version' => $split[0], 'key' => $platformkey];
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

        foreach ($platforms as $key => $platform) {
            switch ($platform) {
                // @todo: CygWin
                case 'WinXPa_cygwin':
                    $x = 600;
                    break;
                case 'CygWin':
                    $x = 599;
                    break;
                // @todo: Winsows
                case 'Win10_x64_B':
                    $x = 598;
                    break;
                case 'Win10_64_B':
                    $x = 597;
                    break;
                case 'Win10_32_B':
                    $x = 596;
                    break;
                case 'Win10_x64':
                    $x = 595;
                    break;
                case 'Win10_64':
                    $x = 594;
                    break;
                case 'Win10_32':
                    $x = 593;
                    break;
                case 'WinRT8_1_32':
                    $x = 592;
                    break;
                case 'Win8_1_x64':
                    $x = 591;
                    break;
                case 'Win8_1_64':
                    $x = 590;
                    break;
                case 'Win8_1_32':
                    $x = 589;
                    break;
                case 'WinRT8_32':
                    $x = 588;
                    break;
                case 'Win8_x64':
                    $x = 587;
                    break;
                case 'Win8_64':
                    $x = 586;
                    break;
                case 'Win8_32':
                    $x = 585;
                    break;
                case 'Win7_x64_2':
                    $x = 584;
                    break;
                case 'Win7_x64':
                    $x = 583;
                    break;
                case 'Win7_64':
                    $x = 582;
                    break;
                case 'Win7_32':
                    $x = 581;
                    break;
                case 'Win7_32_2':
                    $x = 580;
                    break;
                case 'Vista_x64_2':
                    $x = 579;
                    break;
                case 'Vista_x64':
                    $x = 578;
                    break;
                case 'Vista_64':
                    $x = 577;
                    break;
                case 'Vista_32':
                    $x = 576;
                    break;
                case 'WinXPb_x64_3':
                    $x = 575;
                    break;
                case 'WinXPb_x64_2':
                    $x = 574;
                    break;
                case 'WinXPb_x64':
                    $x = 573;
                    break;
                case 'WinXPb_64':
                    $x = 572;
                    break;
                case 'WinXPb_32':
                    $x = 571;
                    break;
                case 'WinXPa_x64_2':
                    $x = 570;
                    break;
                case 'WinXPa_x64':
                    $x = 569;
                    break;
                case 'WinXPa_64':
                    $x = 568;
                    break;
                case 'WinXPa_32':
                    $x = 567;
                    break;
                case 'WinXPa_v2':
                    $x = 566;
                    break;
                case 'Win2000_3':
                    $x = 565;
                    break;
                case 'Win2000_2':
                    $x = 564;
                    break;
                case 'Win2000_x64':
                    $x = 563;
                    break;
                case 'Win2000_64':
                    $x = 562;
                    break;
                case 'Win2000':
                    $x = 561;
                    break;
                case 'Win2000_5':
                    $x = 560;
                    break;
                case 'NT4_1':
                    $x = 559;
                    break;
                case 'NT4':
                    $x = 558;
                    break;
                case 'NT3.5':
                    $x = 557;
                    break;
                case 'NT3.1':
                    $x = 556;
                    break;
                case 'NT_x64':
                    $x = 555;
                    break;
                case 'NT_64':
                    $x = 554;
                    break;
                case 'NT':
                    $x = 553;
                    break;
                case 'NT4_1_B':
                    $x = 552;
                    break;
                case 'WinME_2':
                    $x = 551;
                    break;
                case 'WinME_3':
                    $x = 550;
                    break;
                case 'Win98_2':
                    $x = 549;
                    break;
                case 'Win95_2':
                    $x = 548;
                    break;
                case 'Win3.11_B':
                    $x = 547;
                    break;
                case 'Win3.1_B':
                    $x = 546;
                    break;
                case 'Windows_64':
                    $x = 545;
                    break;
                case 'Win32_B':
                    $x = 544;
                    break;
                case 'Windows':
                    $x = 543;
                    break;
                case 'WinXPb_v2':
                    $x = 542;
                    break;
                case 'WinXPa_v3':
                    $x = 541;
                    break;
                case 'Win2000_6':
                    $x = 540;
                    break;
                case 'Win2000_4':
                    $x = 539;
                    break;
                case 'NT4_B':
                    $x = 538;
                    break;
                case 'NT3.51':
                    $x = 537;
                    break;
                case 'NT_2':
                    $x = 536;
                    break;
                case 'WinME':
                    $x = 535;
                    break;
                case 'Win98':
                    $x = 534;
                    break;
                case 'Win95':
                    $x = 533;
                    break;
                case 'Win3.11':
                    $x = 532;
                    break;
                case 'Win3.1':
                    $x = 531;
                    break;
                case 'Win32':
                    $x = 530;
                    break;
                case 'Win16':
                    $x = 529;
                    break;
                //@todo: OSX
                case 'OSX_PPC_3':
                    $x = 528;
                    break;
                case 'OSX_PPC_10_5':
                    $x = 527;
                    break;
                case 'OSX_PPC_10_4':
                    $x = 526;
                    break;
                case 'OSX_PPC':
                    $x = 525;
                    break;
                case 'OSX_PPC_2':
                    $x = 524;
                    break;
                case 'OSX_10_13':
                    $x = 523;
                    break;
                case 'OSX_10_12':
                    $x = 522;
                    break;
                case 'OSX_10_11':
                    $x = 521;
                    break;
                case 'OSX_10_10':
                    $x = 520;
                    break;
                case 'OSX_10_9':
                    $x = 519;
                    break;
                case 'OSX_10_8':
                    $x = 518;
                    break;
                case 'OSX_10_7':
                    $x = 517;
                    break;
                case 'OSX_10_6':
                    $x = 516;
                    break;
                case 'OSX_10_5':
                    $x = 515;
                    break;
                case 'OSX_10_4':
                    $x = 514;
                    break;
                case 'OSX_10_3':
                    $x = 513;
                    break;
                case 'OSX_10_2':
                    $x = 512;
                    break;
                case 'OSX_10_1':
                    $x = 511;
                    break;
                case 'OSX':
                    $x = 510;
                    break;
                case 'OSX_B_10_12':
                    $x = 509;
                    break;
                case 'OSX_B_10_11':
                    $x = 508;
                    break;
                case 'OSX_B_10_10':
                    $x = 507;
                    break;
                case 'OSX_B_10_9':
                    $x = 506;
                    break;
                case 'OSX_B_10_8':
                    $x = 505;
                    break;
                case 'OSX_B_10_7':
                    $x = 504;
                    break;
                case 'OSX_B_10_6':
                    $x = 503;
                    break;
                case 'OSX_B_10_5':
                    $x = 502;
                    break;
                case 'OSX_B_10_4':
                    $x = 501;
                    break;
                case 'OSX_B_10_3':
                    $x = 500;
                    break;
                case 'OSX_B_10_2':
                    $x = 499;
                    break;
                case 'OSX_B_10_1':
                    $x = 498;
                    break;
                case 'OSX_B':
                    $x = 497;
                    break;
                case 'OSX_C_10_12':
                    $x = 496;
                    break;
                case 'OSX_C_10_11':
                    $x = 495;
                    break;
                case 'OSX_C_10_10':
                    $x = 494;
                    break;
                case 'OSX_C_10_9':
                    $x = 493;
                    break;
                case 'OSX_C_10_8':
                    $x = 492;
                    break;
                case 'OSX_C_10_7':
                    $x = 491;
                    break;
                case 'OSX_C_10_6':
                    $x = 490;
                    break;
                case 'OSX_C_10_5':
                    $x = 489;
                    break;
                case 'OSX_C':
                    $x = 488;
                    break;
                case 'ATV_OSX_8_3':
                    $x = 487;
                    break;
                case 'ATV_OSX':
                    $x = 486;
                    break;
                case 'MacPPC_3':
                    $x = 485;
                    break;
                case 'MacPPC_2':
                    $x = 484;
                    break;
                case 'MacPPC':
                    $x = 483;
                    break;
                case 'Mac68K_2':
                    $x = 482;
                    break;
                case 'Mac68K':
                    $x = 481;
                    break;
                // @todo: BSD/Unixe
                case 'FreeBSD_64':
                    $x = 480;
                    break;
                case 'FreeBSD_64_2':
                    $x = 479;
                    break;
                case 'FreeBSD':
                    $x = 478;
                    break;
                case 'OpenBSD_64':
                    $x = 477;
                    break;
                case 'OpenBSD_64_2':
                    $x = 476;
                    break;
                case 'OpenBSD_64_3':
                    $x = 475;
                    break;
                case 'OpenBSD':
                    $x = 474;
                    break;
                case 'NetBSD_64_2':
                    $x = 473;
                    break;
                case 'NetBSD':
                    $x = 472;
                    break;
                case 'DragonFlyBSD_64':
                    $x = 471;
                    break;
                case 'DragonFlyBSD':
                    $x = 470;
                    break;
                case 'BSD Four':
                    $x = 469;
                    break;
                case 'HPUX':
                    $x = 468;
                    break;
                case 'HPUX_2':
                    $x = 467;
                    break;
                case 'IRIX64':
                    $x = 466;
                    break;
                case 'IRIX64_2':
                    $x = 465;
                    break;
                case 'BeOS':
                    $x = 464;
                    break;
                case 'SunOS':
                    $x = 463;
                    break;
                case 'OS2':
                    $x = 462;
                    break;
                case 'Unix':
                    $x = 461;
                    break;
                case 'AIX':
                    $x = 460;
                    break;
                case 'Tru64_Unix':
                    $x = 459;
                    break;
                case 'Tru64_Unix_B':
                    $x = 458;
                    break;
                case 'Solaris':
                    $x = 457;
                    break;
                //@todo: iOS
                case 'iOS_A_dynamic':
                    $x = 456;
                    break;
                case 'iOS_A_11_2':
                    $x = 455;
                    break;
                case 'iOS_A_11_1':
                    $x = 454;
                    break;
                case 'iOS_A_11_0':
                    $x = 453;
                    break;
                case 'iOS_A_10_3':
                    $x = 452;
                    break;
                case 'iOS_A_10_2':
                    $x = 451;
                    break;
                case 'iOS_A_10_1':
                    $x = 450;
                    break;
                case 'iOS_A_10_0':
                    $x = 449;
                    break;
                case 'iOS_A_9_3':
                    $x = 448;
                    break;
                case 'iOS_A_9_2':
                    $x = 447;
                    break;
                case 'iOS_A_9_1':
                    $x = 446;
                    break;
                case 'iOS_A_9_0':
                    $x = 445;
                    break;
                case 'iOS_A_8_4':
                    $x = 444;
                    break;
                case 'iOS_A_8_3':
                    $x = 443;
                    break;
                case 'iOS_A_8_2':
                    $x = 442;
                    break;
                case 'iOS_A_8_1':
                    $x = 441;
                    break;
                case 'iOS_A_8_0':
                    $x = 440;
                    break;
                case 'iOS_A_8_0_B':
                    $x = 439;
                    break;
                case 'iOS_A_7_1':
                    $x = 438;
                    break;
                case 'iOS_A_7_0':
                    $x = 437;
                    break;
                case 'iOS_A_6_1':
                    $x = 436;
                    break;
                case 'iOS_A_6_0':
                    $x = 435;
                    break;
                case 'iOS_A_5_1':
                    $x = 434;
                    break;
                case 'iOS_A_5_0':
                    $x = 433;
                    break;
                case 'iOS_A_4_3':
                    $x = 432;
                    break;
                case 'iOS_A_4_2':
                    $x = 431;
                    break;
                case 'iOS_A_4_1':
                    $x = 430;
                    break;
                case 'iOS_A_4_0':
                    $x = 429;
                    break;
                case 'iOS_A_3_2':
                    $x = 428;
                    break;
                case 'iOS_A_3_1':
                    $x = 427;
                    break;
                case 'iOS_A_3_0':
                    $x = 426;
                    break;
                case 'iOS_A_2_2':
                    $x = 425;
                    break;
                case 'iOS_A_2_1':
                    $x = 424;
                    break;
                case 'iOS_A_2_0':
                    $x = 423;
                    break;
                case 'iOS_A':
                    $x = 422;
                    break;
                case 'iOS_C_dynamic':
                    $x = 421;
                    break;
                case 'iOS_C_11_2':
                    $x = 420;
                    break;
                case 'iOS_C_11_1':
                    $x = 419;
                    break;
                case 'iOS_C_11_0':
                    $x = 418;
                    break;
                case 'iOS_C_10_3':
                    $x = 417;
                    break;
                case 'iOS_C_10_2':
                    $x = 416;
                    break;
                case 'iOS_C_10_1':
                    $x = 415;
                    break;
                case 'iOS_C_10_0':
                    $x = 414;
                    break;
                case 'iOS_C_9_3':
                    $x = 413;
                    break;
                case 'iOS_C_9_2':
                    $x = 412;
                    break;
                case 'iOS_C_9_1':
                    $x = 411;
                    break;
                case 'iOS_C_9_0':
                    $x = 410;
                    break;
                case 'iOS_C_8_4':
                    $x = 409;
                    break;
                case 'iOS_C_8_3':
                    $x = 408;
                    break;
                case 'iOS_C_8_2':
                    $x = 407;
                    break;
                case 'iOS_C_8_1':
                    $x = 406;
                    break;
                case 'iOS_C_8_0':
                    $x = 405;
                    break;
                case 'iOS_C_7_1':
                    $x = 404;
                    break;
                case 'iOS_C_7_0':
                    $x = 403;
                    break;
                case 'iOS_C_6_1':
                    $x = 402;
                    break;
                case 'iOS_C_6_0':
                    $x = 401;
                    break;
                case 'iOS_C_5_1':
                    $x = 400;
                    break;
                case 'iOS_C_5_0':
                    $x = 399;
                    break;
                case 'iOS_C_4_3':
                    $x = 398;
                    break;
                case 'iOS_C_4_2':
                    $x = 397;
                    break;
                case 'iOS_C_4_1':
                    $x = 396;
                    break;
                case 'iOS_C_4_0':
                    $x = 395;
                    break;
                case 'iOS_C_3_2':
                    $x = 394;
                    break;
                case 'iOS_C_3_1':
                    $x = 393;
                    break;
                case 'iOS_C_3_0':
                    $x = 392;
                    break;
                case 'iOS_C':
                    $x = 391;
                    break;
                case 'iOS_N':
                    $x = 390;
                    break;
                case 'iOS_I_10_3':
                    $x = 389;
                    break;
                case 'iOS_I_10_2':
                    $x = 388;
                    break;
                case 'iOS_I_10_1':
                    $x = 387;
                    break;
                case 'iOS_I_8_4':
                    $x = 386;
                    break;
                case 'iOS_I_8_0':
                    $x = 385;
                    break;
                case 'iOS_I_7_1':
                    $x = 384;
                    break;
                case 'iOS_I_7_0':
                    $x = 383;
                    break;
                case 'iOS_I':
                    $x = 382;
                    break;
                case 'iOS_J_8_0':
                    $x = 381;
                    break;
                case 'iOS_J_7_1':
                    $x = 380;
                    break;
                case 'iOS_J_7_0':
                    $x = 379;
                    break;
                case 'iOS_J_6_1':
                    $x = 378;
                    break;
                case 'iOS_J_6_0':
                    $x = 377;
                    break;
                case 'iOS_J_5_1':
                    $x = 376;
                    break;
                case 'iOS_J':
                    $x = 375;
                    break;
                case 'iOS_E_9_3':
                    $x = 374;
                    break;
                case 'iOS_E_9_2':
                    $x = 373;
                    break;
                case 'iOS_E_9_1':
                    $x = 372;
                    break;
                case 'iOS_E_9_0':
                    $x = 371;
                    break;
                case 'iOS_E_8_1':
                    $x = 370;
                    break;
                case 'iOS_E_8_0':
                    $x = 369;
                    break;
                case 'iOS_E_7_1':
                    $x = 368;
                    break;
                case 'iOS_E_7_0':
                    $x = 367;
                    break;
                case 'iOS_E_6_1':
                    $x = 366;
                    break;
                case 'iOS_E_6_0':
                    $x = 365;
                    break;
                case 'iOS_E_5_1':
                    $x = 364;
                    break;
                case 'iOS_E_5_0':
                    $x = 363;
                    break;
                case 'iOS_E_4_3':
                    $x = 362;
                    break;
                case 'iOS_E':
                    $x = 361;
                    break;
                case 'iOS_K_8_1':
                    $x = 360;
                    break;
                case 'iOS_K_8_0':
                    $x = 359;
                    break;
                case 'iOS_K':
                    $x = 358;
                    break;
                case 'iOS_O_10_3':
                    $x = 357;
                    break;
                case 'iOS_O_10_2':
                    $x = 356;
                    break;
                case 'iOS_O_10_1':
                    $x = 355;
                    break;
                case 'iOS_O_10_0':
                    $x = 354;
                    break;
                case 'iOS_O_9_3':
                    $x = 353;
                    break;
                case 'iOS_O_9_2':
                    $x = 352;
                    break;
                case 'iOS_O_9_1':
                    $x = 351;
                    break;
                case 'iOS_O_9_0':
                    $x = 350;
                    break;
                case 'iOS_O_8_4':
                    $x = 349;
                    break;
                case 'iOS_O_8_3':
                    $x = 348;
                    break;
                case 'iOS_O_8_2':
                    $x = 347;
                    break;
                case 'iOS_O_8_1':
                    $x = 346;
                    break;
                case 'iOS_O_8_0':
                    $x = 345;
                    break;
                case 'iOS_O':
                    $x = 344;
                    break;
                case 'iOS_L_8_1':
                    $x = 343;
                    break;
                case 'iOS_L_8_0':
                    $x = 342;
                    break;
                case 'iOS_L_7_1':
                    $x = 341;
                    break;
                case 'iOS_L_7_0':
                    $x = 340;
                    break;
                case 'iOS_L':
                    $x = 339;
                    break;
                case 'iOS_H_5_0':
                    $x = 338;
                    break;
                case 'iOS_H':
                    $x = 337;
                    break;
                case 'iOS_M':
                    $x = 336;
                    break;
                case 'iOS_B_1_0':
                    $x = 335;
                    break;
                case 'iOS_B':
                    $x = 334;
                    break;
                case 'iOS_D_8_3':
                    $x = 333;
                    break;
                case 'iOS_D_8_2':
                    $x = 332;
                    break;
                case 'iOS_D_8_1':
                    $x = 331;
                    break;
                case 'iOS_D_8_0':
                    $x = 330;
                    break;
                case 'iOS_D_7_1':
                    $x = 329;
                    break;
                case 'iOS_D_7_0':
                    $x = 328;
                    break;
                case 'iOS_D_6_1':
                    $x = 327;
                    break;
                case 'iOS_D_6_0':
                    $x = 326;
                    break;
                case 'iOS_D_5_1':
                    $x = 325;
                    break;
                case 'iOS_D_5_0':
                    $x = 324;
                    break;
                case 'iOS_D':
                    $x = 323;
                    break;
                case 'iOS':
                    $x = 322;
                    break;
                // @todo: Darwin
                case 'Darwin_64':
                    $x = 321;
                    break;
                case 'Darwin':
                    $x = 320;
                    break;
                // @todo: Tizen
                case 'Tizen_3_0':
                    $x = 319;
                    break;
                case 'Tizen_2_4':
                    $x = 318;
                    break;
                case 'Tizen_2_3':
                    $x = 317;
                    break;
                case 'Tizen_2_2':
                    $x = 316;
                    break;
                case 'Tizen_2_1':
                    $x = 315;
                    break;
                case 'Tizen_2_0':
                    $x = 314;
                    break;
                case 'Tizen':
                    $x = 313;
                    break;
                // @todo: Linuxe
                case 'Maemo_ARM':
                    $x = 312;
                    break;
                case 'Maemo':
                    $x = 311;
                    break;
                case 'ChromeOS_ARM':
                    $x = 310;
                    break;
                case 'ChromeOS_x64':
                    $x = 309;
                    break;
                case 'ChromeOS':
                    $x = 308;
                    break;
                case 'Ubuntu_Touch_15_04':
                    $x = 307;
                    break;
                case 'Ubuntu_Touch_14_04':
                    $x = 306;
                    break;
                case 'Ubuntu_Touch':
                    $x = 305;
                    break;
                case 'Ubuntu_14_04_64':
                    $x = 304;
                    break;
                case 'Ubuntu_12_04_64':
                    $x = 303;
                    break;
                case 'Ubuntu_11_04_64':
                    $x = 302;
                    break;
                case 'Ubuntu_10_10_64':
                    $x = 301;
                    break;
                case 'Ubuntu_10_04_64':
                    $x = 300;
                    break;
                case 'Ubuntu_08_10_64':
                    $x = 299;
                    break;
                case 'Ubuntu_08_04_64':
                    $x = 298;
                    break;
                case 'Ubuntu_64':
                    $x = 297;
                    break;
                case 'Ubuntu_14_04':
                    $x = 296;
                    break;
                case 'Ubuntu_12_04':
                    $x = 295;
                    break;
                case 'Ubuntu_11_10':
                    $x = 294;
                    break;
                case 'Ubuntu_11_04':
                    $x = 293;
                    break;
                case 'Ubuntu_10_10':
                    $x = 292;
                    break;
                case 'Ubuntu_10_04':
                    $x = 291;
                    break;
                case 'Ubuntu_09_25':
                    $x = 290;
                    break;
                case 'Ubuntu_08_10':
                    $x = 289;
                    break;
                case 'Ubuntu_08_04':
                    $x = 288;
                    break;
                case 'Ubuntu':
                    $x = 287;
                    break;
                case 'CentOS':
                    $x = 286;
                    break;
                case 'Debian_32_on_x64_2':
                    $x = 285;
                    break;
                case 'Debian_64':
                    $x = 284;
                    break;
                case 'Debian':
                    $x = 283;
                    break;
                case 'Fedora':
                    $x = 282;
                    break;
                case 'Red_Hat':
                    $x = 281;
                    break;
                case 'Linux_32_on_x64':
                    $x = 280;
                    break;
                case 'Linux_32_on_x64_2':
                    $x = 279;
                    break;
                case 'Linux_x64':
                    $x = 278;
                    break;
                case 'Linux_64_sparc':
                    $x = 277;
                    break;
                case 'Linux_64_amd':
                    $x = 276;
                    break;
                case 'Linux_64':
                    $x = 275;
                    break;
                case 'Linux_64_B':
                    $x = 274;
                    break;
                case 'Linux_PPC':
                    $x = 273;
                    break;
                case 'Linux_MIPS':
                    $x = 272;
                    break;
                case 'Linux_ARM':
                    $x = 271;
                    break;
                case 'Linux_SH4':
                    $x = 270;
                    break;
                case 'Mobilinux':
                    $x = 269;
                    break;
                case 'Linux':
                    $x = 268;
                    break;
                // @todo Windows Phone/Mobile
                case 'WinPhone10_Continuum':
                    $x = 267;
                    break;
                case 'WinPhone10_B':
                    $x = 266;
                    break;
                case 'WinPhone10':
                    $x = 265;
                    break;
                case 'WinPhone8.1_B':
                    $x = 264;
                    break;
                case 'WinPhone8.1':
                    $x = 263;
                    break;
                case 'WinPhone8_osmeta':
                    $x = 262;
                    break;
                case 'WinPhone8_B':
                    $x = 261;
                    break;
                case 'WinPhone8':
                    $x = 260;
                    break;
                case 'WinPhone710':
                    $x = 259;
                    break;
                case 'WinPhone78':
                    $x = 258;
                    break;
                case 'WinPhone75':
                    $x = 257;
                    break;
                case 'WinPhone7_C':
                    $x = 256;
                    break;
                case 'WinPhone7':
                    $x = 255;
                    break;
                case 'WinPhone65':
                    $x = 254;
                    break;
                case 'WinPhone':
                    $x = 253;
                    break;
                case 'WinMobile':
                    $x = 252;
                    break;
                case 'WinCE':
                    $x = 251;
                    break;
                // @todo: Miui OS
                case 'Miui_OS_Dynamic':
                    $x = 250;
                    break;
                case 'Miui_OS_7_3':
                    $x = 249;
                    break;
                case 'Miui_OS':
                    $x = 248;
                    break;
                // @todo: Android
                case 'Android_Dynamic':
                    $x = 247;
                    break;
                case 'Android_8_1':
                    $x = 246;
                    break;
                case 'Android_8_0':
                    $x = 245;
                    break;
                case 'Android_7_1':
                    $x = 244;
                    break;
                case 'Android_7_0':
                    $x = 243;
                    break;
                case 'Android_6_0':
                    $x = 242;
                    break;
                case 'Android_5_1':
                    $x = 241;
                    break;
                case 'Android_5_0':
                    $x = 240;
                    break;
                case 'Android_4_4':
                    $x = 239;
                    break;
                case 'Android_4_3':
                    $x = 238;
                    break;
                case 'Android_4_2':
                    $x = 237;
                    break;
                case 'Android_4_1':
                    $x = 236;
                    break;
                case 'Android_4_1_E':
                    $x = 235;
                    break;
                case 'Android_4_0':
                    $x = 234;
                    break;
                case 'Android_3_2':
                    $x = 233;
                    break;
                case 'Android_3_1':
                    $x = 232;
                    break;
                case 'Android_3_0':
                    $x = 231;
                    break;
                case 'Android_2_3':
                    $x = 230;
                    break;
                case 'Android_2_2':
                    $x = 229;
                    break;
                case 'Android_2_1':
                    $x = 228;
                    break;
                case 'Android_2_0':
                    $x = 227;
                    break;
                case 'Android_1_6':
                    $x = 226;
                    break;
                case 'Android_1_5':
                    $x = 225;
                    break;
                case 'Android_1_1':
                    $x = 224;
                    break;
                case 'Android_1_0':
                    $x = 223;
                    break;
                case 'Android_1_0_B':
                    $x = 222;
                    break;
                case 'Android':
                    $x = 221;
                    break;
                case 'Android_H_8_0':
                    $x = 220;
                    break;
                case 'Android_H_7_1':
                    $x = 219;
                    break;
                case 'Android_H_7_0':
                    $x = 218;
                    break;
                case 'Android_H_6_0':
                    $x = 217;
                    break;
                case 'Android_H_5_1':
                    $x = 216;
                    break;
                case 'Android_H_5_0':
                    $x = 215;
                    break;
                case 'Android_H_4_4':
                    $x = 214;
                    break;
                case 'Android_H_4_3':
                    $x = 213;
                    break;
                case 'Android_H_4_2':
                    $x = 212;
                    break;
                case 'Android_H_4_1':
                    $x = 211;
                    break;
                case 'Android_H_4_0':
                    $x = 210;
                    break;
                case 'Android_H_2_3':
                    $x = 209;
                    break;
                case 'Android_H_2_2':
                    $x = 208;
                    break;
                case 'Android_H_2_1':
                    $x = 207;
                    break;
                case 'Android_H_2_0':
                    $x = 206;
                    break;
                case 'Android_H':
                    $x = 205;
                    break;
                case 'Android_B_2_3':
                    $x = 204;
                    break;
                case 'Android_B':
                    $x = 203;
                    break;
                case 'Android_F_8_0':
                    $x = 202;
                    break;
                case 'Android_F_7_1':
                    $x = 201;
                    break;
                case 'Android_F_7_0':
                    $x = 200;
                    break;
                case 'Android_F_6_0':
                    $x = 199;
                    break;
                case 'Android_F_5_1':
                    $x = 198;
                    break;
                case 'Android_F_5_0':
                    $x = 197;
                    break;
                case 'Android_F_4_4':
                    $x = 196;
                    break;
                case 'Android_F_4_3':
                    $x = 195;
                    break;
                case 'Android_F_4_2':
                    $x = 194;
                    break;
                case 'Android_F_4_1':
                    $x = 193;
                    break;
                case 'Android_F_4_0':
                    $x = 192;
                    break;
                case 'Android_F_3_2':
                    $x = 191;
                    break;
                case 'Android_F_3_1':
                    $x = 190;
                    break;
                case 'Android_F_3_0':
                    $x = 189;
                    break;
                case 'Android_F_2_3':
                    $x = 188;
                    break;
                case 'Android_F_2_2':
                    $x = 187;
                    break;
                case 'Android_F_2_1':
                    $x = 186;
                    break;
                case 'Android_F_2_0':
                    $x = 185;
                    break;
                case 'Android_F_1_6':
                    $x = 184;
                    break;
                case 'Android_F':
                    $x = 183;
                    break;
                case 'Android_D_5_1':
                    $x = 182;
                    break;
                case 'Android_D_5_0':
                    $x = 181;
                    break;
                case 'Android_D_4_4':
                    $x = 180;
                    break;
                case 'Android_D_4_3':
                    $x = 179;
                    break;
                case 'Android_D_4_2':
                    $x = 178;
                    break;
                case 'Android_D_4_1':
                    $x = 177;
                    break;
                case 'Android_D_4_0':
                    $x = 176;
                    break;
                case 'Android_D_2_3':
                    $x = 175;
                    break;
                case 'Android_D_2_2':
                    $x = 174;
                    break;
                case 'Android_D_2_1':
                    $x = 173;
                    break;
                case 'Android_D_2_0':
                    $x = 172;
                    break;
                case 'Android_D_1_6':
                    $x = 171;
                    break;
                case 'Android_D_1_5':
                    $x = 170;
                    break;
                case 'Android_D':
                    $x = 169;
                    break;
                case 'Android_E_8_0':
                    $x = 168;
                    break;
                case 'Android_E_7_1':
                    $x = 167;
                    break;
                case 'Android_E_7_0':
                    $x = 166;
                    break;
                case 'Android_E_6_0':
                    $x = 165;
                    break;
                case 'Android_E_5_1':
                    $x = 164;
                    break;
                case 'Android_E_5_0':
                    $x = 163;
                    break;
                case 'Android_E_4_4':
                    $x = 162;
                    break;
                case 'Android_E_4_3':
                    $x = 161;
                    break;
                case 'Android_E_4_2':
                    $x = 160;
                    break;
                case 'Android_E_4_1':
                    $x = 159;
                    break;
                case 'Android_E_4_0':
                    $x = 158;
                    break;
                case 'Android_E_3_2':
                    $x = 157;
                    break;
                case 'Android_E_3_1':
                    $x = 156;
                    break;
                case 'Android_E_3_0':
                    $x = 155;
                    break;
                case 'Android_E_2_3':
                    $x = 154;
                    break;
                case 'Android_E_2_2':
                    $x = 153;
                    break;
                case 'Android_E_2_1':
                    $x = 152;
                    break;
                case 'Android_E_2_0':
                    $x = 151;
                    break;
                case 'Android_E':
                    $x = 150;
                    break;
                case 'Android_C_4_4':
                    $x = 149;
                    break;
                case 'Android_C_4_3':
                    $x = 148;
                    break;
                case 'Android_C_4_2':
                    $x = 147;
                    break;
                case 'Android_C_4_1':
                    $x = 146;
                    break;
                case 'Android_C_4_0':
                    $x = 145;
                    break;
                case 'Android_C_3_2':
                    $x = 144;
                    break;
                case 'Android_C_3_1':
                    $x = 143;
                    break;
                case 'Android_C_3_0':
                    $x = 142;
                    break;
                case 'Android_C_2_3':
                    $x = 141;
                    break;
                case 'Android_C_2_2':
                    $x = 140;
                    break;
                case 'Android_C_2_1':
                    $x = 139;
                    break;
                case 'Android_C_2_0':
                    $x = 138;
                    break;
                case 'Android_C_1_6':
                    $x = 137;
                    break;
                case 'Android_C_1_5':
                    $x = 136;
                    break;
                case 'Android_C':
                    $x = 135;
                    break;
                case 'Android_G_2_3':
                    $x = 134;
                    break;
                case 'Android_G_2_2':
                    $x = 133;
                    break;
                case 'Android_G_2_1':
                    $x = 132;
                    break;
                case 'Android_G_1_5':
                    $x = 131;
                    break;
                case 'Android_G':
                    $x = 130;
                    break;
                case 'Android_I_4_4':
                    $x = 129;
                    break;
                case 'Android_I_4_3':
                    $x = 128;
                    break;
                case 'Android_I_4_2':
                    $x = 127;
                    break;
                case 'Android_I_4_1':
                    $x = 126;
                    break;
                case 'Android_I_4_0':
                    $x = 125;
                    break;
                case 'AndroidTablet_8_1':
                    $x = 124;
                    break;
                case 'AndroidTablet_8_0':
                    $x = 123;
                    break;
                case 'AndroidTablet_7_1':
                    $x = 122;
                    break;
                case 'AndroidTablet_7_0':
                    $x = 121;
                    break;
                case 'AndroidTablet_6_0':
                    $x = 120;
                    break;
                case 'AndroidTablet_5_1':
                    $x = 119;
                    break;
                case 'AndroidTablet_5_0':
                    $x = 118;
                    break;
                case 'AndroidTablet_4_4':
                    $x = 117;
                    break;
                case 'AndroidTablet_4_3':
                    $x = 116;
                    break;
                case 'AndroidTablet_4_2':
                    $x = 115;
                    break;
                case 'AndroidTablet_4_1':
                    $x = 114;
                    break;
                case 'AndroidTablet_4_0':
                    $x = 113;
                    break;
                case 'AndroidTablet_3_2':
                    $x = 112;
                    break;
                case 'AndroidTablet_3_1':
                    $x = 111;
                    break;
                case 'AndroidTablet_3_0':
                    $x = 110;
                    break;
                case 'AndroidTablet_2_3':
                    $x = 109;
                    break;
                case 'AndroidTablet_2_2':
                    $x = 108;
                    break;
                case 'AndroidTablet_2_1':
                    $x = 107;
                    break;
                case 'AndroidTablet_2_0':
                    $x = 106;
                    break;
                case 'AndroidTablet':
                    $x = 105;
                    break;
                case 'AndroidMobile_8_1':
                    $x = 104;
                    break;
                case 'AndroidMobile_8_0':
                    $x = 103;
                    break;
                case 'AndroidMobile_7_1':
                    $x = 102;
                    break;
                case 'AndroidMobile_7_0':
                    $x = 101;
                    break;
                case 'AndroidMobile_6_0':
                    $x = 100;
                    break;
                case 'AndroidMobile_5_1':
                    $x = 99;
                    break;
                case 'AndroidMobile_5_0':
                    $x = 98;
                    break;
                case 'AndroidMobile_4_4':
                    $x = 97;
                    break;
                case 'AndroidMobile_4_3':
                    $x = 96;
                    break;
                case 'AndroidMobile_4_2':
                    $x = 95;
                    break;
                case 'AndroidMobile_4_1':
                    $x = 94;
                    break;
                case 'AndroidMobile_4_0':
                    $x = 93;
                    break;
                case 'AndroidMobile_2_3':
                    $x = 92;
                    break;
                case 'AndroidMobile_2_2':
                    $x = 91;
                    break;
                case 'AndroidMobile_2_1':
                    $x = 90;
                    break;
                case 'AndroidMobile_2_0':
                    $x = 89;
                    break;
                case 'AndroidMobile':
                    $x = 88;
                    break;
                case 'Android_GoogleTV_3_2':
                    $x = 86;
                    break;
                case 'Android_GoogleTV':
                    $x = 85;
                    break;
                case 'AndroidGeneric':
                    $x = 84;
                    break;
                // @todo: Chromecast OS
                case 'Chromecast_OS_64_ARM':
                    $x = 83;
                    break;
                case 'Chromecast_OS_ARM':
                    $x = 82;
                    break;
                // @todo: Brew
                case 'Brew_MP':
                    $x = 81;
                    break;
                case 'Brew_4_0':
                    $x = 80;
                    break;
                case 'Brew_3_1':
                    $x = 79;
                    break;
                case 'Brew_3_0':
                    $x = 78;
                    break;
                case 'Brew':
                    $x = 77;
                    break;
                // @todo: Syllable
                case 'Syllable':
                    $x = 76;
                    break;
                // @todo: SymbianOS
                case 'SymbianOS':
                    $x = 75;
                    break;
                case 'SymbianOS_B':
                    $x = 74;
                    break;
                case 'SymbianOS_C':
                    $x = 73;
                    break;
                case 'SymbianOS_D':
                    $x = 72;
                    break;
                // @todo: Java
                case 'JAVA':
                    $x = 71;
                    break;
                // @todo: AmigaOS
                case 'Amiga':
                    $x = 70;
                    break;
                // @todo: RIM OS
                case 'RIM_Tablet_OS_2_1':
                    $x = 69;
                    break;
                case 'RIM_Tablet_OS_2_0':
                    $x = 68;
                    break;
                case 'RIM_Tablet_OS_1':
                    $x = 67;
                    break;
                case 'RIM_Tablet_OS':
                    $x = 66;
                    break;
                case 'RIMOS_10_X':
                    $x = 65;
                    break;
                case 'RIMOS_Dynamic':
                    $x = 64;
                    break;
                case 'RIMOS_7_1':
                    $x = 63;
                    break;
                case 'RIMOS_7_0':
                    $x = 62;
                    break;
                case 'RIMOS_6_0':
                    $x = 61;
                    break;
                case 'RIMOS_5_0':
                    $x = 60;
                    break;
                case 'RIMOS_4_7':
                    $x = 59;
                    break;
                case 'RIMOS_4_6':
                    $x = 58;
                    break;
                case 'RIMOS_4_5':
                    $x = 57;
                    break;
                case 'RIMOS_4_3':
                    $x = 56;
                    break;
                case 'RIMOS_4_2_B':
                    $x = 55;
                    break;
                case 'RIMOS_4_2':
                    $x = 54;
                    break;
                case 'RIMOS_4_1':
                    $x = 53;
                    break;
                case 'RIMOS_4_0':
                    $x = 52;
                    break;
                case 'RIMOS_3_8':
                    $x = 51;
                    break;
                case 'RIMOS_3_7':
                    $x = 50;
                    break;
                case 'RIMOS_3_6':
                    $x = 49;
                    break;
                case 'RIMOS':
                    $x = 48;
                    break;
                // @todo: WebOS
                case 'webOS_B_30':
                    $x = 47;
                    break;
                case 'webOS_B':
                    $x = 46;
                    break;
                case 'webOS_30':
                    $x = 45;
                    break;
                case 'webOS_22':
                    $x = 44;
                    break;
                case 'webOS_21':
                    $x = 43;
                    break;
                case 'webOS_20':
                    $x = 42;
                    break;
                case 'webOS_14':
                    $x = 41;
                    break;
                case 'webOS_13':
                    $x = 40;
                    break;
                case 'webOS_12':
                    $x = 39;
                    break;
                case 'webOS_11':
                    $x = 38;
                    break;
                case 'webOS_10':
                    $x = 37;
                    break;
                case 'webOS':
                    $x = 36;
                    break;
                // @todo: Bada
                case 'Bada_2_0':
                    $x = 35;
                    break;
                case 'Bada_1_2':
                    $x = 34;
                    break;
                case 'Bada_1_0':
                    $x = 33;
                    break;
                case 'Bada':
                    $x = 32;
                    break;
                // @todo: Xbox OS
                case 'Xbox_OS_10_Mobile':
                    $x = 31;
                    break;
                case 'Xbox_OS_10':
                    $x = 30;
                    break;
                case 'Xbox_OS_Mobile_A':
                    $x = 29;
                    break;
                case 'Xbox_OS_Mobile':
                    $x = 28;
                    break;
                case 'Xbox_OS':
                    $x = 27;
                    break;
                case 'Xbox_360_Mobile':
                    $x = 26;
                    break;
                case 'Xbox_360':
                    $x = 25;
                    break;
                case 'RISC_OS':
                    $x = 24;
                    break;
                // @todo: FirefoxOS
                case 'FirefoxOS_2_5':
                    $x = 23;
                    break;
                case 'FirefoxOS_2_2':
                    $x = 22;
                    break;
                case 'FirefoxOS_2_1':
                    $x = 21;
                    break;
                case 'FirefoxOS_2_0':
                    $x = 20;
                    break;
                case 'FirefoxOS_1_4':
                    $x = 19;
                    break;
                case 'FirefoxOS_1_3':
                    $x = 18;
                    break;
                case 'FirefoxOS_1_2':
                    $x = 17;
                    break;
                case 'FirefoxOS_1_1':
                    $x = 16;
                    break;
                case 'FirefoxOS_1_0':
                    $x = 15;
                    break;
                case 'FirefoxOS_0_x':
                    $x = 14;
                    break;
                case 'FirefoxOS':
                    $x = 13;
                    break;
                // @todo: SailfishOS
                case 'SailfishOS':
                    $x = 12;
                    break;
                case 'MeeGo':
                    $x = 11;
                    break;
                case 'OpenVMS':
                    $x = 10;
                    break;
                case 'Nintendo3DS_OS':
                    $x = 9;
                    break;
                case 'NintendoWiiU_OS':
                    $x = 8;
                    break;
                case 'NintendoSwitch_OS':
                    $x = 7;
                    break;
                case 'NintendoDS_OS':
                    $x = 6;
                    break;
                case 'NintendoWii_OS':
                    $x = 5;
                    break;
                case 'NintendoDSi_OS':
                    $x = 4;
                    break;
                case 'Series30':
                    $x = 3;
                    break;
                case 'Asha':
                    $x = 2;
                    break;
                case 'Haiku':
                    $x = 1;
                    break;
                case 'any':
                default:
                    $x = 0;

            }

            $platformVersions[$key] = $x;
        }

        array_multisort(
            $platformVersions,
            SORT_NUMERIC,
            SORT_DESC,
            $platforms
        );

        return $platforms;
    }
}

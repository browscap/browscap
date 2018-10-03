<?php
declare(strict_types = 1);
namespace Browscap\Command\Helper;

use ExceptionalJSON\DecodeErrorException;
use ExceptionalJSON\EncodeErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Finder\Finder;
use Localheinz\Json\Normalizer;

class Rewrite extends Helper
{
    public function getName() : string
    {
        return 'rewrite';
    }

    /**
     * @param \Psr\Log\LoggerInterface              $logger
     * @param string                                $resources
     * @param string                                $schema
     * @param bool                                  $sort
     */
    public function rewrite(LoggerInterface $logger, string $resources, string $schema, bool $sort = false) : void
    {
        $normalizer = new Normalizer\ChainNormalizer(
            new Normalizer\SchemaNormalizer($schema),
            new Normalizer\JsonEncodeNormalizer(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            new Normalizer\IndentNormalizer('  '),
            new Normalizer\FinalNewLineNormalizer()
        );

        $finder = new Finder();
        $finder->files();
        $finder->name('*.json');
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sortByName();
        $finder->ignoreUnreadableDirs();
        $finder->in($resources);

        foreach ($finder as $file) {
            /* @var \Symfony\Component\Finder\SplFileInfo $file */
            $logger->info('read source file ' . $file->getPathname());

            try {
                $json = $file->getContents();
            } catch (\RuntimeException $e) {
                $logger->critical(new \Exception(sprintf('file "%s" is not readable', $file->getPathname()), 0, $e));
                continue;
            }

            if ($sort) {
                /** @var \Browscap\Command\Helper\Sorter $sorterHelper */
                $sorterHelper = $this->helperSet->get('sorter');

                try {
                    $json = $sorterHelper->sort($json);
                } catch (DecodeErrorException | EncodeErrorException $e) {
                    $logger->critical(new \Exception(sprintf('file "%s" is not valid', $file->getPathname()), 0, $e));
                    continue;
                }
            }

            try {
                $normalized = $normalizer->normalize($json);
            } catch (\InvalidArgumentException $e) {
                $logger->critical(new \Exception(sprintf('an error occured while nomalizing file "%s"', $file->getPathname()), 0, $e));
                continue;
            }

            file_put_contents($file->getPathname(), $normalized);
        }
    }
}

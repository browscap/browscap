<?php

namespace Browscap\Helper;

use Browscap\Generator\BuildGenerator;
use Browscap\Helper\CollectionParser;
use Browscap\Helper\CollectionCreator;
use Browscap\Data\DataCollection;
use Psr\Log\LoggerInterface;

/**
 * @package Browscap\Helper
 * @author Thomas Müller <t_mueller_stolzenhain@yahoo.de>
 */
class Generator
{
    /**
     * @var \Browscap\Generator\AbstractGenerator
     */
    private $generator = null;

    /**
     * @var string
     */
    private $version = null;

    /**
     * @var string
     */
    private $resourceFolder = null;

    /**
     * @var \Browscap\Data\DataCollection
     */
    private $collection = null;

    /**
     * @var array
     */
    private $collectionData = null;

    /**
     * @var \Browscap\Helper\CollectionCreator
     */
    private $collectionCreator = null;

    /**
     * @var \Browscap\Generator\CollectionParser
     */
    private $collectionParser = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * @param \Browscap\Generator\AbstractGenerator $generator
     *
     * @return \Browscap\Helper\Generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @param string $version
     *
     * @return \Browscap\Helper\Generator
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param string $resourceFolder
     *
     * @return \Browscap\Helper\Generator
     */
    public function setResourceFolder($resourceFolder)
    {
        $this->resourceFolder = $resourceFolder;

        return $this;
    }

    /**
     * @param \Browscap\Helper\CollectionCreator $collectionCreator
     *
     * @return \Browscap\Helper\Generator
     */
    public function setCollectionCreator(CollectionCreator $collectionCreator)
    {
        $this->collectionCreator = $collectionCreator;

        return $this;
    }

    /**
     * @param \Browscap\Helper\CollectionParser $collectionParser
     *
     * @return \Browscap\Helper\Generator
     */
    public function setCollectionParser(CollectionParser $collectionParser)
    {
        $this->collectionParser = $collectionParser;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getResourceFolder()
    {
        return $this->resourceFolder;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Helper\Generator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface $logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * creates the required data collection
     *
     * @throws \LogicException
     * @return \Browscap\Data\DataCollection
     */
    public function createCollection()
    {
        if (null === $this->collectionCreator) {
            throw new \LogicException(
                'An instance of \Browscap\Helper\CollectionCreator is required for this function. '
                . 'Please set it with setCollectionCreator'
            );
        }

        $this->getLogger()->info('started creating a data collection');

        $collection = new DataCollection($this->getVersion());

        $this->collectionCreator
            ->setLogger($this->logger)
            ->setDataCollection($collection)
            ->createDataCollection($this->getResourceFolder())
        ;

        $this->getLogger()->info('finished creating a data collection');

        return $collection;
    }

    /**
     * parses the data collection into an array
     *
     * @throws \LogicException
     * @return \Browscap\Helper\Generator
     */
    public function parseCollection()
    {
        if (null === $this->collectionParser) {
            throw new \LogicException(
                'An instance of \Browscap\Generator\CollectionParser is required for this function. '
                . 'Please set it with setCollectionParser'
            );
        }

        $this->getLogger()->info('start parsing a data collection into an array');

        $this
            ->collectionParser
            ->setLogger($this->logger)
            ->setDataCollection($this->collection)
        ;
        $this->collectionData = $this->collectionParser->parse();

        $this->getLogger()->info('finished parsing a data collection into an array');

        return $this;
    }

    /**
     * creates the output file with an given generator
     *
     * @param string $format
     * @param string $type
     *
     * @throws \LogicException
     * @return string
     */
    public function create($format = BuildGenerator::OUTPUT_FORMAT_PHP, $type = BuildGenerator::OUTPUT_TYPE_FULL)
    {
        $this->getLogger()->debug('create the output file');

        if (null === $this->generator) {
            throw new \LogicException(
                'An instance of \Browscap\Generator\AbstractGenerator is required for this function. '
                . 'Please set it with setGenerator'
            );
        }

        $this->getLogger()->info('started creating output files');

        $comments = array(
            'Provided courtesy of http://browscap.org/',
            'Created on ' . $this->collection->getGenerationDate()->format('l, F j, Y \a\t h:i A T'),
            'Keep up with the latest goings-on with the project:',
            'Follow us on Twitter <https://twitter.com/browscap>, or...',
            'Like us on Facebook <https://facebook.com/browscap>, or...',
            'Collaborate on GitHub <https://github.com/browscap>, or...',
            'Discuss on Google Groups <https://groups.google.com/forum/#!forum/browscap>.'
        );

        $this->generator
            ->setLogger($this->logger)
            ->setCollectionData($this->collectionData)
            ->setComments($comments)
            ->setVersionData(
                array(
                    'version' => $this->collection->getVersion(),
                    'released' => $this->collection->getGenerationDate()->format('r')
                )
            )
        ;

        $output = $this->generator->generate($format, $type);

        $this->getLogger()->info('finished creating output files');

        return output;
    }
}

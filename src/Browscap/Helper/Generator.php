<?php

namespace Browscap\Helper;

use Browscap\Generator\CollectionParser;

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
     * @var \Browscap\Generator\DataCollection
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
     * @param \Browscap\Generator\CollectionParser $collectionParser
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
     * creates the required data collection
     *
     * @throws \LogicException
     * @return \Browscap\Helper\Generator
     */
    public function createCollection()
    {
        if (null === $this->collectionCreator) {
            throw new \LogicException(
                'An instance of \\Browscap\\Helper\\CollectionCreator is required for this function. '
                . 'Please set it with setCollectionCreator'
            );
        }

        $this->collection = $this->collectionCreator->createDataCollection(
            $this->getVersion(),
            $this->getResourceFolder()
        );

        return $this;
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
                'An instance of \\Browscap\\Generator\\CollectionParser is required for this function. '
                . 'Please set it with setCollectionParser'
            );
        }

        $this->collectionParser->setDataCollection($this->collection);
        $this->collectionData = $this->collectionParser->parse();

        return $this;
    }

    /**
     * creates the output file with an given generator
     *
     * @return string
     */
    public function create()
    {
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
            ->setCollectionData($this->collectionData)
            ->setComments($comments)
            ->setVersionData(
                array(
                     'version' => $this->collection->getVersion(),
                     'released' => $this->collection->getGenerationDate()->format('r')
                )
            )
        ;

        return $this->generator->generate();
    }
}

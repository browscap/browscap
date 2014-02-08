<?php

namespace Browscap\Helper;

use Browscap\Generator\CollectionParser;

/**
 * @author James Titcumb <james@asgrim.com>
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
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function getGenerator()
    {
        return $this->generator;
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
     * @return \Browscap\Helper\Generator
     */
    public function createCollection()
    {
        $collectionCreator = new CollectionCreator();
        $this->collection = $collectionCreator->createDataCollection($this->getVersion(), $this->getResourceFolder());

        return $this;
    }

    /**
     * parses the data collection into an array
     *
     * @return \Browscap\Helper\Generator
     */
    public function parseCollection()
    {
        $collectionParser = new CollectionParser();
        $collectionParser->setDataCollection($this->collection);
        $this->collectionData = $collectionParser->parse();

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

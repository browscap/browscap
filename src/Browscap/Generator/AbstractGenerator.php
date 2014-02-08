<?php

namespace Browscap\Generator;

use Psr\Log\LoggerInterface;

abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var bool
     */
    protected $quoteStringProperties;

    /**
     * @var bool
     */
    protected $includeExtraProperties;

    /**
     * @var bool
     */
    protected $liteOnly;

    /**
     * @var array
     */
    protected $collectionData;

    /**
     * @var array
     */
    protected $comments = array();

    /**
     * @var array
     */
    protected $versionData = array();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * Set defaults
     */
    public function __construct()
    {
        $this->quoteStringProperties = false;
        $this->includeExtraProperties = true;
        $this->liteOnly = false;
    }

    /**
     * Set the data collection
     *
     * @param array $collectionData
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function setCollectionData(array $collectionData)
    {
        $this->collectionData = $collectionData;
        return $this;
    }

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return array
     */
    public function getCollectionData()
    {
        if (!isset($this->collectionData)) {
            throw new \LogicException("Data collection has not been set yet - call setDataCollection");
        }

        return $this->collectionData;
    }

    /**
     * @param string[] $comments
     *
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function setComments(array $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $versionData
     *
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function setVersionData(array $versionData)
    {
        $this->versionData = $versionData;

        return $this;
    }

    /**
     * @return array
     */
    public function getVersionData()
    {
        return $this->versionData;
    }

    /**
     * Set the options for generation
     *
     * @param boolean $quoteStringProperties
     * @param boolean $includeExtraProperties
     * @param boolean $liteOnly
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function setOptions($quoteStringProperties, $includeExtraProperties, $liteOnly)
    {
        $this->quoteStringProperties = (bool)$quoteStringProperties;
        $this->includeExtraProperties = (bool)$includeExtraProperties;
        $this->liteOnly = (bool)$liteOnly;

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return \Browscap\Generator\AbstractGenerator
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}

<?php

namespace Browscap\Generator;

/**
 * Interface GeneratorInterface
 *
 * @package Browscap\Generator
 */
interface GeneratorInterface
{
    /**
     * Set the data collection
     *
     * @param array $collectionData
     * @return \Browscap\Generator\GeneratorInterface
     */
    public function setCollectionData(array $collectionData);

    /**
     * Get the data collection
     *
     * @throws \LogicException
     * @return array
     */
    public function getCollectionData();

    /**
     * Generate and a string containing formatted Browscap data
     *
     * @return string
     */
    public function generate();
}

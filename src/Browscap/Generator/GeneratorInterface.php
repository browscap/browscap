<?php

namespace Browscap\Generator;

interface GeneratorInterface
{
    /**
     * Set the source data collection object
     *
     * @param \Browscap\Generator\DataCollection $collection
     */
    public function setDataCollection(DataCollection $collection);

    /**
     * Get the data collection
     *
     * @return \Browscap\Generator\DataCollection
     */
    public function getDataCollection();

    /**
     * Generate and a string containing formatted Browscap data
     *
     * @return string
     */
    public function generate();
}

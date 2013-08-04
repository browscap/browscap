<?php

namespace Browscap\Generator;

interface GeneratorInterface
{
    /**
     * @param \Browscap\Entity\UserAgent[] $userAgents
     */
    public function generate(array $userAgents);
}

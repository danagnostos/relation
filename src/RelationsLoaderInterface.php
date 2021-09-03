<?php

declare(strict_types=1);

namespace AlBundy\Relation;

interface RelationsLoaderInterface
{
    public function findAll(array $options): \Traversable;
}

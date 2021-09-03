<?php

declare(strict_types=1);

namespace AlBundy\Relation;

interface RelationInterface
{
    public function load(RelationshipsAwareInterface $model);
}

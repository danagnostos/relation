<?php

declare(strict_types=1);

namespace AlBundy\Relation;

interface RelationshipsAwareInterface
{
    public function getRelationContext(): ResultSetContextInterface;

    public function getRelationLoader(string $id): RelationsLoaderInterface;

    public function getRelationHandler(array $config): RelationInterface;
}

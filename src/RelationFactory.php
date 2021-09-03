<?php

declare(strict_types=1);

namespace AlBundy\Relation;

class RelationFactory
{
    public static function getInstance(array $config): RelationInterface
    {
        $relation = $config['type'];
        return new $relation($config);
    }
}

<?php

declare(strict_types=1);

namespace AlBundy\Relation;

interface ResultSetContextInterface extends \Traversable
{
    public function extractUnique(string $key): array;
}

<?php

declare(strict_types=1);

namespace AlBundy\Relation;

class Count extends AbstractRelation
{
    protected function getEmptyResult(): int
    {
        return 0;
    }

    protected function getPropertyValueForModel(string $key): int
    {
        if (empty($this->groupedChildren[$key])) {
            return $this->getEmptyResult();
        }

        return count($this->groupedChildren[$key]);
    }
}

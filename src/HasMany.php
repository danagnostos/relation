<?php

declare(strict_types=1);

namespace AlBundy\Relation;

class HasMany extends AbstractRelation
{
    const CONFIG_RESULT_SET = 'resultSet';

    /**
     * @var \Traversable|null
     */
    protected $emptyResult;

    public function getResultSetClass(): string
    {
        return $this->config[self::CONFIG_RESULT_SET] ?? \ArrayIterator::class;
    }

    protected function getEmptyResult(): \Traversable
    {
        if (!$this->emptyResult) {
            $class = $this->getResultSetClass();
            $this->emptyResult = new $class;
        }

        return clone $this->emptyResult;
    }

    protected function getPropertyValueForModel(string $key): \Traversable
    {
        if (empty($this->groupedChildren[$key])) {
            return $this->getEmptyResult();
        }

        $class = $this->getResultSetClass();
        return new $class($this->groupedChildren[$key]);
    }
}

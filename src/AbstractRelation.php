<?php

declare(strict_types=1);

namespace AlBundy\Relation;

abstract class AbstractRelation implements RelationInterface
{
    const CONFIG_KEY_PROPERTY = 'property';
    const CONFIG_KEY_TYPE = 'type';
    const CONFIG_KEY_ON = 'on';
    const CONFIG_KEY_MODEL = 'model';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $groupedChildren = [];

    /**
     * @var array
     */
    protected $parentOnKeys = [];

    /**
     * @var array
     */
    protected $childOnKeys = [];

    /**
     * @var string
     */
    protected $relationKeyMethod = 'getSingleRelationKey';

    abstract protected function getPropertyValueForModel(string $key);

    public function __construct($config)
    {
        $this->config = $config;
        $this->parentOnKeys = array_values($this->getOn());
        $this->childOnKeys = array_keys($this->getOn());

        if (count($this->getOn()) > 1) { // slower...
            $this->relationKeyMethod = 'getCompositeRelationKey';
        }
    }

    public function load(RelationshipsAwareInterface $model)
    {
        if (!$this->loaded) {
            $filter = $this->buildOnFilter($model);
            $children = $this->loadChildrenWithFilter($model, $filter);
            $this->groupChildren($children);
        }

        $key = $this->{$this->relationKeyMethod}($this->parentOnKeys, $model);

        return $this->getPropertyValueForModel($key);
    }

    protected function getGroupedChildren(): array
    {
        return $this->groupedChildren;
    }

    protected function groupChildren(\Traversable $children): void
    {
        foreach ($children as $child) {
            $key = $this->{$this->relationKeyMethod}($this->childOnKeys, $child);
            $this->groupedChildren[$key][] = $child;
        };
    }

    protected function getSingleRelationKey(array $properties, RelationshipsAwareInterface $model)
    {
        return (string) $model->{'get' . ucfirst($properties[0])}();
    }

    protected function getCompositeRelationKey(array $properties, RelationshipsAwareInterface $model): string
    {
        $keys = [];
        foreach ($properties as $property) {
            $keys[] = $model->{'get' . ucfirst($property)}();
        }

        return $this->concatCompositeKeys($keys);
    }

    protected function buildOnFilter(RelationshipsAwareInterface $model): array
    {
        $filter = [];
        foreach ($this->getOn() as $childModelProperty => $parentModelProperty) {
            $filter[$childModelProperty] = $this->getFilterValueFromContext($model, $parentModelProperty);
        }

        if (!$filter) {
            trigger_error('No relationship filter specified', E_USER_NOTICE);
        }

        return $filter;
    }

    protected function loadChildrenWithFilter(RelationshipsAwareInterface $model, array $filter): \Traversable
    {
        $this->loaded = true;
        return $this->getLoader($model)->findAll($filter);
    }

    protected function getFilterValueFromContext(RelationshipsAwareInterface $model, string $property)
    {
        $context = $model->getRelationContext();
        return $context->extractUnique($property);
    }

    protected function getChildModel(): string
    {
        return $this->config[self::CONFIG_KEY_MODEL];
    }

    protected function getOn(): array
    {
        return $this->config[self::CONFIG_KEY_ON];
    }

    protected function getProperty(): string
    {
        return $this->config[self::CONFIG_KEY_PROPERTY];
    }

    protected function concatCompositeKeys(array $ids): string
    {
        return implode('_', $ids);
    }

    protected function getLoader(RelationshipsAwareInterface $model): RelationsLoaderInterface
    {
        $class = $this->getChildModel();
        return $model->getRelationLoader($class);
    }
}

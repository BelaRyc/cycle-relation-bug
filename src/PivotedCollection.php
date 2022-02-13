<?php

declare(strict_types=1);

namespace Example;

use Cycle\ORM\Collection\Pivoted\PivotedCollectionInterface;
use Cycle\ORM\Collection\Pivoted\PivotedStorage;
use Illuminate\Support\Collection;
use SplObjectStorage;

class PivotedCollection extends Collection implements PivotedCollectionInterface
{
    /** @var SplObjectStorage<TEntity, TPivot> */
    protected SplObjectStorage $pivotContext;

  /**
   * @param PivotedStorage|array|null $elements
   * @param SplObjectStorage<TEntity, TPivot>|null $pivotData
   */
    final public function __construct(PivotedStorage|array $elements = null, SplObjectStorage $pivotData = null)
    {
        parent::__construct($elements);
        $this->pivotContext = $pivotData ?? new SplObjectStorage();
    }

    public function hasPivot(object $element): bool
    {
        return $this->pivotContext->offsetExists($element);
    }

    public function getPivot(object $element): mixed
    {
        return $this->pivotContext[$element] ?? null;
    }

    public function setPivot(object $element, mixed $pivot): void
    {
        $this->pivotContext[$element] = $pivot;
    }

    public function getPivotContext(): SplObjectStorage
    {
        return $this->pivotContext;
    }
}

<?php

declare(strict_types=1);

namespace Example\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Example\PivotedCollection;

#[Entity]
class Product
{
  public function __construct()
  {
    $this->attrs = new PivotedCollection();
  }

  #[Column(type: 'primary')]
  public int $id;

  #[Column(type: 'string')]
  public string $text;

  #[ManyToMany(target: Value::class, through: ProductAttr::class)]
  public PivotedCollection|array $attrs;
}

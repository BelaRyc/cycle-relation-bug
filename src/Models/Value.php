<?php

declare(strict_types=1);

namespace Example\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;

#[Entity]
class Value
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $text;

    #[ManyToMany(target: Product::class, through: ProductAttr::class)]
    public array $products;
}

<?php

declare(strict_types=1);

namespace Example\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;

#[Entity]
#[Index(columns: ['product_id', 'test_id'], unique: true)]
class ProductAttr
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer')]
    public int $test_id;

    public function __construct(int $test_id)
    {
        $this->test_id = $test_id;
    }
}

<?php
declare(strict_types=1);
include 'vendor/autoload.php';

use Cycle\ORM\EntityManager;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Example\Models\ProductAttr;
use Example\Models\Value;
use Example\Models\Product;
use Cycle\Database;
use Cycle\Database\Config;
use Cycle\Schema;
use Cycle\Annotated;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Example\PivotedCollection;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

$dbal = new Database\DatabaseManager(
    new Config\DatabaseConfig([
        'default' => 'default',
        'databases' => [
            'default' => ['connection' => 'sqlite']
        ],
        'connections' => [
            'sqlite' => new Config\SQLiteDriverConfig(
                connection: new Config\SQLite\MemoryConnectionConfig(),
                queryCache: true,
            ),
        ]
    ])
);

$finder = (new Finder())->files()->in([__DIR__ . '/src']); // __DIR__ here is folder with entities
$classLocator = new ClassLocator($finder);

// autoload annotations
AnnotationRegistry::registerLoader('class_exists');

$schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
    new Schema\Generator\ResetTables(),             // re-declared table schemas (remove columns)
    new Annotated\Embeddings($classLocator),        // register embeddable entities
    new Annotated\Entities($classLocator),          // register annotated entities
    new Annotated\TableInheritance(),               // register STI/JTI
    new Annotated\MergeColumns(),                   // add @Table column declarations
    new Schema\Generator\GenerateRelations(),       // generate entity relations
    new Schema\Generator\GenerateModifiers(),       // generate changes from schema modifiers
    new Schema\Generator\ValidateEntities(),        // make sure all entity schemas are correct
    new Schema\Generator\RenderTables(),            // declare table schemas
    new Schema\Generator\RenderRelations(),         // declare relation keys and indexes
    new Schema\Generator\RenderModifiers(),         // render all schema modifiers
    new Annotated\MergeIndexes(),                   // add @Table column declarations
    new Schema\Generator\SyncTables(),              // sync table changes to database
    new Schema\Generator\GenerateTypecast(),        // typecast non string columns
]);

$schema = new \Cycle\ORM\Schema($schema);

$orm = new ORM(new Factory($dbal), $schema);

$tr = new EntityManager($orm);

$p = new Product();
$p->id = 1;
$p->text = 'test';
$tr->persist($p);
$tr->run();

foreach (range(1,3) as $i){
    $v = new Value();
    $v->text = (string)$i;

    $p->attrs->add($v);
    $p->attrs->setPivot($v, new ProductAttr($i));
}

$tr->persist($p);
$tr->run();

$p = $orm->getRepository(Product::class)->select()->load('attrs')->wherePK(1)->fetchOne();

$p->attrs = new PivotedCollection();

/** @var Value $av3 */
$av3 = $orm->getRepository(Value::class)->findByPK(3);
$p->attrs->setPivot($v, new ProductAttr(3));

$p->attrs->add($av3);

$tr->persist($p);
$tr->run();
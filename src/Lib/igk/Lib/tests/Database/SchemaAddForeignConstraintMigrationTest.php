<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaAddForeignConstraintMigrationTest.php
// @desc: <addForeignConstraint> migration must register the constraint on the table definition
namespace IGK\Tests\Database;

use IGK\Controllers\BaseController;
use IGK\System\Database\SchemaBuilderMigration;
use IGK\System\Database\SchemaForeignConstraintInfo;
use IGK\System\Database\SchemaMigration;
use IGK\System\Database\SchemaMigrationInfo;
use IGK\Tests\BaseTestCase;

/**
 * test the in-memory upgrade of the `addForeignConstraint` migration
 * @package IGK\Tests\Database
 */
class SchemaAddForeignConstraintMigrationTest extends BaseTestCase
{
    const TABLE = 'tbfkmig_cars';

    /**
     * create the migration item from `<addForeignConstraint ...>` attributes
     */
    private function _createItem(BaseController $ctrl, array $attributes)
    {
        $node = igk_create_xmlnode('addForeignConstraint');
        $node->setAttributes($attributes);
        $mig = new SchemaBuilderMigration();
        $mig->controller = $ctrl;
        return $mig->addForeignConstraint()->load($node);
    }
    private function _createTables(): array
    {
        return [self::TABLE => new SchemaMigrationInfo()];
    }
    private function _attributes(array $override = []): array
    {
        return array_merge([
            'table' => '%prefix%cars',
            'on' => 'model_name,brand_id',
            'from' => '%prefix%models',
            'columns' => 'name,brand_id',
        ], $override);
    }
    public function test_do_upgrade_register_constraint()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();
        $this->_createItem($ctrl, $this->_attributes())->doUpgrade($tables, $ctrl);

        $list = $tables[self::TABLE]->foreignConstraint;
        $this->assertIsArray($list);
        $this->assertCount(1, $list);
        $this->assertInstanceOf(SchemaForeignConstraintInfo::class, $list[0]);
        $this->assertEquals('model_name,brand_id', $list[0]->on);
        $this->assertEquals('%prefix%models', $list[0]->from, 'table name is resolved when the query is sent');
        $this->assertEquals('name,brand_id', $list[0]->columns);
    }
    public function test_do_upgrade_keep_name()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();
        $this->_createItem($ctrl, $this->_attributes(['foreignKeyName' => 'fk_cars_models']))->doUpgrade($tables, $ctrl);
        $this->assertEquals('fk_cars_models', $tables[self::TABLE]->foreignConstraint[0]->foreignKeyName);
    }
    public function test_do_upgrade_add_to_existing_constraints()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();
        $first = new SchemaForeignConstraintInfo();
        $first->on = 'owner_id';
        $first->from = '%prefix%owners';
        $first->columns = 'id';
        // + | legacy format : a single object instead of a list
        $tables[self::TABLE]->foreignConstraint = $first;

        $this->_createItem($ctrl, $this->_attributes())->doUpgrade($tables, $ctrl);

        $list = $tables[self::TABLE]->foreignConstraint;
        $this->assertCount(2, $list);
        $this->assertSame($first, $list[0], 'existing constraint is kept');
        $this->assertEquals('model_name,brand_id', $list[1]->on);
    }
    public function test_do_upgrade_two_migrations_on_same_table()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();
        $this->_createItem($ctrl, $this->_attributes())->doUpgrade($tables, $ctrl);
        $this->_createItem($ctrl, $this->_attributes(['on' => 'dealer_id', 'from' => '%prefix%dealers', 'columns' => 'id']))
            ->doUpgrade($tables, $ctrl);
        $this->assertCount(2, $tables[self::TABLE]->foreignConstraint);
    }
    public function test_do_upgrade_unknown_table_is_ignored()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();
        $this->_createItem($ctrl, $this->_attributes(['table' => '%prefix%unknown']))->doUpgrade($tables, $ctrl);
        $this->assertEquals([self::TABLE], array_keys($tables));
        $this->assertEmpty($tables[self::TABLE]->foreignConstraint);
    }
    public function test_do_upgrade_column_count_mismatch_is_ignored()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();
        $this->_createItem($ctrl, $this->_attributes(['columns' => 'name']))->doUpgrade($tables, $ctrl);
        $this->assertEmpty($tables[self::TABLE]->foreignConstraint);
    }
    /**
     * load a data schema definition from xml text, like initdb does
     */
    private function _loadDefinition(string $xml, BaseController $ctrl)
    {
        $file = tempnam(sys_get_temp_dir(), 'igk_fk_schema_') . '.xml';
        file_put_contents($file, $xml);
        try {
            $node = \IGK\System\Html\HtmlReader::LoadFile($file);
            $this->assertNotNull($node, 'schema file not loaded');
            return \IGK\Database\DbSchemas::GetDefinition($node, $ctrl);
        } finally {
            @unlink($file);
        }
    }
    /**
     * many <ForeignConstraint> in one DataDefinition must be all kept (not only the last one)
     */
    public function test_schema_many_foreign_constraint_in_one_definition()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $def = $this->_loadDefinition(<<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<data-schemas version="1.0">
    <DataDefinition TableName="%prefix%cars" Prefix="car_">
        <Column clName="car_id" clType="Int" clAutoIncrement="True" clIsPrimary="True" />
        <Column clName="car_owner_id" clType="Int" />
        <Column clName="car_dealer_id" clType="Int" />
        <ForeignConstraint on="car_owner_id" from="%prefix%owners" columns="owner_id" />
        <ForeignConstraint on="car_dealer_id" from="%prefix%dealers" columns="dealer_id" foreignKeyName="fk_car_dealer" />
    </DataDefinition>
</data-schemas>
XML, $ctrl);

        $info = igk_getv($def->tables, self::TABLE);
        $this->assertNotNull($info, 'table definition not loaded: ' . implode(',', array_keys($def->tables ?? [])));
        $list = $info->foreignConstraint;
        $this->assertIsArray($list);
        $this->assertCount(2, $list, 'every ForeignConstraint of the table is kept');
        $this->assertEquals('car_owner_id', $list[0]->on);
        $this->assertEquals('%prefix%owners', $list[0]->from);
        $this->assertEquals('car_dealer_id', $list[1]->on);
        $this->assertEquals('fk_car_dealer', $list[1]->foreignKeyName);
    }
    /**
     * <ForeignConstraint> of the table + <addForeignConstraint> migration : both are kept
     */
    public function test_schema_foreign_constraint_and_migration_are_merged()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $def = $this->_loadDefinition(<<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<data-schemas version="1.0">
    <DataDefinition TableName="%prefix%cars" Prefix="car_">
        <Column clName="car_id" clType="Int" clAutoIncrement="True" clIsPrimary="True" />
        <Column clName="car_owner_id" clType="Int" />
        <ForeignConstraint on="car_owner_id" from="%prefix%owners" columns="owner_id" />
    </DataDefinition>
    <Migrations>
        <Migration>
            <addColumn table="%prefix%cars">
                <Column clName="car_model_id" clType="Int" />
            </addColumn>
        </Migration>
        <Migration>
            <addForeignConstraint table="%prefix%cars" on="car_model_id" from="%prefix%models" columns="model_id" />
        </Migration>
    </Migrations>
</data-schemas>
XML, $ctrl);

        $info = igk_getv($def->tables, self::TABLE);
        $this->assertNotNull($info, 'table definition not loaded');
        $this->assertCount(2, $info->foreignConstraint);
        $this->assertEquals('car_owner_id', $info->foreignConstraint[0]->on);
        $this->assertEquals('car_model_id', $info->foreignConstraint[1]->on);
        $this->assertArrayHasKey('car_model_id', $info->columnInfo, 'column added by the previous migration');
    }
    /**
     * full path : migration node loaded by SchemaMigration::upgrade
     */
    public function test_schema_migration_upgrade_register_constraint()
    {
        $ctrl = new DummyForeignConstraintMigrationController();
        $tables = $this->_createTables();

        $migration = igk_create_xmlnode('Migration');
        $node = $migration->add('addForeignConstraint');
        $node->setAttributes($this->_attributes());

        $schema = new SchemaMigration();
        $schema->upgrade([$migration], $tables, $ctrl);

        $this->assertCount(1, $tables[self::TABLE]->foreignConstraint);
        $this->assertEquals('name,brand_id', $tables[self::TABLE]->foreignConstraint[0]->columns);
    }
}

/**
 * dummy controller : table prefix `tbfkmig_`
 * @package IGK\Tests\Database
 */
class DummyForeignConstraintMigrationController extends BaseController
{
    protected function getEntryNameSpace()
    {
        return __NAMESPACE__;
    }
    public function __construct()
    {
        $this->getConfigs()->clDataTablePrefix = "tbfkmig_";
    }
}

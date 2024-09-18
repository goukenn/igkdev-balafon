<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaMigrationBuilder.php
// @desc: the migration schema builder
// @date: 20210422 06:39:05
namespace IGK\System\Database;

use Error;
use IGK\Database\DbSchemas;
use IGK\System\Database\SchemaBuilderHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use ReflectionException;

require_once IGK_LIB_CLASSES_DIR . '/System/Database/SchemaBuilderHelper.php';
/**
 * schema migration builder
 * @package IGK\System\Database
 */
class SchemaMigrationBuilder extends SchemaBuilderHelper
{

    private $is_migration;
    private $table_prefix;
    private $m_table;
    private $m_column;

    const SUPPORTED_TYPES = 'int|float|bigint|long|varchar|text|json|enum|double|date|datetime';

    /**
     * clean and start new edition 
     * @return static 
     */
    public function edit():static{
        $this->m_column = null;
        $this->m_table = null;
        return $this;
    }
    /**
     * use to add a reference chain column
     * @param string $name 
     * @return static 
     */
    public function column(string $name):static{
        if ($this->m_table){
            $this->m_column = $this->m_table->add('Column');
            $this->m_column['clName'] = $this->getPrefixTable($name);
            $this->m_column['clType'] = 'Int';
        } else {
            igk_die("must call addTable first");
        }
        return $this;
    }

    public function id():static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clIsPrimary'] = true;
        return $this;
    }
    
    public function text():static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        return $this->type('text');
    }
    /**
     * add a reference column
     * @param string $id 
     * @return static 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function ref(string $id = IGK_FD_ID):static{
       
        return $this->column($id)->id()->autoincrement();
    }
    /**
     * add generate colummn update time
     */
    public function updateTime(){
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $g = $this->m_table->add(IGK_GEN_COLUMS);
        $g['name'] = __FUNCTION__;
        $g['prefix'] = $this->getPrefixTable('');//$this->getPrefix();
        return $this; 
    }
    public function getPrefix():?string{
        if ($this->m_table){
            return $this->m_table['prefix'];
        }

    }

    /**
     * set auto increment
     * @return static 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function autoincrement():static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clAutoIncrement'] = true;
        return $this;
    }
    public function uniqueColumnMember(?int $index):static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clIsUniqueColumnMember'] = true;
        $this->m_column['clColumnMemberIndex'] = $index;
        return $this;
    }
    public function primary():static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clIsPrimary'] = true;
        return $this;
    }
    /**
     * set column as unique
     * @return static 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function unique():static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clIsPrimary'] = true;
        return $this;
    }
    public function notnull():static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clNotNull'] = true;
        return $this;
    }
    public function varchar(int $length):static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clType'] = 'VarChar';
        $this->m_column['clTypeLength'] = $length;
        return $this;
    }
    public function description(?string $description):static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        $this->m_column['clType'] = 'VarChar';
        $this->m_column['clDescription'] = $description;
        return $this;
    }
    public function type(string $type):static{
        if (!$this->m_column){
            igk_die('not in column edition');
        }
        if (!in_array(strtolower($type), $g = explode('|', self::SUPPORTED_TYPES))){
            igk_die(sprintf('not a valid type. %s', implode(',', $g)));
        }
        $this->m_column['clType'] = $type;
        return $this;
    }

    /**
     * add a table to the schema definition
     * @param string $name 
     * @param null|string $description 
     * @param null|array $options 
     * @return static 
     * @throws IGKException 
     */
    public function addTable(string $name, ?string $description=null, ?array $options = null):static{
        if ($this->is_migration){
            $this->edit();
            $v_table = $this->_output->add("createTable");
            $this->m_table = $v_table;
            $v_table['description'] = $description;
            $v_table['table'] = $name;
            $this->table_prefix = $v_table['Prefix'] = igk_getv($options,'prefix',  'cl');
            return $this;
        } 
        return $this->migration()->addTable($name, $description, $options);
    }
    public function dropTable(string $tablename){
        if ($this->is_migration){
            $this->edit();
            $v_table = $this->_output->add("dropTable"); 
            $v_table['table'] = $tablename; 
            return $this;
        } 
        return $this->migration()->dropTable($tablename);
    }
    public function getPrefixTable(string $table): string
    {
        return sprintf("%s%s", $this->table_prefix, $table);
    }
    protected function __construct()
    {
        
    }
    /**
     * create a schema builder node 
     * @param mixed $node 
     * @param mixed $schema 
     * @return static 
     */
    public static function Create($node, $schema)
    {
        $c = new static();
        $c->_output = $node;
        $c->_schema = $schema;
        $c->is_migration = 0;
        return $c;
    }
    /**
     * add migration node if not exists
     * @return $this|SchemaMigrationBuilder 
     */
    public function migration()
    {
        if ($this->is_migration) {
            return $this;
        }
        $n = $this->_output->add(DbSchemas::MIGRATION_TAG);
        $d = self::Create($n, $this->_schema);
        $d->is_migration = 1;
        return $d;
    }
    /**
     * add columns
     * @param mixed $table 
     * @param null|array|string $options 
     * @param mixed $after 
     * @return $this 
     */
    public function addColumn($table, ?array $options = null, $after = null)
    {
        if ($this->is_migration) {
            if (!empty($options)) {
                $b = $this->_output->add("addColumn");
                $b["table"] = $table;
                $b["after"] = $after;
                $this->_addcolumnAttributes($options, $b);
            }
            return $this;
        }
        $this->migration()->addColumn($table, $options, $after);
        return $this;
    }
    /**
     * 
     * @param mixed $table 
     * @param mixed $column 
     * @param array $options 
     * @return $this|void 
     */
    public function changeColumn($table, $column, array $options)
    {
        if ($this->is_migration) {
            $b = $this->_output->add("changeColumn");
            $b["table"] = $table;
            $b["column"] = $column;
            if (!empty($options)) {
                $this->_addcolumnAttributes($options, $b);
            }
            return $this;
        }
        $this->migration()->changeColumn($table, $column, $options);
    }
    public function renameColumn($table, $colname, $newname)
    {
        if ($this->is_migration) {
            $b = $this->_output->add("renameColumn");
            $b["table"] = $table;
            $b["column"] = $colname;
            $b["new_name"] = $newname;
            return $this;
        }
        $this->migration()->renameColumn($table, $colname, $newname);
        return $this;
    }
    /**
     * remove column
     * @param mixed $table 
     * @param mixed $colname 
     * @return $this 
     */
    public function removeColumn($table, $colname):static
    {
        if ($this->is_migration) {
            $b = $this->_output->add("removeColumn");
            $b["table"] = $table;
            $b["column"] = $colname;
            return $this;
        }
        $this->migration()->removeColumn($table, $colname);
        return $this;
    }
    /**
     * 
     * @param mixed $table 
     * @param string $colname 
     * @return static 
     */
    public function addIndex(string $table, $colname):static
    {
        if ($this->is_migration) {
            $b = $this->_output->add("addIndex");
            $b["table"] = $table;
            $b["columns"] = is_array($colname)? implode(',', array_filter($colname)) : $this->getPrefixTable($colname);
            return $this;
        }
        $this->migration()->addIndex($table, $colname);
        return $this;
    }

    /**
     * create table migration
     * @param string $table 
     * @return SchemaTableBuilder 
     */
    public function createTable(string $table): SchemaTableBuilder
    {
        if ($this->is_migration) {
            $b = $this->_output->add(__FUNCTION__);
            $b["table"] = $table;
            $s = SchemaTableBuilder::Create($b, $this->_schema);
            return $s;
        }
        return $this->migration()->createTable($table);
    }
    /**
     * delete table migration
     * @param string $table 
     * @return mixed 
     */
    public function deleteTable(string $table)
    {
        if ($this->is_migration) {
            $b = $this->_output->add(__FUNCTION__);
            $b["table"] = $table;
            return $b;
        }
        $this->migration()->deleteTable($table);
        return $this;
    }
}

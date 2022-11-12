<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaMigrationBuilder.php
// @desc: the migration schema builder
// @date: 20210422 06:39:05
namespace IGK\System\DataBase;

use IGK\Database\DbSchemas;

/**
 * schema migration builder
 * @package IGK\System\DataBase
 */
class SchemaMigrationBuilder extends SchemaBuilderHelper{
  
    private $is_migration;
    public static function Create($node, $schema){
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
    public function migration(){
        if ($this->is_migration){
            return $this;
        }
        $n = $this->_output->add(DbSchemas::MIGRATION_TAG);        
        $d = self::Create($n,$this->_schema);
        $d->is_migration = 1;
        return $d;
    }
    /**
     * add columns
     * @param mixed $table 
     * @param null|array $options 
     * @param mixed $after 
     * @return $this 
     */
    public function addColumn($table, ?array $options=null, $after=null){
        if ($this->is_migration){
            $b = $this->_output->add("addColumn");
            $b["table"] = $table;
            $b["after"] = $after;
            if (!empty($options)){
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
    public function changeColumn($table, $column, array $options){
        if ($this->is_migration){
            $b = $this->_output->add("changeColumn");
            $b["table"] = $table; 
            $b["column"] = $column; 
            if (!empty($options)){
                $this->_addcolumnAttributes($options, $b);
            } 
            return $this;
        }
        $this->migration()->changeColumn($table, $column, $options);
    }
    public function renameColumn($table, $colname, $newname){
        if ($this->is_migration){
            $b = $this->_output->add("renameColumn");
            $b["table"] = $table;
            $b["column"] = $colname;
            $b["new_name"] = $newname;
            return $this;
        }
        $this->migration()->renameColumn($table, $colname, $newname);
        return $this;
    }
    public function removeColumn($table, $colname){
        if ($this->is_migration){
            $b = $this->_output->add("removeColumn");
            $b["table"] = $table;
            $b["column"] = $colname; 
            return $this;
        }
        $this->migration()->removeColumn($table, $colname);
        return $this;
    }

    /**
     * create table migration
     * @param string $table 
     * @return SchemaTableBuilder 
     */
    public function createTable(string $table): SchemaTableBuilder{
        if ($this->is_migration){
            $b = $this->_output->add(__FUNCTION__);
            $b["table"]= $table;
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
    public function deleteTable(string $table){
        if ($this->is_migration){
            $b = $this->_output->add(__FUNCTION__);
            $b["table"]= $table;
            return $b;
        }
        $this->migration()->deleteTable($table);
        return $this;
    }
}
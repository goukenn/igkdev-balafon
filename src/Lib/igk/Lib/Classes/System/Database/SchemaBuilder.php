<?php
namespace IGK\System\Database;

use DbSchemas;

/**
 * represent a schema builder class
 * @package IGK\System\Database
 */
class SchemaBuilder{
    private $_output;
    private $_migrations;
    public function __construct(){
        $this->_output = igk_create_xmlnode(IGK_SCHEMA_TAGNAME);
    }
    public function render($options=null){
        return $this->_output->render($options);
    }
    public function createTable(string $table, $desc=null){
        $n = $this->_output->add(DbSchemas::DATA_DEFINITION);
        $n["TableName"] = $table;
        $n["Description"] = $desc;
        return SchemaTableBuilder::Create($n, $this);
    }
    public function migrations(){
        if ($this->_migrations==null){

            $n =  $this->_output->add(DbSchemas::MIGRATIONS_TAG);
            $this->_migrations = SchemaMigrationBuilder::Create($n , $this);
            
        }
        return $this->_migrations;
    }
}
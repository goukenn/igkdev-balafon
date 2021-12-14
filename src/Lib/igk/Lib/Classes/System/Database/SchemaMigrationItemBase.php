<?php

namespace IGK\System\Database;

use IGKDbSchemas;
use IGKException;
use IGKHtmlCommentItem;

/** @package  */
abstract class SchemaMigrationItemBase{
    private $migration;
    private $m_raw;
    protected $fill_properties;

    public function __get($name){
        return igk_getv($this->m_raw, $name);
    }
    public function getMigration(){
        return $this->migration;
    }
    function __construct(SchemaBuilderMigration $migration){
        $this->migration = $migration;
    }
    public function load($node){  
        $this->m_raw = igk_get_robjs($this->fill_properties, 0, $node->getAttributes()->to_array());
        $tab = array_filter($node->getChilds()->to_array(), function($v){
            return !($v instanceof IGKHtmlCommentItem);
        }); 
        $this->loadChilds($tab);
        return $this;
    }
    protected function loadChilds($childs){

    }
    /**
     * up the migration
     * @return void 
     */
    public function up(){

    }
    /**
     * down migration item
     * @return void 
     */
    public function down(){

    }
}
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaMigrationItemBase.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

use IGK\System\Html\Dom\HtmlCommentNode;
use DbSchemas;
use Google\Service\Vision\Property;
use IGKException;
use IGKHtmlCommentItem;

/** @package  */
abstract class SchemaMigrationItemBase{
    private $m_migration;
    protected $raw;
    protected $fill_properties; 

    /**
     * 
     * @return ?ISchemaMigrationInfoListener 
     */
    public function getMigrationInfoListener(){
        if ($mig = $this->getMigration()){
            return $mig->migrationListener;
        }
        return null;

    }
    public function __isset($name){
        return  property_exists($this->raw, $name);
    }
    public function __get($name){
        return igk_getv($this->raw, $name);
    }
    public function getMigration(){
        return $this->m_migration;
    }
    function __construct(SchemaBuilderMigration $migration){
        $this->m_migration = $migration;
    }
    public function load($node){  
        $this->raw = igk_get_robjs($this->fill_properties, 0, $node->getAttributes()->to_array());
        $this->checkRequirement();
        $tab = array_filter($node->getChilds()->to_array(), function($v){
            return !($v instanceof HtmlCommentNode);
        }); 
        $this->loadChilds($tab);
        return $this;
    }
    /**
     * check for requiement 
     * @return bool|mixed
     * @throws \Exception on requirement failed
     */
    protected function checkRequirement(){
    }
    /**
     * load children definition
     * @param mixed $childs 
     * @return void 
     */
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
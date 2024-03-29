<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaBuilder.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Database;

use ArrayAccess;
use Exception;
use IGK\Database\DbSchemas;
use IGKException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlCommentNode;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
 * represent a schema builder class
 * @package IGK\System\Database
 */
class SchemaBuilder implements ArrayAccess{
    use ArrayAccessSelfTrait;
    private $_output;
    private $_migrations;
    public function __construct(){
        $this->_output = igk_create_xmlnode(IGK_SCHEMA_TAGNAME);
    }
    /**
     * render output 
     * @param mixed $options 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     */
    public function render($options=null){
        return rtrim($this->_output->render($options));
    }
    /**
     * create a table
     * @param string $table 
     * @param ?string $desc 
     * @return SchemaTableBuilder 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function createTable(string $table, ?string $desc=null){
        $n = $this->_output->add(DbSchemas::DATA_DEFINITION);
        $n["TableName"] = $table;
        $n["Description"] = $desc;
        return SchemaTableBuilder::Create($n, $this);
    }
    /**
     * get migrations file
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function migrations(){
        if ($this->_migrations==null){
            $n =  $this->_output->add(DbSchemas::MIGRATIONS_TAG);
            $this->_migrations = SchemaMigrationBuilder::Create($n , $this);            
        }
        return $this->_migrations;
    }
    /**
     * add string comment
     * @param string $comment 
     * @return HtmlCommentNode 
     */
    public function comment(?string $comment=null): HtmlCommentNode{
        $n = new HtmlCommentNode();
        $n->setContent($comment);
        $this->_output->add($n);
        return $n;
    }
    // 
    protected function _access_OffsetSet($n,$v){
        $this->_output[$n] = $v;
    }
    //
    protected function _access_OffsetGet($n){
        return $this->_output[$n];
    }
    //
    protected function _access_offsetExists($n,$v){
        return isset($this->_output[$n]);
    }
    //
    protected function _access_OffsetUnset($n,$v){
        unset($this->_output[$n]);
    }

}
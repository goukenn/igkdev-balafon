<?php
// @author: C.A.D. BONDJE DOUE
// @file: Factory.php
// @date: 20231224 14:26:48
namespace IGK\Database\SchemaBuilder\Entities;
use IGK\Resources\R;
/**
* Entity factory helper 
* @package IGK\Database\SchemaBuilder\Entities
*/
abstract class Factory implements IDiagramVisitorEntity{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $_table;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $_mig;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $_controller;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $_props;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $_schema;

    /**
    * auto generate doc.
    */
    public function up(){
        $this->updateSchema($this->_schema, 'up');
    }

    /**
    * auto generate doc.
    */
    public function down(){
        $this->updateSchema($this->_schema, 'down');
    }
    /**
     * create entity  from migration type
     * @param string $migration_type 
     * @return null|IDiagramVisitorEntity 
     */

    public static function Create(string $migration_type) : ?IDiagramVisitorEntity{
        $v_ns = __NAMESPACE__."\\".ucfirst($migration_type)."Entity";
        if (class_exists($v_ns)){
            $cl = new $v_ns;    
            return $cl;
        }
        return null;
    }
}